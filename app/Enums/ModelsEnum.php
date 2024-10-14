<?php

namespace App\Enums;

use App\Models\User;
use App\Models\Marketplace;

enum ModelsEnum: string
{
    case User = User::class;
    case Marketplace = Marketplace::class;

    public function getModel(): string
    {
        return match($this) {
            self::User => User::class,
            self::Marketplace => Marketplace::class,
        };
    }
}
