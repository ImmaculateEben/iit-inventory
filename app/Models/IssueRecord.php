<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssueRecord extends Model
{
    protected $fillable = [
        'issue_number', 'action_type', 'request_line_id', 'inventory_item_id', 'asset_unit_id',
        'department_id', 'staff_directory_id', 'staff_name_snapshot',
        'quantity', 'note',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function requestLine(): BelongsTo
    {
        return $this->belongsTo(RequestLine::class);
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

    public function staffDirectory(): BelongsTo
    {
        return $this->belongsTo(StaffDirectory::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public function returnRecords(): HasMany
    {
        return $this->hasMany(ReturnRecord::class);
    }

    public function outstandingQuantity(): int
    {
        return $this->quantity - $this->returned_quantity;
    }

    public static function generateNumber(): string
    {
        $latest = static::latest('id')->first();
        $next = $latest ? $latest->id + 1 : 1;
        return 'ISS-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
