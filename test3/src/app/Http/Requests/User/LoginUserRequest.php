<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class LoginUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                'max:20',
                'exists:users,login',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'password' => 'required|string|min:8',
        ];
    }

    public function getLogin(): string
    {
        return $this->get('login');
    }

    public function getPassword(): string
    {
        return $this->get('password');
    }
}
