<?php

namespace App\Services;

use App\Models\AssetUnit;
use App\Models\InventoryItem;
use App\Models\SystemSetting;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Apply user access scoping to an inventory query.
     */
    private function applyAccessScope($query, ?array $deptIds, ?array $catIds)
    {
        if ($deptIds !== null) {
            $query->whereIn('department_id', $deptIds);
        }
        if ($catIds !== null) {
            $query->whereIn('category_id', $catIds);
        }
        return $query;
    }

    public function getMetrics(?array $deptIds = null, ?array $catIds = null): array
    {
        $itemQuery = InventoryItem::active();
        $this->applyAccessScope($itemQuery, $deptIds, $catIds);

        $totalItems = (clone $itemQuery)->count();
        $consumablesCount = (clone $itemQuery)->where('item_type', 'consumable')->count();
        $assetsCount = (clone $itemQuery)->where('item_type', 'asset')->count();

        $lowStockItems = $this->getLowStockCount($deptIds, $catIds);

        $quantityIssued = (clone $itemQuery)->quantityTracked()->sum('quantity_issued') ?? 0;

        $unitIssuedQuery = AssetUnit::active()->where('unit_status', 'issued');
        if ($deptIds !== null || $catIds !== null) {
            $unitIssuedQuery->whereHas('inventoryItem', function ($q) use ($deptIds, $catIds) {
                $this->applyAccessScope($q, $deptIds, $catIds);
            });
        }
        $individualIssued = $unitIssuedQuery->count();
        $issuedItems = (int) $quantityIssued + $individualIssued;

        $quantityUnderRepair = (clone $itemQuery)->quantityTracked()->sum('quantity_under_repair') ?? 0;
        $unitRepairQuery = AssetUnit::active()->where('unit_status', 'under_repair');
        if ($deptIds !== null || $catIds !== null) {
            $unitRepairQuery->whereHas('inventoryItem', function ($q) use ($deptIds, $catIds) {
                $this->applyAccessScope($q, $deptIds, $catIds);
            });
        }
        $individualUnderRepair = $unitRepairQuery->count();
        $itemsUnderRepair = (int) $quantityUnderRepair + $individualUnderRepair;

        // Available items: quantity-tracked with quantity_available > 0, plus individual-tracked units with status 'available'
        $availableQuantityItems = (clone $itemQuery)->quantityTracked()->where('quantity_available', '>', 0)->count();
        $availableUnitQuery = AssetUnit::active()->where('unit_status', 'available');
        if ($deptIds !== null || $catIds !== null) {
            $availableUnitQuery->whereHas('inventoryItem', function ($q) use ($deptIds, $catIds) {
                $this->applyAccessScope($q, $deptIds, $catIds);
            });
        }
        $totalAvailableUnits = $availableUnitQuery->count();
        $totalAvailableQty = (int) ((clone $itemQuery)->quantityTracked()->sum('quantity_available') ?? 0) + $totalAvailableUnits;

        // Out of stock: quantity-tracked items with quantity_available = 0
        $outOfStockItems = (clone $itemQuery)->quantityTracked()->where('quantity_available', 0)->count();

        // Individual-tracked items with zero available units
        $individualItemsOutOfStock = (clone $itemQuery)->individualTracked()
            ->whereDoesntHave('assetUnits', function ($q) {
                $q->whereNull('archived_at')->where('unit_status', 'available');
            })->count();

        return [
            'total_items' => $totalItems,
            'consumables_count' => $consumablesCount,
            'assets_count' => $assetsCount,
            'low_stock_items' => $lowStockItems,
            'issued_items' => $issuedItems,
            'items_under_repair' => $itemsUnderRepair,
            'total_available_qty' => $totalAvailableQty,
            'out_of_stock_items' => $outOfStockItems + $individualItemsOutOfStock,
        ];
    }

    public function getLowStockAlerts(?array $deptIds = null, ?array $catIds = null, int $limit = 10): Collection
    {
        $threshold = SystemSetting::getValue('inventory.low_stock_default_threshold', 10);

        $query = InventoryItem::active()
            ->quantityTracked()
            ->with(['department', 'category'])
            ->where(function ($q) use ($threshold) {
                $q->whereRaw('quantity_available <= COALESCE(low_stock_threshold, ?)', [$threshold]);
            });

        $this->applyAccessScope($query, $deptIds, $catIds);

        return $query
            ->orderByRaw('quantity_available - COALESCE(low_stock_threshold, ?) ASC', [$threshold])
            ->limit($limit)
            ->get()
            ->map(function ($item) use ($threshold) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'department_name' => $item->department?->name ?? '—',
                    'category_name' => $item->category?->name ?? '—',
                    'quantity_available' => $item->quantity_available ?? 0,
                    'effective_threshold' => $item->low_stock_threshold ?? $threshold,
                ];
            });
    }

    private function getLowStockCount(?array $deptIds = null, ?array $catIds = null): int
    {
        $threshold = SystemSetting::getValue('inventory.low_stock_default_threshold', 10);

        $query = InventoryItem::active()
            ->quantityTracked()
            ->where(function ($q) use ($threshold) {
                $q->whereRaw('quantity_available <= COALESCE(low_stock_threshold, ?)', [$threshold]);
            });

        $this->applyAccessScope($query, $deptIds, $catIds);

        return $query->count();
    }
}
