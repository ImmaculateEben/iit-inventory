<?php

namespace App\Livewire\Adjustments;

use App\Models\InventoryItem;
use App\Models\StockAdjustment;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['inventory_item_id', 'adjustment_type', 'quantity', 'reason'];
    }

    public string $inventory_item_id = '';
    public string $adjustment_type = 'stock_in';
    public int $quantity = 0;
    public string $reason = '';

    protected function rules(): array
    {
        return [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'adjustment_type' => 'required|in:stock_in,stock_out,correction_increase,correction_decrease,damage,loss,disposal,repair_out,repair_in',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();
        $item = InventoryItem::findOrFail($this->inventory_item_id);
        $before = $item->quantity_in_stock;

        $increase = in_array($this->adjustment_type, ['stock_in', 'correction_increase', 'repair_in']);

        if (!$increase && $item->quantity_available < $this->quantity) {
            $this->addError('quantity', 'Insufficient stock. Available: ' . $item->quantity_available);
            return;
        }

        $adjustment = StockAdjustment::create([
            'inventory_item_id' => $this->inventory_item_id,
            'adjusted_by' => auth()->id(),
            'adjustment_type' => $this->adjustment_type,
            'quantity' => $this->quantity,
            'quantity_before' => $before,
            'quantity_after' => $increase ? $before + $this->quantity : $before - $this->quantity,
            'reason' => $this->reason,
        ]);

        if ($increase) {
            $item->increment('quantity_in_stock', $this->quantity);
            $item->increment('quantity_available', $this->quantity);
        } else {
            $item->decrement('quantity_in_stock', $this->quantity);
            $item->decrement('quantity_available', $this->quantity);
        }

        AuditLogger::log('stock_adjusted', StockAdjustment::class, $adjustment->id, ['qty' => $before], ['qty' => $adjustment->quantity_after]);

        $this->clearFormState();
        session()->flash('success', 'Stock adjustment recorded.');
        return $this->redirect(route('adjustments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.adjustments.create', [
            'items' => InventoryItem::where('is_active', true)->orderBy('item_name')->get(),
        ])->layout('layouts.app');
    }
}
