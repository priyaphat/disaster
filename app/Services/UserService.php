<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

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
        // You might want to assign a default role, send a welcome email, etc.
        // event(new UserCreated($user));
        return $user;
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        // You might want to assign a default role, send a welcome email, etc.
        // event(new UserCreated($user));
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
}
