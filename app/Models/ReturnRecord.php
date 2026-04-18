<?php

namespace App\Models;

use App\Enums\ConditionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRecord extends Model
{
    protected $fillable = [
        'issue_record_id', 'inventory_item_id', 'asset_unit_id', 'department_id',
        'staff_directory_id', 'staff_name_snapshot', 'returned_quantity',
        'return_condition', 'received_by_user_id', 'returned_at', 'note',
    ];

    protected function casts(): array
    {
        return [
            'return_condition' => ConditionStatus::class,
            'returned_at' => 'datetime',
        ];
    }

    public function issueRecord(): BelongsTo
    {
        return $this->belongsTo(IssueRecord::class);
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

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}
