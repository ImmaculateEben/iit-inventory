<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffDirectory extends Model
{
    protected $table = 'staff_directory';

    protected $fillable = ['display_name', 'normalized_name', 'linked_user_id', 'is_active', 'last_used_at'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    public static function findOrCreateByName(string $name): self
    {
        $normalized = mb_strtolower(trim($name));

        return static::firstOrCreate(
            ['normalized_name' => $normalized],
            ['display_name' => trim($name)]
        );
    }
}
