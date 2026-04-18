<?php

namespace App\Livewire\Issues;

use App\Models\InventoryItem;
use App\Models\IssueRecord;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Create extends Component
{
    public string $inventory_item_id = '';
    public string $department_id = '';
    public string $staff_name = '';
    public int $quantity = 1;
    public ?string $asset_unit_id = null;
    public string $note = '';

    // Search / UI state
    public string $itemSearch = '';
    public string $staffSearch = '';
    public bool $showItemDropdown = false;
    public bool $showStaffDropdown = false;
    public int $maxQuantity = 0;
    public string $selectedItemLabel = '';

    protected function rules(): array
    {
        return [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'department_id' => 'required|exists:departments,id',
            'staff_name' => 'required|string|max:200',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'staff_name.required' => 'Enter the name of the person receiving the item.',
    ];

    public function updatedItemSearch(): void
    {
        $this->showItemDropdown = strlen($this->itemSearch) > 0;
    }

    public function selectItem(int $id): void
    {
        $item = InventoryItem::find($id);
        if (!$item) return;

        $this->inventory_item_id = (string) $item->id;
        $this->department_id = (string) $item->department_id;
        $this->maxQuantity = $item->quantity_available;
        $this->selectedItemLabel = $item->item_code . ' — ' . $item->item_name . ' (' . $item->quantity_available . ' available)';
        $this->itemSearch = $this->selectedItemLabel;
        $this->showItemDropdown = false;
        $this->quantity = 1;
    }

    public function clearItem(): void
    {
        $this->inventory_item_id = '';
        $this->itemSearch = '';
        $this->selectedItemLabel = '';
        $this->maxQuantity = 0;
        $this->quantity = 1;
    }

    public function updatedStaffSearch(): void
    {
        $this->staff_name = $this->staffSearch;
        $this->showStaffDropdown = strlen($this->staffSearch) >= 2;
    }

    public function selectStaff(string $name): void
    {
        $this->staff_name = $name;
        $this->staffSearch = $name;
        $this->showStaffDropdown = false;
    }

    public function updatedQuantity(): void
    {
        if ($this->maxQuantity > 0 && $this->quantity > $this->maxQuantity) {
            $this->quantity = $this->maxQuantity;
        }
        if ($this->quantity < 1) {
            $this->quantity = 1;
        }
    }

    public function save()
    {
        $this->validate();

        $item = InventoryItem::findOrFail($this->inventory_item_id);

        if ($item->quantity_available < $this->quantity) {
            $this->addError('quantity', 'Not enough stock. Available: ' . $item->quantity_available);
            return;
        }

        $issue = IssueRecord::create([
            'issue_number' => IssueRecord::generateNumber(),
            'inventory_item_id' => $this->inventory_item_id,
            'department_id' => $this->department_id,
            'staff_name_snapshot' => $this->staff_name,
            'quantity' => $this->quantity,
            'asset_unit_id' => $this->asset_unit_id ?: null,
            'issued_by_user_id' => auth()->id(),
            'issued_at' => now(),
            'note' => $this->note ?: null,
        ]);

        $item->decrement('quantity_available', $this->quantity);
        $item->increment('quantity_issued', $this->quantity);

        AuditLogger::log('item_issued', IssueRecord::class, $issue->id, null, [
            'item' => $item->item_name, 'qty' => $this->quantity, 'to' => $this->staff_name,
        ]);

        session()->flash('success', 'Item issued successfully.');
        return $this->redirect(route('issues.index'), navigate: true);
    }

    public function render()
    {
        $items = collect();
        if (strlen($this->itemSearch) > 0 && !$this->inventory_item_id) {
            $items = InventoryItem::where('is_active', true)
                ->where('quantity_available', '>', 0)
                ->where(function ($q) {
                    $q->where('item_name', 'like', '%' . $this->itemSearch . '%')
                      ->orWhere('item_code', 'like', '%' . $this->itemSearch . '%');
                })
                ->orderBy('item_name')
                ->limit(15)
                ->get();
        }

        $pastStaffNames = collect();
        if (strlen($this->staffSearch) >= 2) {
            $pastStaffNames = IssueRecord::where('staff_name_snapshot', 'like', '%' . $this->staffSearch . '%')
                ->whereNotNull('staff_name_snapshot')
                ->select('staff_name_snapshot')
                ->distinct()
                ->orderBy('staff_name_snapshot')
                ->limit(10)
                ->pluck('staff_name_snapshot');
        }

        return view('livewire.issues.create', [
            'filteredItems' => $items,
            'pastStaffNames' => $pastStaffNames,
        ])->layout('layouts.app');
    }
}
