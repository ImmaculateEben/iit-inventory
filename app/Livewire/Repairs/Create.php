<?php

namespace App\Livewire\Repairs;

use App\Models\InventoryItem;
use App\Models\RepairRecord;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['inventory_item_id', 'action_type', 'component_repaired', 'repair_description', 'repair_date', 'status'];
    }

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

        // Enforce department/category access boundary
        abort_unless(auth()->user()->canAccessItem($item), 403, 'You do not have access to this item.');

        $repair = RepairRecord::create([
            'repair_number' => RepairRecord::generateNumber(),
            'action_type' => $this->action_type,
            'inventory_item_id' => $this->inventory_item_id,
            'department_id' => $item->department_id,
            'component_repaired' => $this->component_repaired,
            'repair_description' => $this->repair_description,
            'problem_description' => $this->repair_description,
            'repair_date' => $this->repair_date,
            'date_reported' => now(),
            'status' => $this->status,
            'created_by_user_id' => auth()->id(),
        ]);

        AuditLogger::log('repair_created', RepairRecord::class, $repair->id);

        $this->clearFormState();
        session()->flash('success', ucfirst($this->action_type) . ' record created.');
        return $this->redirect(route('repairs.index'), navigate: true);
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

        return view('livewire.repairs.create', [
            'filteredItems' => $filteredItems,
        ])->layout('layouts.app');
    }
}
