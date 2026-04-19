<?php

namespace App\Enums;

enum RepairStatus: string
{
    case Reported = 'reported';
    case SentForRepair = 'sent_for_repair';
    case InRepair = 'in_repair';
    case Repaired = 'repaired';
    case Returned = 'returned';
    case NotRepairable = 'not_repairable';

    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Reported',
            self::SentForRepair => 'Sent for Repair',
            self::InRepair => 'In Repair',
            self::Repaired => 'Repaired',
            self::Returned => 'Returned',
            self::NotRepairable => 'Not Repairable',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Reported => 'yellow',
            self::SentForRepair => 'blue',
            self::InRepair => 'indigo',
            self::Repaired => 'green',
            self::Returned => 'gray',
            self::NotRepairable => 'red',
        };
    }

    /**
     * Get the valid statuses that can follow this status.
     *
     * @return self[]
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::Reported => [self::SentForRepair, self::NotRepairable],
            self::SentForRepair => [self::InRepair, self::NotRepairable],
            self::InRepair => [self::Repaired, self::NotRepairable],
            self::Repaired => [self::Returned],
            self::Returned => [],
            self::NotRepairable => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->validTransitions(), true);
    }
}
