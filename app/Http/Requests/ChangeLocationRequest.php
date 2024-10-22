<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeLocationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'max:10', 'unique:marketplaces,mobile'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
