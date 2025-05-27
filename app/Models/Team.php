<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name',
        'leader_id'
    ];

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'team_id', 'id');
    }

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
        $existingMembers = $this->members()->get()->pluck('user_id', 'position');
        $newMembers = collect($members);

        // Get Keys from two collections
        $newKeys = $newMembers->keys()->values();
        $existingKeys = $existingMembers->keys()->values();

        // Check members was changed
        if ($existingMembers != $newMembers) {
            
            // Find key that doesn't exist from new request
            $deleteKeyArray = $existingKeys->diffAssoc($newKeys);

            // Remove any positions that are missing from the current input
            if ($deleteKeyArray->isNotEmpty()) {
                return $this->members()->whereIn('position', $deleteKeyArray)->delete();
            }

            // Loop over input to preserve order and create or update
            foreach ($newMembers as $position => $userId) {
                // Check if the position exists. If it does, update the user_id; otherwise, create a new team member with the given user_id and position
                $checkExist = $this->members()->where(['position' => $position])->exists();
                if ($checkExist) {
                    $this->members()->where(['position' => $position])->update(['user_id' => $userId]);
                } else {
                    $this->members()->create(['user_id' => $userId, 'position' => $position]);
                }
            }
        }
    }
}
