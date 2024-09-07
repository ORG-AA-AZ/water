<?php

namespace App\Http\Controllers\User;

use Illuminate\Foundation\Http\FormRequest;

class NewVerifyCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string'],
        ];
    }
}
