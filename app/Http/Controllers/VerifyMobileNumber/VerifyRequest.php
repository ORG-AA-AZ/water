<?php

namespace App\Http\Controllers\VerifyMobileNumber;

use Illuminate\Foundation\Http\FormRequest;

class VerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'max:15'],
            'code' => ['required', 'string', 'max:6'],
        ];
    }
}
