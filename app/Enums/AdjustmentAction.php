<?php

namespace App\Enums;

enum AdjustmentAction: string
{
    case StockIn = 'stock_in';
    case StockOut = 'stock_out';
    case CorrectionIncrease = 'correction_increase';
    case CorrectionDecrease = 'correction_decrease';
    case Damage = 'damage';
    case Loss = 'loss';
    case Disposal = 'disposal';
    case RepairOut = 'repair_out';
    case RepairIn = 'repair_in';

    public function label(): string
    {
        return match ($this) {
            self::StockIn => 'Stock In',
            self::StockOut => 'Stock Out',
            self::CorrectionIncrease => 'Correction (Increase)',
            self::CorrectionDecrease => 'Correction (Decrease)',
            self::Damage => 'Damage',
            self::Loss => 'Loss',
            self::Disposal => 'Disposal',
            self::RepairOut => 'Repair Out',
            self::RepairIn => 'Repair In',
        };
    }
}
