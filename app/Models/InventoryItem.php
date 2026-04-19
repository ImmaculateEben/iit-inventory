<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Enums\TrackingMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_name', 'item_code', 'department_id', 'category_id', 'item_type', 'tracking_method',
        'unit_of_measure', 'image_path', 'description',
        // Procurement
        'manufacturer', 'model_number', 'supplier_donor',
        'purchase_date', 'purchase_cost', 'warranty_info', 'warranty_expiry', 'guarantee_info',
        // Location
        'location', 'floor', 'venue', 'venue_storage',
        // Details
        'size', 'remarks',
        // Stock (thresholds only - quantities are managed server-side)
        'low_stock_threshold', 'reorder_level',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'item_type' => ItemType::class,
            'tracking_method' => TrackingMethod::class,
            'is_active' => 'boolean',
            'archived_at' => 'datetime',
            'purchase_date' => 'date',
            'warranty_expiry' => 'date',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('archived_at');
    }

    public function scopeQuantityTracked($query)
    {
        return $query->where('tracking_method', TrackingMethod::Quantity);
    }

    public function scopeIndividualTracked($query)
    {
        return $query->where('tracking_method', TrackingMethod::Individual);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function assetUnits(): HasMany
    {
        return $this->hasMany(AssetUnit::class);
    }

    public function issueRecords(): HasMany
    {
        return $this->hasMany(IssueRecord::class);
    }

    public function repairRecords(): HasMany
    {
        return $this->hasMany(RepairRecord::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'entity_id')
            ->where('entity_type', 'inventory_item');
    }

    public function isConsumable(): bool
    {
        return $this->item_type === ItemType::Consumable;
    }

    public function isQuantityTracked(): bool
    {
        return $this->tracking_method === TrackingMethod::Quantity;
    }

    public function isIndividualTracked(): bool
    {
        return $this->tracking_method === TrackingMethod::Individual;
    }
}
