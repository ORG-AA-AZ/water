<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mobile',
        'national_id',
        'password',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
