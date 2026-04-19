<?php

namespace App\Models;

use App\Enums\AdjustmentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'adjustment_number', 'inventory_item_id', 'action_type', 'note',
        'related_issue_record_id', 'related_return_record_id', 'related_repair_record_id',
    ];

    protected function casts(): array
    {
        return [
            'action_type' => AdjustmentAction::class,
            'performed_at' => 'datetime',
        ];
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    public static function generateNumber(): string
    {
        $latest = static::latest('id')->first();
        $next = $latest ? $latest->id + 1 : 1;
        return 'ADJ-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
