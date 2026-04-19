<?php

namespace App\Livewire\Repairs;

use App\Enums\RepairStatus;
use App\Models\RepairRecord;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Show extends Component
{
    public RepairRecord $repairRecord;

    public function mount(RepairRecord $repairRecord): void
    {
        $this->repairRecord = $repairRecord->load(['inventoryItem', 'assetUnit', 'createdBy']);

        // Enforce department/category access boundary
        if ($repairRecord->inventoryItem) {
            abort_unless(auth()->user()->canAccessItem($repairRecord->inventoryItem), 403, 'You do not have access to this item.');
        }
    }

    public function updateStatus(string $status): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('manage_repairs')) {
            abort(403);
        }

        // Verify user can access the underlying inventory item
        if ($this->repairRecord->inventoryItem) {
            abort_unless($user->canAccessItem($this->repairRecord->inventoryItem), 403, 'You do not have access to this item.');
        }

        $targetStatus = RepairStatus::tryFrom($status);
        abort_unless($targetStatus, 422, 'Invalid repair status.');

        $currentStatus = $this->repairRecord->status;
        abort_unless($currentStatus->canTransitionTo($targetStatus), 422,
            "Cannot transition from '{$currentStatus->label()}' to '{$targetStatus->label()}'.");

        $old = $currentStatus;
        $data = ['status' => $status];

        if ($targetStatus === RepairStatus::SentForRepair) $data['date_sent'] = now();
        if ($targetStatus === RepairStatus::Returned || $targetStatus === RepairStatus::Repaired) $data['date_returned'] = now();

        $this->repairRecord->update($data);
        AuditLogger::log('repair_status_updated', RepairRecord::class, $this->repairRecord->id, ['status' => $old->value], ['status' => $status]);

        session()->flash('success', 'Status updated.');
    }

    public function render()
    {
        return view('livewire.repairs.show')->layout('layouts.app');
    }
}
