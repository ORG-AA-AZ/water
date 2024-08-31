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
        'user_id',
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
