<?php

namespace App\Services\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserLoginService
{
    public function login(string $login, string $password): Authenticatable
    {
        if (!Auth::attempt(['login' => $login, 'password' => $password])) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        return Auth::user();
    }
}
