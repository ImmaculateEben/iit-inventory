<?php

namespace App\Livewire\Issues;

use App\Enums\UnitStatus;
use App\Models\AssetUnit;
use App\Models\InventoryItem;
use App\Models\IssueRecord;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['action_type', 'inventory_item_id', 'department_id', 'staff_name', 'quantity', 'asset_unit_ids', 'note'];
    }

    // Action type toggle
    public string $action_type = 'issue';

    public string $inventory_item_id = '';
    public string $department_id = '';
    public string $staff_name = '';
    public int $quantity = 1;
    public array $asset_unit_ids = [];
    public string $note = '';

    // Search / UI state
    public string $itemSearch = '';
    public string $staffSearch = '';
    public bool $showItemDropdown = false;
    public bool $showStaffDropdown = false;
    public int $maxQuantity = 0;
    public string $selectedItemLabel = '';
    public string $selectedTrackingMethod = '';

    protected function rules(): array
    {
        $rules = [
            'action_type' => 'required|in:issue,assign',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'department_id' => 'required|exists:departments,id',
            'staff_name' => 'required|string|max:200',
            'note' => 'nullable|string|max:500',
        ];

        if ($this->action_type === 'assign') {
            $rules['asset_unit_ids'] = 'required|array|min:1';
            $rules['asset_unit_ids.*'] = 'exists:asset_units,id';
        } else {
            $rules['quantity'] = 'required|integer|min:1';
        }

        return $rules;
    }

    protected $messages = [
        'staff_name.required' => 'Enter the name of the person receiving the item.',
        'asset_unit_ids.required' => 'Please select at least one asset unit to assign.',
        'asset_unit_ids.min' => 'Please select at least one asset unit to assign.',
    ];

    public function updatedActionType(): void
    {
        $this->clearItem();
    }

    public function updatedItemSearch(): void
    {
        $this->showItemDropdown = strlen($this->itemSearch) > 0;
    }

    public function selectItem(int $id): void
    {
        $item = InventoryItem::find($id);
        if (!$item || !auth()->user()->canAccessItem($item)) return;

        $this->inventory_item_id = (string) $item->id;
        $this->department_id = (string) $item->department_id;
        $this->selectedTrackingMethod = $item->tracking_method->value ?? $item->tracking_method;

        if ($this->action_type === 'issue') {
            $this->maxQuantity = $item->quantity_available;
            $label = ($item->item_code ? $item->item_code . ' — ' : '') . $item->item_name . ' (' . $item->quantity_available . ' available)';
        } else {
            $availableUnits = $item->assetUnits()->where('unit_status', 'available')->count();
            $this->maxQuantity = $availableUnits;
            $label = ($item->item_code ? $item->item_code . ' — ' : '') . $item->item_name . ' (' . $availableUnits . ' units available)';
        }

        $this->selectedItemLabel = $label;
        $this->itemSearch = $label;
        $this->showItemDropdown = false;
        $this->quantity = 1;
        $this->asset_unit_ids = [];
    }

    public function clearItem(): void
    {
        $this->inventory_item_id = '';
        $this->itemSearch = '';
        $this->selectedItemLabel = '';
        $this->selectedTrackingMethod = '';
        $this->maxQuantity = 0;
        $this->quantity = 1;
        $this->asset_unit_ids = [];
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

        // Enforce department/category access boundary
        abort_unless(auth()->user()->canAccessItem($item), 403, 'You do not have access to this item.');

        // Use the item's actual department_id to prevent IDOR
        $departmentId = $item->department_id;

        $saved = false;

        DB::transaction(function () use ($item, $departmentId, &$saved) {
            if ($this->action_type === 'assign') {
                $units = AssetUnit::whereIn('id', $this->asset_unit_ids)
                    ->where('inventory_item_id', $item->id)
                    ->where('unit_status', 'available')
                    ->lockForUpdate()
                    ->get();

                if ($units->count() !== count($this->asset_unit_ids)) {
                    $this->addError('asset_unit_ids', 'Some selected units are no longer available. Please refresh and try again.');
                    return;
                }

                foreach ($units as $unit) {
                    $issue = IssueRecord::forceCreate([
                        'issue_number' => IssueRecord::generateNumber(),
                        'action_type' => 'assign',
                        'inventory_item_id' => $item->id,
                        'department_id' => $departmentId,
                        'asset_unit_id' => $unit->id,
                        'staff_name_snapshot' => $this->staff_name,
                        'quantity' => 1,
                        'issued_by_user_id' => auth()->id(),
                        'issued_at' => now(),
                        'note' => $this->note ?: null,
                    ]);

                    $unit->update([
                        'unit_status' => UnitStatus::Issued,
                        'assigned_staff_name_snapshot' => $this->staff_name,
                    ]);

                    AuditLogger::log('item_assigned', IssueRecord::class, $issue->id, null, [
                        'item' => $item->item_name, 'unit' => $unit->asset_tag ?? $unit->serial_number, 'to' => $this->staff_name,
                    ]);
                }

                $count = $units->count();
                session()->flash('success', $count . ' unit' . ($count > 1 ? 's' : '') . ' assigned successfully.');
                $saved = true;
            } else {
                // Re-read with lock to prevent race condition
                $item = InventoryItem::lockForUpdate()->findOrFail($item->id);

                if ($item->quantity_available < $this->quantity) {
                    $this->addError('quantity', 'Not enough stock. Only ' . $item->quantity_available . ' available.');
                    return;
                }

                $issue = IssueRecord::forceCreate([
                    'issue_number' => IssueRecord::generateNumber(),
                    'action_type' => 'issue',
                    'inventory_item_id' => $item->id,
                    'department_id' => $departmentId,
                    'staff_name_snapshot' => $this->staff_name,
                    'quantity' => $this->quantity,
                    'asset_unit_id' => null,
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
                $saved = true;
            }
        });

        if (!$saved) {
            return;
        }

        $this->clearFormState();
        return $this->redirect(route('issues.index'), navigate: true);
    }

    public function render()
    {
        $items = collect();
        if (strlen($this->itemSearch) > 0 && !$this->inventory_item_id) {
            $user = auth()->user();
            $query = $user->scopeInventoryItems(
                InventoryItem::where('is_active', true)
                    ->where(function ($q) {
                        $q->where('item_name', 'like', '%' . $this->itemSearch . '%')
                          ->orWhere('item_code', 'like', '%' . $this->itemSearch . '%');
                    })
            );

            if ($this->action_type === 'assign') {
                // For assign, show items with individual tracking that have available units
                $query->where('tracking_method', 'individual')
                    ->whereHas('assetUnits', fn($q) => $q->where('unit_status', 'available'));
                $items = $query->withCount(['assetUnits as available_units_count' => fn($q) => $q->where('unit_status', 'available')])
                    ->orderBy('item_name')->limit(15)->get();
            } else {
                $query->where('quantity_available', '>', 0);
                $items = $query->orderBy('item_name')->limit(15)->get();
            }
        }

        // Available asset units for the selected item (assign mode)
        $availableUnits = collect();
        if ($this->action_type === 'assign' && $this->inventory_item_id) {
            $availableUnits = AssetUnit::where('inventory_item_id', $this->inventory_item_id)
                ->where('unit_status', 'available')
                ->orderBy('asset_tag')
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
            'availableUnits' => $availableUnits,
        ])->layout('layouts.app');
    }
}
