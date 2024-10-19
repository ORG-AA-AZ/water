<?php

namespace App\Http\Controllers\VerifyMobileNumber;

use Illuminate\Foundation\Http\FormRequest;

class NewVerifyCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string'],
        ];
    }
}
