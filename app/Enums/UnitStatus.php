<?php

namespace App\Enums;

enum UnitStatus: string
{
    case Available = 'available';
    case Issued = 'issued';
    case UnderRepair = 'under_repair';
    case Damaged = 'damaged';
    case Lost = 'lost';
    case Disposed = 'disposed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Issued => 'Issued',
            self::UnderRepair => 'Under Repair',
            self::Damaged => 'Damaged',
            self::Lost => 'Lost',
            self::Disposed => 'Disposed',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Issued => 'blue',
            self::UnderRepair => 'yellow',
            self::Damaged => 'red',
            self::Lost => 'gray',
            self::Disposed => 'gray',
            self::Archived => 'gray',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Lost, self::Disposed, self::Archived]);
    }
}
