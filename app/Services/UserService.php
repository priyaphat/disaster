<?php

namespace App\Services;

use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data)
    {
        // Validate user data and send welcome email.
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        // event(new UserRegistered($user));
        return $user;
    }
}
