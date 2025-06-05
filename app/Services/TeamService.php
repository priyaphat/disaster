<?php

namespace App\Services;

use App\Http\Resources\TeamCollection;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    /**
     * Get all Teames with filter and limit number.
     */
    public function getList(array $filters = [], ?int $limit = null): Collection
    {
        $query = Team::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        // Orders by 'created_at' DESC if ASC use oldest
        $query->latest();

        // limit the number of results
        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    /**
     * Get paginated Team.
     */
    public function paginate(array $filters = [], int $perPage = 10)
    {
        $query = Team::query();
        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }
        $data = $query->with('leader')->latest()->paginate(10);
        $paginate = $data->setCollection(TeamResource::collection($data->items())->collection);
        return  $paginate;
    }

    /**
     * Get a Team by its id.
     */
    public function getById(int $id): Team
    {
        return Team::with('members')->findOrFail($id);
    }

    /**
     * Create a Team.
     */
    public function create(array $data): Team
    {
        $team = Team::create($data);
        // dd($data['members']);
        $team->members()->createMany($data['members']);
        return $team;
    }

    /**
     * Show a Team.
     */
    public function show(Team $team)
    {
        // Eager load members relationship
        $team->load(['leader', 'allUsers']);
        return new TeamResource($team);
    }

    /**
     * Update a Team.
     */
    public function update(Team $team, array $data): Team
    {
        $team->update($data);
        $team->syncMembers($data['members']);
        return $team;
    }

    /**
     * Delete a Team.
     */
    public function delete(Team $team): bool
    {
        return $team->delete();
    }

    /**
     * Create a Team.
     */
    public function createMember(array $data): TeamMember
    {
        return TeamMember::create($data);
    }

    /**
     * Update a member.
     */
    public function updateMember(TeamMember $team, array $data): TeamMember
    {
        $team->update($data);
        return $team;
    }


    /**
     * Delete a member.
     */
    public function deleteMember(TeamMember $team, array $data): TeamMember
    {
        $team->update($data);
        return $team;
    }
}
