<?php

namespace App\Livewire\Returns;

use App\Enums\ConditionStatus;
use App\Models\InventoryItem;
use App\Models\IssueRecord;
use App\Models\ReturnRecord;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Create extends Component
{
    public string $issue_record_id = '';
    public string $inventory_item_id = '';
    public int $quantity_returned = 1;
    public string $condition_on_return = 'good';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'issue_record_id' => 'required|exists:issue_records,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity_returned' => 'required|integer|min:1',
            'condition_on_return' => 'required|in:good,damaged,faulty',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function updatedIssueRecordId($value): void
    {
        if ($value) {
            $issue = IssueRecord::find($value);
            if ($issue) {
                $this->inventory_item_id = (string) $issue->inventory_item_id;
                $this->quantity_returned = $issue->quantity_issued;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $return = ReturnRecord::create([
            'issue_record_id' => $this->issue_record_id,
            'inventory_item_id' => $this->inventory_item_id,
            'returned_by' => IssueRecord::find($this->issue_record_id)->issued_to,
            'received_by' => auth()->id(),
            'quantity_returned' => $this->quantity_returned,
            'condition_on_return' => $this->condition_on_return,
            'return_date' => now(),
            'notes' => $this->notes ?: null,
        ]);

        $item = InventoryItem::findOrFail($this->inventory_item_id);
        $item->increment('quantity_available', $this->quantity_returned);
        $item->decrement('quantity_issued', $this->quantity_returned);

        AuditLogger::log('item_returned', ReturnRecord::class, $return->id, null, [
            'item' => $item->item_name, 'qty' => $this->quantity_returned,
        ]);

        session()->flash('success', 'Return recorded successfully.');
        return $this->redirect(route('returns.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.returns.create', [
            'issueRecords' => IssueRecord::with('inventoryItem')
                ->where('quantity_issued', '>', 0)
                ->latest('issue_date')
                ->get(),
        ])->layout('layouts.app');
    }
}
