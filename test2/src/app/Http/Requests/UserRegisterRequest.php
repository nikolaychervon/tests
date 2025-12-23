<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * @return array{string, string}
     */
    public function rules(): array
    {
        return [
            'nickname' => 'required|string',
            'avatar' => 'required|image|max:2048',
        ];
    }
}
