<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:10', 'unique:marketplaces,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'national_id' => ['required', 'string', 'min:8', 'unique:marketplaces,national_id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages()
    {
        return [
            'mobile.unique' => 'The mobile number has already been taken.',
            'national_id.unique' => 'The national ID has already been taken.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
