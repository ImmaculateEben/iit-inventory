<?php

namespace App\Livewire\Repairs;

use App\Models\InventoryItem;
use App\Models\RepairRecord;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Edit extends Component
{
    public RepairRecord $repairRecord;

    // Searchable item selection
    public string $itemSearch = '';
    public ?int $inventory_item_id = null;
    public ?string $selectedItemLabel = null;
    public bool $showItemDropdown = false;

    // Form fields
    public string $action_type = 'repair';
    public string $component_repaired = '';
    public string $repair_description = '';
    public ?string $repair_date = null;
    public string $status = 'reported';

    public function mount(RepairRecord $repairRecord): void
    {
        $this->repairRecord = $repairRecord->load('inventoryItem', 'createdBy');

        // Only admin or the reporter can edit
        $user = auth()->user();
        abort_unless(
            $user->isAdmin() || $repairRecord->created_by_user_id === $user->id,
            403
        );

        // Enforce department/category access boundary
        if ($repairRecord->inventoryItem) {
            abort_unless($user->canAccessItem($repairRecord->inventoryItem), 403, 'You do not have access to this item.');
        }

        $this->inventory_item_id = $repairRecord->inventory_item_id;
        $this->action_type = $repairRecord->action_type ?? 'repair';
        $this->component_repaired = $repairRecord->component_repaired ?? '';
        $this->repair_description = $repairRecord->repair_description ?? '';
        $this->repair_date = $repairRecord->repair_date?->format('Y-m-d');
        $this->status = $repairRecord->status->value ?? $repairRecord->status;

        if ($repairRecord->inventoryItem) {
            $item = $repairRecord->inventoryItem;
            $this->selectedItemLabel = ($item->item_code ? $item->item_code . ' — ' : '') . $item->item_name;
        }
    }

    protected function rules(): array
    {
        return [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'action_type' => 'required|in:repair,replacement',
            'component_repaired' => 'required|string|max:200',
            'repair_description' => 'required|string|max:2000',
            'repair_date' => 'required|date',
            'status' => 'required|in:reported,sent_for_repair,in_repair,repaired,returned,not_repairable',
        ];
    }

    protected $messages = [
        'inventory_item_id.required' => 'Please select an item.',
        'component_repaired.required' => 'Please specify what was repaired.',
        'repair_description.required' => 'Please provide a repair description.',
        'repair_date.required' => 'Please provide the repair date.',
    ];

    public function updatedItemSearch(): void
    {
        $this->showItemDropdown = strlen($this->itemSearch) >= 1;
    }

    public function selectItem(int $id): void
    {
        $item = InventoryItem::find($id);
        if ($item && auth()->user()->canAccessItem($item)) {
            $this->inventory_item_id = $item->id;
            $this->selectedItemLabel = ($item->item_code ? $item->item_code . ' — ' : '') . $item->item_name;
            $this->itemSearch = '';
            $this->showItemDropdown = false;
        }
    }

    public function clearItem(): void
    {
        $this->inventory_item_id = null;
        $this->selectedItemLabel = null;
        $this->itemSearch = '';
    }

    public function save()
    {
        $this->validate();

        $item = InventoryItem::findOrFail($this->inventory_item_id);

        $this->repairRecord->forceFill([
            'action_type' => $this->action_type,
            'inventory_item_id' => $this->inventory_item_id,
            'department_id' => $item->department_id,
            'component_repaired' => $this->component_repaired,
            'repair_description' => $this->repair_description,
            'problem_description' => $this->repair_description,
            'repair_date' => $this->repair_date,
            'status' => $this->status,
            'updated_by_user_id' => auth()->id(),
        ])->save();

        AuditLogger::log('repair_updated', RepairRecord::class, $this->repairRecord->id);

        session()->flash('success', ucfirst($this->action_type) . ' record updated.');
        return $this->redirect(route('repairs.show', $this->repairRecord), navigate: true);
    }

    public function render()
    {
        $filteredItems = [];
        if (strlen($this->itemSearch) >= 1) {
            $filteredItems = auth()->user()->scopeInventoryItems(
                InventoryItem::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('item_name', 'like', "%{$this->itemSearch}%")
                          ->orWhere('item_code', 'like', "%{$this->itemSearch}%")
                          ->orWhere('model_number', 'like', "%{$this->itemSearch}%")
                          ->orWhere('manufacturer', 'like', "%{$this->itemSearch}%");
                    })
            )->orderBy('item_name')->limit(15)->get();
        }

        return view('livewire.repairs.edit', [
            'filteredItems' => $filteredItems,
        ])->layout('layouts.app');
    }
}
