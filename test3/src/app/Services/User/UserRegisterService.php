<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRegisterService
{
    public function createUser(string $login, string $password): User
    {
        return User::query()->create([
            'login' => $login,
            'password' => Hash::make($password),
        ]);
    }
}
