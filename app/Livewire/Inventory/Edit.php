<?php

namespace App\Livewire\Inventory;

use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Department;
use App\Models\InventoryItem;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Edit extends Component
{
    public InventoryItem $inventoryItem;

    // Basic Information
    public string $item_code = '';
    public string $item_name = '';
    public string $description = '';
    public string $item_type = '';
    public string $tracking_method = '';
    public string $category_id = '';
    public string $department_id = '';
    public string $unit_of_measure = '';

    // Procurement
    public string $manufacturer = '';
    public string $model_number = '';
    public string $supplier_donor = '';
    public ?string $purchase_date = null;
    public ?string $purchase_cost = null;
    public string $warranty_info = '';
    public ?string $warranty_expiry = null;
    public string $guarantee_info = '';

    // Location
    public string $location = '';
    public string $floor = '';
    public string $venue = '';
    public string $venue_storage = '';

    // Stock
    public ?int $low_stock_threshold = null;
    public string $size = '';
    public string $sizeMode = 'simple';
    public string $dim_width = '';
    public string $dim_length = '';
    public string $dim_height = '';
    public string $dim_unit = 'cm';
    public string $remarks = '';
    public bool $is_active = true;

    // Custom fields
    public array $customFieldValues = [];
    public array $extraFields = [];

    public function mount(InventoryItem $inventoryItem): void
    {
        $this->inventoryItem = $inventoryItem->load('customFieldValues');

        $this->item_code = $inventoryItem->item_code ?? '';
        $this->item_name = $inventoryItem->item_name ?? '';
        $this->description = $inventoryItem->description ?? '';
        $this->item_type = $inventoryItem->item_type?->value ?? 'consumable';
        $this->tracking_method = $inventoryItem->tracking_method?->value ?? 'quantity';
        $this->category_id = (string) $inventoryItem->category_id;
        $this->department_id = (string) $inventoryItem->department_id;
        $this->unit_of_measure = $inventoryItem->unit_of_measure ?? '';

        $this->manufacturer = $inventoryItem->manufacturer ?? '';
        $this->model_number = $inventoryItem->model_number ?? '';
        $this->supplier_donor = $inventoryItem->supplier_donor ?? '';
        $this->purchase_date = $inventoryItem->purchase_date?->format('Y-m-d');
        $this->purchase_cost = $inventoryItem->purchase_cost !== null ? (string) $inventoryItem->purchase_cost : null;
        $this->warranty_info = $inventoryItem->warranty_info ?? '';
        $this->warranty_expiry = $inventoryItem->warranty_expiry?->format('Y-m-d');
        $this->guarantee_info = $inventoryItem->guarantee_info ?? '';

        $this->location = $inventoryItem->location ?? '';
        $this->floor = $inventoryItem->floor ?? '';
        $this->venue = $inventoryItem->venue ?? '';
        $this->venue_storage = $inventoryItem->venue_storage ?? '';

        $this->low_stock_threshold = $inventoryItem->low_stock_threshold;
        $rawSize = $inventoryItem->size ?? '';
        if (str_contains($rawSize, '×')) {
            $this->sizeMode = 'dimensions';
            foreach (['cm', 'mm', 'm', 'in', 'ft'] as $u) {
                if (str_ends_with(trim($rawSize), $u)) {
                    $this->dim_unit = $u;
                    $rawSize = trim(substr($rawSize, 0, -strlen($u)));
                    break;
                }
            }
            $parts = array_map('trim', explode('×', $rawSize));
            $this->dim_width  = $parts[0] ?? '';
            $this->dim_length = $parts[1] ?? '';
            $this->dim_height = $parts[2] ?? '';
        } else {
            $this->size = $rawSize;
        }
        $this->remarks = $inventoryItem->remarks ?? '';
        $this->is_active = (bool) $inventoryItem->is_active;

        // Load existing custom field values
        foreach ($inventoryItem->customFieldValues as $cfv) {
            $field = $cfv->customField;
            if (!$field) continue;
            $this->customFieldValues[$cfv->custom_field_id] = match ($field->field_type->value) {
                'number' => $cfv->value_number,
                'date' => $cfv->value_date,
                'boolean' => $cfv->value_boolean,
                default => $cfv->value_text,
            };
        }
    }

    protected function rules(): array
    {
        return [
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code,' . $this->inventoryItem->id,
            'item_name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'item_type' => 'required|in:consumable,asset',
            'tracking_method' => 'required|in:quantity,individual',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'required|exists:departments,id',
            'unit_of_measure' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:200',
            'model_number' => 'nullable|string|max:200',
            'supplier_donor' => 'nullable|string|max:200',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_info' => 'nullable|string|max:500',
            'warranty_expiry' => 'nullable|date',
            'guarantee_info' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:200',
            'floor' => 'nullable|string|max:100',
            'venue' => 'nullable|string|max:200',
            'venue_storage' => 'nullable|string|max:200',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'size' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ];
    }

    public function addExtraField(): void
    {
        $this->extraFields[] = [
            'label' => '',
            'type' => 'text',
            'value' => '',
            'save_for_future' => false,
        ];
    }

    public function removeExtraField(int $index): void
    {
        unset($this->extraFields[$index]);
        $this->extraFields = array_values($this->extraFields);
    }

    public function save()
    {
        if ($this->sizeMode === 'dimensions') {
            $dims = array_filter([$this->dim_width ?: null, $this->dim_length ?: null, $this->dim_height ?: null]);
            $this->size = $dims ? implode(' × ', $dims) . ($this->dim_unit ? ' ' . $this->dim_unit : '') : '';
        }

        $validated = $this->validate();
        $old = $this->inventoryItem->toArray();

        $itemData = collect($validated)->only([
            'item_code', 'item_name', 'description', 'item_type', 'tracking_method',
            'category_id', 'department_id', 'unit_of_measure',
            'manufacturer', 'model_number', 'supplier_donor',
            'purchase_date', 'purchase_cost', 'warranty_info', 'warranty_expiry', 'guarantee_info',
            'location', 'floor', 'venue', 'venue_storage',
            'size', 'remarks', 'low_stock_threshold',
        ])->toArray();

        $itemData['is_active'] = $this->is_active;
        $itemData['updated_by'] = auth()->id();

        $this->inventoryItem->update($itemData);

        // Sync custom field values
        foreach ($this->customFieldValues as $fieldId => $value) {
            $field = CustomField::find($fieldId);
            if (!$field) continue;

            $existing = CustomFieldValue::where('custom_field_id', $fieldId)
                ->where('entity_type', 'inventory_item')
                ->where('entity_id', $this->inventoryItem->id)
                ->first();

            if ($value === '' || $value === null) {
                $existing?->delete();
                continue;
            }

            $data = ['custom_field_id' => $fieldId, 'entity_type' => 'inventory_item', 'entity_id' => $this->inventoryItem->id];
            match ($field->field_type->value) {
                'number' => $data['value_number'] = $value,
                'date' => $data['value_date'] = $value,
                'boolean' => $data['value_boolean'] = (bool) $value,
                default => $data['value_text'] = $value,
            };

            if ($existing) {
                $existing->update($data);
            } else {
                CustomFieldValue::create($data);
            }
        }

        // Save ad-hoc extra fields
        $canManageFields = auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_custom_fields');
        foreach ($this->extraFields as $ef) {
            if (empty($ef['label']) || ($ef['value'] === '' && $ef['value'] === null)) continue;

            $fieldKey = \Illuminate\Support\Str::slug($ef['label'], '_');

            if ($canManageFields) {
                $customField = CustomField::firstOrCreate(
                    ['field_key' => $fieldKey],
                    [
                        'label' => $ef['label'],
                        'field_type' => $ef['type'],
                        'entity_scope' => 'inventory_item',
                        'is_active' => !empty($ef['save_for_future']),
                    ]
                );
            } else {
                $customField = CustomField::firstOrCreate(
                    ['field_key' => $fieldKey],
                    [
                        'label' => $ef['label'],
                        'field_type' => $ef['type'],
                        'entity_scope' => 'inventory_item',
                        'is_active' => false,
                    ]
                );
            }

            $cfv = ['custom_field_id' => $customField->id, 'entity_type' => 'inventory_item', 'entity_id' => $this->inventoryItem->id];
            match ($ef['type']) {
                'number' => $cfv['value_number'] = $ef['value'],
                'date' => $cfv['value_date'] = $ef['value'],
                'boolean' => $cfv['value_boolean'] = (bool) $ef['value'],
                default => $cfv['value_text'] = $ef['value'],
            };
            CustomFieldValue::create($cfv);
        }

        AuditLogger::log('inventory_item_updated', InventoryItem::class, $this->inventoryItem->id, $old, $itemData);

        session()->flash('success', 'Inventory item updated successfully.');
        return $this->redirect(route('inventory.show', $this->inventoryItem), navigate: true);
    }

    public function render()
    {
        return view('livewire.inventory.edit', [
            'categories' => Category::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'customFields' => CustomField::where('entity_scope', 'inventory_item')->where('is_active', true)->orderBy('label')->get(),
        ])->layout('layouts.app');
    }
}
