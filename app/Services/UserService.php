<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSelectResource;
use App\Mail\ApproveEmail;
use App\Mail\RejectEmail;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Models\UserApproval as Approval;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserService
{
    protected $relations = ['prefix', 'department', 'position', 'area', 'approval', 'member', 'image', 'staff'];
    public function baseQuery($filters)
    {
        return User::filter($filters)->with($this->relations);
    }


    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        $filters['approve'] = isset($filters['approve']) ? $filters['approve'] : 'success';
        if(isset($filters['available'])){
            $filters['available_staff'] = true;
            $filters['available_member'] = true;
        }

        if (!$paginate) {
            return new UserCollection($this->baseQuery($filters)->latest()->get());
        } else {
            $data = $this->baseQuery($filters)->latest()->paginate($perPage);
            $paginate = $data->setCollection(UserResource::collection($data->items())->collection);
            return $paginate;
        }
    }

    public function selectableUsers(array $filters = [])
    {
        $filters['approve'] = 'success';
        $filters['available_staff'] = true;
        $filters['available_member'] = true;
        $data = $this->baseQuery($filters)
            ->select('id', 'prefix_id', 'firstname', 'lastname')
            ->latest()->get();
        return UserSelectResource::collection($data);
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
        $user->approval->delete();
        $user->delete();
    }

    public static function generatePassword(): string
    {
        return Str::password(12);
    }

    /**
     * Update a Approval.
     */
    public function approve(array $data)
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
            $user->image->delete();
            $this->delete($user);
            Mail::to($user->email)->send(new RejectEmail($user));
            return __('success.approval.reject');
        } else {
            $approval->update($data);
            Mail::to($user->email)->send(new ApproveEmail($user));
            return __('success.approval.approve');
        }
    }

    /**
     * Delete a Approval.
     */
    public function deleteApprovalData(Approval $approval): bool
    {
        return $approval->delete();
    }
}
