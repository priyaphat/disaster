<?php

namespace App\Services;

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
        return User::latest()->all();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return User::latest()->paginate($perPage);
    }

    public function getById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return $user;
    }

    public function create(array $data): User
    {
        $password = self::generatePassword();
        $data['password'] = Hash::make($password);
        $data['created_by_admin'] = true;
        $user = User::create($data);
        // send a password via emai
        UserRegistered::dispatch($user, $password);
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
