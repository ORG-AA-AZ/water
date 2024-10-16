<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:10', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'mobile.unique' => 'The mobile number has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
