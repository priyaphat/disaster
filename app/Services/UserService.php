<?php

namespace App\Services;

use App\Enums\RegistrationType;
use App\Events\AdminRegistered;
use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function getList(): Collection
    {
        return User::latest()->get();
    }

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return User::filter($filters)->with(['prefix', 'department', 'position', 'area'])->latest()->paginate($perPage);
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
        $user->delete();
    }

    public static function generatePassword(): string
    {
        return Str::password(12);
    }
}
