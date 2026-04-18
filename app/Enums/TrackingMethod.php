<?php

namespace App\Enums;

enum TrackingMethod: string
{
    case Quantity = 'quantity';
    case Individual = 'individual';

    public function label(): string
    {
        return match ($this) {
            self::Quantity => 'Quantity',
            self::Individual => 'Individual',
        };
    }
}
