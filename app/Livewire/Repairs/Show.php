<?php

namespace App\Livewire\Repairs;

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

        $old = $this->repairRecord->status;
        $data = ['status' => $status];

        if ($status === 'sent_for_repair') $data['sent_date'] = now();
        if ($status === 'returned' || $status === 'repaired') $data['return_date'] = now();

        $this->repairRecord->update($data);
        AuditLogger::log('repair_status_updated', RepairRecord::class, $this->repairRecord->id, ['status' => $old], ['status' => $status]);

        session()->flash('success', 'Status updated.');
    }

    public function render()
    {
        return view('livewire.repairs.show')->layout('layouts.app');
    }
}
