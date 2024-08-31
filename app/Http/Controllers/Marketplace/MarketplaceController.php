<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use App\Models\User;
use App\Resources\MarketplaceResource;
use Illuminate\Support\Facades\Hash;

class MarketplaceController extends Controller
{
    public function store(MarketplaceRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->password),
        ]);

        $marketplace = Marketplace::create([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'user_id' => $user->id,
            'password' => Hash::make($request->password),
        ]);

        return new MarketplaceResource($marketplace);
    }
}
