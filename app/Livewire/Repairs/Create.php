<?php

namespace App\Livewire\Repairs;

use App\Models\InventoryItem;
use App\Models\RepairRecord;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Create extends Component
{
    public string $inventory_item_id = '';
    public ?string $asset_unit_id = null;
    public string $fault_description = '';
    public string $vendor_name = '';
    public ?string $sent_date = null;

    protected function rules(): array
    {
        return [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'fault_description' => 'required|string|max:1000',
            'vendor_name' => 'nullable|string|max:200',
            'sent_date' => 'nullable|date',
        ];
    }

    public function save()
    {
        $this->validate();

        $repair = RepairRecord::create([
            'inventory_item_id' => $this->inventory_item_id,
            'asset_unit_id' => $this->asset_unit_id,
            'reported_by' => auth()->id(),
            'fault_description' => $this->fault_description,
            'status' => 'reported',
            'vendor_name' => $this->vendor_name ?: null,
            'sent_date' => $this->sent_date,
        ]);

        AuditLogger::log('repair_created', RepairRecord::class, $repair->id);

        session()->flash('success', 'Repair record created.');
        return $this->redirect(route('repairs.show', $repair), navigate: true);
    }

    public function render()
    {
        return view('livewire.repairs.create', [
            'items' => InventoryItem::where('is_active', true)->orderBy('item_name')->get(),
        ])->layout('layouts.app');
    }
}
