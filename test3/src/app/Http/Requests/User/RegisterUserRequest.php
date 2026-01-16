<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Auth",
    title: "Auth Data",
    description: "Data for authorization",
    required: ["login", "password"],
    properties: [
        new OA\Property(property: "login", description: "Логин", type: "string", example: "testuser"),
        new OA\Property(property: "password", description: "Пароль", type: "string", format: "password", example: "password")
    ]
)]
class RegisterUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                'max:20',
                'unique:users,login',
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
