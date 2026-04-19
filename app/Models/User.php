<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'department_id', 'is_active', 'can_view_all_inventory',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'can_view_all_inventory' => 'boolean',
            'last_login_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function accessibleDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_accessible_departments');
    }

    public function accessibleCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'user_accessible_categories');
    }

    public function canViewAllInventory(): bool
    {
        return $this->isAdmin() || $this->can_view_all_inventory || $this->hasPermission('manage_inventory');
    }

    /**
     * Get all department IDs this user can see inventory for.
     * Returns null if the user can see ALL departments.
     */
    public function getAccessibleDepartmentIds(): ?array
    {
        if ($this->canViewAllInventory()) {
            return null; // null = no restriction
        }

        $ids = collect([$this->department_id]);
        $extra = $this->accessibleDepartments()->pluck('departments.id');
        return $ids->merge($extra)->unique()->filter()->values()->toArray();
    }

    /**
     * Get category IDs this user is restricted to.
     * Returns null if the user can see ALL categories.
     */
    public function getAccessibleCategoryIds(): ?array
    {
        if ($this->canViewAllInventory()) {
            return null;
        }

        $ids = $this->accessibleCategories()->pluck('categories.id')->toArray();
        return count($ids) > 0 ? $ids : null; // null = no category restriction
    }

    /**
     * Scope an InventoryItem query to only items this user can access.
     */
    public function scopeInventoryItems($query = null)
    {
        $base = $query ?? \App\Models\InventoryItem::query();
        $deptIds = $this->getAccessibleDepartmentIds();
        $catIds = $this->getAccessibleCategoryIds();

        return $base
            ->when($deptIds !== null, fn($q) => $q->whereIn('department_id', $deptIds))
            ->when($catIds !== null, fn($q) => $q->whereIn('category_id', $catIds));
    }

    /**
     * Check if this user can access a specific inventory item.
     */
    public function canAccessItem(InventoryItem $item): bool
    {
        $deptIds = $this->getAccessibleDepartmentIds();
        $catIds = $this->getAccessibleCategoryIds();

        if ($deptIds !== null && !in_array($item->department_id, $deptIds)) {
            return false;
        }
        if ($catIds !== null && $item->category_id && !in_array($item->category_id, $catIds)) {
            return false;
        }
        return true;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hasPermission(string $code): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($code) {
            $q->where('code', $code);
        })->exists();
    }

    public function hasRole(string $code): bool
    {
        return $this->roles()->where('code', $code)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('archived_at');
    }
}
