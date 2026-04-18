<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class Show extends Component
{
    public InventoryItem $inventoryItem;

    public function mount(InventoryItem $inventoryItem): void
    {
        // Enforce access scoping
        $user = auth()->user();
        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        if ($deptIds !== null && !in_array($inventoryItem->department_id, $deptIds)) {
            abort(403, 'You do not have access to this item.');
        }
        if ($catIds !== null && !in_array($inventoryItem->category_id, $catIds)) {
            abort(403, 'You do not have access to this item.');
        }

        $this->inventoryItem = $inventoryItem->load([
            'category', 'department', 'assetUnits', 'customFieldValues.customField',
            'stockAdjustments' => fn($q) => $q->latest()->limit(10),
            'issueRecords' => fn($q) => $q->latest('issued_at')->limit(10),
            'repairRecords' => fn($q) => $q->latest('repair_date'),
        ]);
    }

    public function render()
    {
        return view('livewire.inventory.show')->layout('layouts.app');
    }
}
