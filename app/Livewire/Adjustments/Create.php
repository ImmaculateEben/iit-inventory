<?php

namespace App\Livewire\Adjustments;

use App\Models\InventoryItem;
use App\Models\StockAdjustment;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Illuminate\Support\Facades\DB;
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
            'adjustment_type' => 'required|in:stock_in,stock_out,correction_increase,correction_decrease,damage,loss,disposal',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $item = InventoryItem::lockForUpdate()->findOrFail($this->inventory_item_id);

            // Enforce department/category access boundary
            abort_unless(auth()->user()->canAccessItem($item), 403, 'You do not have access to this item.');

            $before = $item->quantity_in_stock;

            $increase = in_array($this->adjustment_type, ['stock_in', 'correction_increase']);

            if (!$increase && $item->quantity_available < $this->quantity) {
                $this->addError('quantity', 'Insufficient stock. Available: ' . $item->quantity_available);
                return;
            }

            $deltaTotal = $increase ? $this->quantity : -$this->quantity;
            $deltaAvailable = $deltaTotal;

            $adjustment = StockAdjustment::forceCreate([
                'adjustment_number' => StockAdjustment::generateNumber(),
                'inventory_item_id' => $this->inventory_item_id,
                'action_type' => $this->adjustment_type,
                'delta_total' => $deltaTotal,
                'delta_available' => $deltaAvailable,
                'performed_by_user_id' => auth()->id(),
                'performed_at' => now(),
                'note' => $this->reason,
            ]);

            if ($increase) {
                $item->increment('quantity_in_stock', $this->quantity);
                $item->increment('quantity_available', $this->quantity);
            } else {
                $item->decrement('quantity_in_stock', $this->quantity);
                $item->decrement('quantity_available', $this->quantity);
            }

            AuditLogger::log('stock_adjusted', StockAdjustment::class, $adjustment->id, ['qty' => $before], ['qty' => $before + $deltaTotal]);

            session()->flash('success', 'Stock adjustment recorded.');
        });

        $this->clearFormState();
        return $this->redirect(route('adjustments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.adjustments.create', [
            'items' => auth()->user()->scopeInventoryItems(
                InventoryItem::where('is_active', true)
            )->orderBy('item_name')->get(),
        ])->layout('layouts.app');
    }
}
