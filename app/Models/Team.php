<?php

namespace App\Models;

use App\Filters\TeamFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;

class Team extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name',
        'sequence'
        // 'leader_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function log()
    {
        return $this->morphOne(AppointmentLog::class, 'parentable');
    }

    public function team_members()
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id');
    }

    public function leader()
    {
        return $this->hasOneThrough(
            User::class,   // Final model
            TeamMember::class,   // Intermediate model
            'team_id',  // Foreign key on users table
            'id',     // Foreign key on posts table
            'id',          // Local key on team table
            'user_id'           // Local key on users table
        )->where('team_members.team_position', 1);
    }


    public function members()
    {
        return $this->hasManyThrough(
            User::class,   // Final model
            TeamMember::class,   // Intermediate model
            'team_id',  // Foreign key on users table
            'id',     // Foreign key on posts table
            'id',          // Local key on team table
            'user_id'           // Local key on users table
        )->where('team_members.team_position', 2)
            ->orderBy('team_members.sequence', 'ASC');
    }

    public function allUsers()
    {
        return $this->hasManyThrough(
            User::class,   // Final model
            TeamMember::class,   // Intermediate model
            'team_id',  // Foreign key on users table
            'id',     // Foreign key on posts table
            'id',          // Local key on team table
            'user_id'           // Local key on users table
        )->orderBy('team_members.team_position')
            ->select('users.*', 'team_members.team_position')
            ->orderBy('team_members.sequence', 'ASC');
    }

    public function totalMembers()
    {
        return $this->hasManyThrough(
            User::class,   // Final model
            TeamMember::class,   // Intermediate model
            'team_id',  // Foreign key on users table
            'id',     // Foreign key on posts table
            'id',          // Local key on team table
            'user_id'           // Local key on users table
        )->orderBy('team_members.team_position')
            ->count();
    }
    // public function members(): HasMany
    // {
    //     return $this->hasMany(TeamMember::class, 'team_id', 'id');
    // }

    public function syncMembers(array $members)
    {
        // Get user_id and position from database and new request
        $oldMembers = $this->team_members()->orderBy('sequence')->get();

        $existingMembers = $oldMembers->pluck('user_id');
        $newMembers = collect($members);

        // Get Keys from two collections
        // $newKeys = $newMembers->keys()->values();
        // $existingKeys = $existingMembers->keys()->values();

        // $oldMemberIds = $oldMembers->keyBy('user_id')->keys();
        $newMemberIds = $newMembers->pluck('user_id')->values()->all();

        // สมาชิกที่ถูกลบ
        $leaveMembers = $existingMembers->diff($newMemberIds);
        if ($leaveMembers->isNotEmpty()) {
            $this->team_members->whereIn('user_id', $leaveMembers)->each->delete();
        }

        // Loop over input to preserve order and create or update
        foreach ($newMembers as $member) {
            // Check if the position exists. If it does, update the user_id; otherwise, create a new team member with the given user_id and position
            $teamMember = $this->team_members()->where(['user_id' => $member['user_id']])->first();
            if ($teamMember) {
                $teamMember->sequence = $member['sequence'];
                $teamMember->team_position = $member['team_position'];
                $teamMember->save();
            } else {
                $this->team_members()->create(['user_id' => $member['user_id'], 'sequence' => $member['sequence'], 'team_position' => $member['team_position']]);
            }
        }
    }

    public function upcomingAppointments(): HasMany
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $this->hasMany(Appointment::class, 'team_id', 'id')
            ->whereRaw("STR_TO_DATE(CONCAT(appointments.appointment_date, ' ', appointments.end_time), '%Y-%m-%d %H:%i:%s') > ?", [$now]);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new TeamFilter($filters))->apply($query);
    }

    public function histories()
    {
        return $this->morphMany(TransferHistory::class, 'childable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('teams')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
