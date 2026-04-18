<?php

namespace App\Enums;

enum ConditionStatus: string
{
    case Good = 'good';
    case Damaged = 'damaged';
    case Faulty = 'faulty';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Damaged => 'Damaged',
            self::Faulty => 'Faulty',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Good => 'green',
            self::Damaged => 'red',
            self::Faulty => 'orange',
        };
    }
}
