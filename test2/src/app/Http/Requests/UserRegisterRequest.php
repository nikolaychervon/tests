<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nickname' => 'required|string',
            'avatar' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
