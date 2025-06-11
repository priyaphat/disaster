<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Models\UserApproval as Approval;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserService
{
    protected $relations = ['prefix', 'department', 'position', 'area', 'approval', 'member', 'image'];
    public function baseQuery($filters)
    {
        return User::filter($filters)->with($this->relations);
    }

    public function getList(array $filters = []): Collection
    {
        return $this->baseQuery($filters)->latest();
    }

    /**
     * Get all users with approve or pending status.
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $data = $this->baseQuery($filters)->latest()->paginate($perPage);
        $paginate = $data->setCollection(UserResource::collection($data->items())->collection);
        return $paginate;
    }

    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['registration_type'] = RegistrationType::SELF;
        $user = User::create($data);

        // Send a password via email
        userRegistered::dispatch($user);
        return $user;
    }

    public function create(array $data): User
    {
        $password = self::generatePassword();
        $data['password'] = Hash::make($password);
        $data['registration_type'] = RegistrationType::ADMIN;
        $user = User::create($data);

        // Send a password via email
        AdminRegistered::dispatch($user, $password);
        return $user;
    }

    public function show(User $user)
    {
        $user->load($this->relations);
        return new UserResource($user);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public static function generatePassword(): string
    {
        return Str::password(12);
    }

    /**
     * Update a Approval.
     */
    public function approve(array $data): Approval
    {
        $approval = Approval::where('user_id', $data['user_id'])->first();
        $user = $this->getById($data['user_id']);

        if (!$approval || !$user) {
            throw new ModelNotFoundException();
        }

        if ($approval->approved_at) {
            throw new BadRequestException();
        }

        // If reject : Delete user
        if ($data['is_approved'] == ApprovalStatus::REJECT->value) {
            $this->delete($user);
            $this->deleteApprovalData($approval);
        } else {
            $approval->update($data);
        }

        return $approval;
    }

    /**
     * Delete a Approval.
     */
    public function deleteApprovalData(Approval $approval): bool
    {
        return $approval->delete();
    }
}
