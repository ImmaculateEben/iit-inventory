<?php

namespace App\Enums;

enum ItemType: string
{
    case Consumable = 'consumable';
    case Asset = 'asset';

    public function label(): string
    {
        return match ($this) {
            self::Consumable => 'Consumable',
            self::Asset => 'Asset',
        };
    }
}
