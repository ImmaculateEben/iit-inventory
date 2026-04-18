<?php

namespace App\Models;

use App\Enums\CustomFieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    protected $fillable = ['field_key', 'label', 'field_type', 'options_json', 'entity_scope', 'is_required', 'is_active'];

    protected function casts(): array
    {
        return [
            'field_type' => CustomFieldType::class,
            'options_json' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }
}
