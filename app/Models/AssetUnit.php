<?php

namespace App\Models;

use App\Enums\ConditionStatus;
use App\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetUnit extends Model
{
    protected $fillable = [
        'inventory_item_id', 'serial_number', 'asset_tag',
        'assigned_department_id', 'assigned_staff_directory_id', 'assigned_staff_name_snapshot',
        'condition_status', 'unit_status', 'current_location', 'notes',
        'archived_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'condition_status' => ConditionStatus::class,
            'unit_status' => UnitStatus::class,
            'archived_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'assigned_department_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(StaffDirectory::class, 'assigned_staff_directory_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function repairRecords(): HasMany
    {
        return $this->hasMany(RepairRecord::class);
    }

    public function issueRecords(): HasMany
    {
        return $this->hasMany(IssueRecord::class);
    }

    public function isAvailable(): bool
    {
        return $this->unit_status === UnitStatus::Available;
    }
}
