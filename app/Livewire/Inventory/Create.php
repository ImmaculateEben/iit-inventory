<?php

namespace App\Livewire\Inventory;

use App\Enums\ItemType;
use App\Enums\TrackingMethod;
use App\Models\AssetUnit;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Department;
use App\Models\InventoryItem;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return [
            'item_code', 'item_name', 'description', 'item_type', 'tracking_method',
            'category_id', 'department_id', 'unit_of_measure',
            'manufacturer', 'model_number', 'supplier_donor',
            'purchase_date', 'purchase_cost', 'warranty_info', 'warranty_expiry', 'guarantee_info',
            'location', 'floor', 'venue', 'venue_storage',
            'quantity_in_stock', 'low_stock_threshold', 'size', 'sizeMode',
            'dim_width', 'dim_length', 'dim_height', 'dim_unit',
            'remarks', 'is_active',
        ];
    }

    // Basic Information
    public string $item_code = '';
    public string $item_name = '';
    public string $description = '';
    public string $item_type = 'consumable';
    public string $tracking_method = 'quantity';
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

    // Stock & tracking
    public int $quantity_in_stock = 0;
    public ?int $low_stock_threshold = null;
    public string $size = '';
    public string $sizeMode = 'simple';
    public string $dim_width = '';
    public string $dim_length = '';
    public string $dim_height = '';
    public string $dim_unit = 'cm';
    public string $remarks = '';
    public bool $is_active = true;

    // Asset units (for individual tracking)
    public array $assetUnits = [];

    // Custom fields
    public array $customFieldValues = [];
    public array $extraFields = [];

    protected function rules(): array
    {
        $rules = [
            'item_code' => 'nullable|string|max:50|unique:inventory_items,item_code',
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
            'size' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($this->tracking_method === 'quantity') {
            $rules['quantity_in_stock'] = 'required|integer|min:0';
        } else {
            $rules['assetUnits'] = 'required|array|min:1';
            $rules['assetUnits.*.serial_number'] = 'nullable|string|max:200';
            $rules['assetUnits.*.asset_tag'] = 'nullable|string|max:200';
            $rules['assetUnits.*.condition_status'] = 'required|in:good,damaged,faulty';
            $rules['assetUnits.*.assigned_staff_name_snapshot'] = 'nullable|string|max:200';
            $rules['assetUnits.*.current_location'] = 'nullable|string|max:200';
        }

        return $rules;
    }

    protected $messages = [
        'assetUnits.required' => 'At least one asset unit is required for individual tracking.',
        'assetUnits.min' => 'At least one asset unit is required.',
        'assetUnits.*.serial_number.max' => 'Serial number is too long.',
        'assetUnits.*.condition_status.required' => 'Condition is required for each unit.',
    ];

    public function mount(): void
    {
        $this->addAssetUnit();
    }

    public function addAssetUnit(): void
    {
        $this->assetUnits[] = [
            'serial_number' => '',
            'asset_tag' => '',
            'condition_status' => 'good',
            'assigned_staff_name_snapshot' => '',
            'current_location' => '',
        ];
    }

    public function removeAssetUnit(int $index): void
    {
        if (count($this->assetUnits) > 1) {
            unset($this->assetUnits[$index]);
            $this->assetUnits = array_values($this->assetUnits);
        }
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

        $itemData = collect($validated)->only([
            'item_code', 'item_name', 'description', 'item_type', 'tracking_method',
            'category_id', 'department_id', 'unit_of_measure',
            'manufacturer', 'model_number', 'supplier_donor',
            'purchase_date', 'purchase_cost', 'warranty_info', 'warranty_expiry', 'guarantee_info',
            'location', 'floor', 'venue', 'venue_storage',
            'size', 'remarks', 'low_stock_threshold',
        ])->toArray();

        $itemData['is_active'] = $this->is_active;
        $itemData['created_by'] = auth()->id();

        if ($this->tracking_method === 'quantity') {
            $itemData['quantity_in_stock'] = $this->quantity_in_stock;
            $itemData['quantity_total'] = $this->quantity_in_stock;
            $itemData['quantity_available'] = $this->quantity_in_stock;
        } else {
            $unitCount = count($this->assetUnits);
            $itemData['quantity_in_stock'] = $unitCount;
            $itemData['quantity_total'] = $unitCount;
            $itemData['quantity_available'] = $unitCount;
        }

        $item = InventoryItem::create($itemData);

        // Create asset units for individual tracking
        if ($this->tracking_method === 'individual') {
            foreach ($this->assetUnits as $unitData) {
                $item->assetUnits()->create([
                    'serial_number' => $unitData['serial_number'] ?: null,
                    'asset_tag' => $unitData['asset_tag'] ?: null,
                    'condition_status' => $unitData['condition_status'],
                    'assigned_staff_name_snapshot' => $unitData['assigned_staff_name_snapshot'] ?: null,
                    'current_location' => $unitData['current_location'] ?: null,
                    'unit_status' => 'available',
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // Save existing custom field values
        foreach ($this->customFieldValues as $fieldId => $value) {
            if ($value === '' || $value === null) continue;
            $field = CustomField::find($fieldId);
            if (!$field) continue;

            $cfv = ['custom_field_id' => $fieldId, 'entity_type' => 'inventory_item', 'entity_id' => $item->id];
            match ($field->field_type->value) {
                'number' => $cfv['value_number'] = $value,
                'date' => $cfv['value_date'] = $value,
                'boolean' => $cfv['value_boolean'] = (bool) $value,
                default => $cfv['value_text'] = $value,
            };
            CustomFieldValue::create($cfv);
        }

        // Save ad-hoc extra fields
        $canManageFields = auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_custom_fields');
        foreach ($this->extraFields as $ef) {
            if (empty($ef['label']) || ($ef['value'] === '' && $ef['value'] === null)) continue;

            $fieldKey = \Illuminate\Support\Str::slug($ef['label'], '_');

            if ($canManageFields) {
                $customField = CustomField::firstOrCreate(
                    ['field_key' => $fieldKey],
                    ['label' => $ef['label'], 'field_type' => $ef['type'], 'entity_scope' => 'inventory_item', 'is_active' => (bool) $ef['save_for_future']]
                );
            } else {
                // Without permission, only use an existing field — never create new definitions
                $customField = CustomField::where('field_key', $fieldKey)->first();
                if (!$customField) continue;
            }

            $cfv = ['custom_field_id' => $customField->id, 'entity_type' => 'inventory_item', 'entity_id' => $item->id];
            match ($ef['type']) {
                'number' => $cfv['value_number'] = $ef['value'],
                'date' => $cfv['value_date'] = $ef['value'],
                'boolean' => $cfv['value_boolean'] = (bool) $ef['value'],
                default => $cfv['value_text'] = $ef['value'],
            };
            CustomFieldValue::create($cfv);
        }

        AuditLogger::log('inventory_item_created', InventoryItem::class, $item->id, null, $itemData);

        $this->clearFormState();
        session()->flash('success', 'Inventory item created successfully.');
        return $this->redirect(route('inventory.show', $item), navigate: true);
    }

    public function render()
    {
        return view('livewire.inventory.create', [
            'categories' => Category::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'customFields' => CustomField::where('entity_scope', 'inventory_item')->where('is_active', true)->orderBy('label')->get(),
        ])->layout('layouts.app');
    }
}
