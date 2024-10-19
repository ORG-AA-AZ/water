<?php

namespace App\Resources;

use App\Models\Marketplace;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Marketplace */
class MarketplaceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'national_id' => $this->national_id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'location' => $this->location,
        ];
    }
}
