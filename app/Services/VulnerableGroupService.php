<?php

namespace App\Services;


use App\Models\VulnerableGroup as Group;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VulnerableGroupService
{
    public function getList(array $filters = [], bool $paginate = false, int $perPage = 10)
    {
        $query = Group::select('id', 'name')->latest();

        if (!empty($filters['search'])) {
            $query->whereAny(['name'], 'LIKE', '%' . $filters['search'] . '%');
        }

        if (!$paginate) {
            return $query->get();
        } else {
            return $query->paginate($perPage);
        }
    }

    public function selectableGroups(array $filters = [])
    {
        $noGroup = [
            [
                'id' => null,
                'name' => __('data.no_vulnerable')
            ]
        ];
        $data = $this->getList($filters)->toArray();
        return array_merge($noGroup, $data);
    }

    public function getById(int $id): Group
    {
        return Group::findOrFail($id);
    }

    public function create(array $data): Group
    {
        $group = Group::create($data);
        return $group;
    }

    public function update(Group $group, array $data): Group
    {
        $group->update($data);
        $group->refresh();
        return $group;
    }

    /**
     * Check vulnerable is used.
     */
    public function isUsed(Group $group): bool
    {
        return $group->populations()->exists();
    }

    /**
     * Delete a vulnerable.
     */
    public function delete(Group $group): bool
    {
        if ($this->isUsed($group)) {
            throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        }

        return $group->delete();
    }
}
