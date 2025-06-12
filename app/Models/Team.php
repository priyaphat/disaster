<?php

namespace App\Models;

use App\Casts\ThaiDateTimeFormat;
use App\Filters\TeamFilter;
use App\Http\Traits\CreatedUpdatedBy;
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
            'created_at' => ThaiDateTimeFormat::class,
            'updated_at' => ThaiDateTimeFormat::class,
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
        ->select('users.*','team_members.team_position');
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
        // Bad solution for update team members
        // Delete all members and add new datas
        // $existingMembers =$this->members()->delete();
        // $memberArray = collect($members)->map(function ($value, $index) {
        //     return [
        //         'user_id' => $value, 
        //         'position' => $index];
        // });
        //$this->members()->createMany($memberArray);

        // // Good solution for update team members
        // $newMembers = collect($members);
        // $existingMembers =$this->members()->get()->pluck('user_id', 'position');

        // // To Delete
        // $deleteArray = $existingMembers->diffAssoc($newMembers);
        // // To Add
        // $addArray = $newMembers->diffAssoc($existingMembers);

        // if ($deleteArray->isNotEmpty()) {
        //    $this->members()->whereIn('user_id', $deleteArray)->delete();
        // }

        // foreach ($addArray as $position => $userId) {
        //     $checkExist =$this->members()->where(['position' => $position])->exists();
        //     if ($checkExist) {
        //        $this->members()->where(['position' => $position])->update(['user_id' => $userId]);
        //     } else {
        //        $this->members()->create(['user_id' => $userId, 'position' => $position]);
        //     }
        // }

        // Get user_id and position from database and new request
        $existingMembers = $this->members()->get()->pluck('user_id', 'sequence');
        $newMembers = collect($members);

        // Get Keys from two collections
        $newKeys = $newMembers->keys()->values();
        $existingKeys = $existingMembers->keys()->values();


        // Check members was changed
        if ($existingMembers != $newMembers) {

            // Find key that doesn't exist from new request
            $deleteKeyArray = $existingKeys->diffAssoc($newKeys);
            // dd($newMembers);
            // Remove any positions that are missing from the current input
            if ($deleteKeyArray->isNotEmpty()) {
                return $this->members()->whereIn('sequence', $deleteKeyArray)->delete();
            }

            // Loop over input to preserve order and create or update
            foreach ($newMembers as $member) {
                // Check if the position exists. If it does, update the user_id; otherwise, create a new team member with the given user_id and position
                $checkExist = $this->members()->where(['sequence' => $member['sequence']])->exists();
                if ($checkExist) {
                    $this->members()->where(['sequence' => $member['sequence']])->update(['user_id' => $member['user']]);
                } else {
                    $this->members()->create(['user_id' => $member['user_id'], 'sequence' => $member['sequence'], 'position' => $member['position']]);
                }
            }
        }
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new TeamFilter($filters))->apply($query);
    }
}
