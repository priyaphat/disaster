<?php

namespace App\Services;

use App\Models\UserApproval as Approval;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class UserApprovalService
{
    /**
     * Get paginated Approval.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Approval::query();
        return $query->with('user')->latest()->paginate($perPage);
    }

    /**
     * Update a Approval.
     */
    public function update(Approval $approval, array $data): Approval
    {
        $approval->fill($data);
        if($approval->isDirty('is_approved')){
            $data['approved_by'] =  Auth::id();
            $data['approved_at'] = now();
        }
        $approval->update($data);
        return $approval;
    }

    /**
     * Delete a Approval.
     */
    public function delete(Approval $approval): bool
    {
        return $approval->delete();
    }
}
