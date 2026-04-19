<?php

namespace App\Livewire\Returns;

use App\Enums\ConditionStatus;
use App\Enums\UnitStatus;
use App\Models\AssetUnit;
use App\Models\InventoryItem;
use App\Models\IssueRecord;
use App\Models\ReturnRecord;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['issue_record_id', 'returned_quantity', 'return_condition', 'note'];
    }

    public string $issue_record_id = '';
    public int $returned_quantity = 1;
    public string $return_condition = 'good';
    public string $note = '';

    // Display info
    public int $maxReturnQuantity = 0;
    public string $staffName = '';
    public string $departmentName = '';
    public string $itemName = '';

    protected function rules(): array
    {
        return [
            'issue_record_id' => 'required|exists:issue_records,id',
            'returned_quantity' => 'required|integer|min:1',
            'return_condition' => 'required|in:good,damaged,faulty',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function updatedIssueRecordId($value): void
    {
        if ($value) {
            $issue = IssueRecord::with(['inventoryItem', 'department'])->find($value);
            if ($issue) {
                $outstanding = $issue->outstandingQuantity();
                $this->maxReturnQuantity = $outstanding;
                $this->returned_quantity = $outstanding;
                $this->staffName = $issue->staff_name_snapshot ?? '';
                $this->departmentName = $issue->department?->name ?? '';
                $this->itemName = $issue->inventoryItem?->item_name ?? '';
            }
        } else {
            $this->maxReturnQuantity = 0;
            $this->returned_quantity = 1;
            $this->staffName = '';
            $this->departmentName = '';
            $this->itemName = '';
        }
    }

    public function save()
    {
        $this->validate();

        $issue = IssueRecord::findOrFail($this->issue_record_id);
        $outstanding = $issue->outstandingQuantity();

        if ($this->returned_quantity > $outstanding) {
            $this->addError('returned_quantity', "Cannot return more than outstanding quantity ($outstanding).");
            return;
        }

        DB::transaction(function () use ($issue) {
            // Re-check under lock
            $issue->lockForUpdate();
            $issue->refresh();
            $outstanding = $issue->outstandingQuantity();

            if ($this->returned_quantity > $outstanding) {
                $this->addError('returned_quantity', "Cannot return more than outstanding quantity ($outstanding).");
                return;
            }

            $return = ReturnRecord::create([
                'issue_record_id' => $issue->id,
                'inventory_item_id' => $issue->inventory_item_id,
                'asset_unit_id' => $issue->asset_unit_id,
                'department_id' => $issue->department_id,
                'staff_directory_id' => $issue->staff_directory_id,
                'staff_name_snapshot' => $issue->staff_name_snapshot,
                'returned_quantity' => $this->returned_quantity,
                'return_condition' => $this->return_condition,
                'received_by_user_id' => auth()->id(),
                'returned_at' => now(),
                'note' => $this->note ?: null,
            ]);

            $issue->increment('returned_quantity', $this->returned_quantity);

            $item = InventoryItem::lockForUpdate()->findOrFail($issue->inventory_item_id);
            $item->increment('quantity_available', $this->returned_quantity);
            $item->decrement('quantity_issued', $this->returned_quantity);

            if ($issue->asset_unit_id && $issue->fresh()->outstandingQuantity() <= 0) {
                AssetUnit::where('id', $issue->asset_unit_id)
                    ->update(['unit_status' => UnitStatus::Available, 'assigned_staff_name_snapshot' => null]);
            }

            AuditLogger::log('item_returned', ReturnRecord::class, $return->id, null, [
                'item' => $item->item_name, 'qty' => $this->returned_quantity,
            ]);

            session()->flash('success', 'Return recorded successfully.');
        });

        $this->clearFormState();
        return $this->redirect(route('returns.index'), navigate: true);
    }

    public function render()
    {
        $issueRecords = IssueRecord::with(['inventoryItem', 'department'])
            ->whereRaw('quantity > returned_quantity')
            ->latest('issued_at')
            ->get();

        return view('livewire.returns.create', [
            'issueRecords' => $issueRecords,
        ])->layout('layouts.app');
    }
}
