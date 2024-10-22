<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Marketplace extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'mobile',
        'national_id',
        'password',
        'latitude',
        'longitude',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
