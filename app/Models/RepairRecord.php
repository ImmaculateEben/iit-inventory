<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairRecord extends Model
{
    protected $fillable = [
        'repair_number', 'inventory_item_id', 'asset_unit_id', 'department_id',
        'quantity', 'problem_description', 'repair_notes',
        'date_reported', 'date_sent', 'date_returned',
        'status', 'created_by_user_id', 'updated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => RepairStatus::class,
            'date_reported' => 'date',
            'date_sent' => 'date',
            'date_returned' => 'date',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function assetUnit(): BelongsTo
    {
        return $this->belongsTo(AssetUnit::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function generateNumber(): string
    {
        $latest = static::latest('id')->first();
        $next = $latest ? $latest->id + 1 : 1;
        return 'RPR-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
