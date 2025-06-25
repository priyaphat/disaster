<?php

namespace App\Models;

use App\Casts\ThaiDateTimeFormat;
use App\Filters\TeamFilter;
use App\Http\Traits\CreatedUpdatedBy;
use App\Services\TransferHistoryService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        )->where('team_members.team_position', 2);
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
            ->select('users.*', 'team_members.team_position');
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

        $existingMembers = $oldMembers->pluck('user_id', 'sequence');
        $newMembers = collect($members);

        // Get Keys from two collections
        // $newKeys = $newMembers->keys()->values();
        // $existingKeys = $existingMembers->keys()->values();

        $oldMemberIds = $oldMembers->keyBy('user_id')->keys();
        $newMemberIds = $newMembers->keyBy('user_id')->keys();


        // สมาชิกที่ถูกลบ
        $leaveMembers = $oldMemberIds->diffKeys($newMemberIds);
        if ($leaveMembers->isNotEmpty()) {
            $this->team_members->whereIn('user_id', $leaveMembers)->each->delete();
        }

        // Check members was changed
        if ($existingMembers != $newMembers) {

            // Loop over input to preserve order and create or update
            foreach ($newMembers as $member) {
                // Check if the position exists. If it does, update the user_id; otherwise, create a new team member with the given user_id and position
                $checkExist = $this->team_members()->where(['sequence' => $member['sequence']])->exists();
                if ($checkExist) {
                    $this->team_members->where(['sequence' => $member['sequence']])->each->update(['user_id' => $member['user_id'], 'team_position' => $member['team_position']]);
                } else {
                    $this->team_members()->create(['user_id' => $member['user_id'], 'sequence' => $member['sequence'], 'team_position' => $member['team_position']]);
                }
            }
        }
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new TeamFilter($filters))->apply($query);
    }

    public function histories()
    {
        return $this->morphMany(TransferHistory::class, 'childable');
    }
}
