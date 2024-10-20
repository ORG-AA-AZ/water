<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Foundation\Http\FormRequest;

class MarketplaceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:10', 'unique:marketplaces,mobile'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'national_id' => ['required', 'string', 'min:8', 'unique:marketplaces,national_id'],
            'location' => ['required', 'string'],
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
