<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:10', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
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
