<?php

namespace App\Services;

use App\Models\Inventory;
use RuntimeException;

class InventoryService
{
    public function increase(int $branchId, ?int $warehouseId, int $productId, float $quantity): Inventory
    {
        $inventory = $this->query($branchId, $warehouseId, $productId)->lockForUpdate()->first();

        if (! $inventory) {
            $inventory = new Inventory([
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);
        }

        $inventory->quantity = (float) $inventory->quantity + $quantity;
        $inventory->save();

        return $inventory;
    }

    public function decrease(int $branchId, ?int $warehouseId, int $productId, float $quantity, bool $useAvailable = false): Inventory
    {
        $inventory = $this->query($branchId, $warehouseId, $productId)->lockForUpdate()->first();

        if (! $inventory) {
            throw new RuntimeException('Inventory record was not found for the selected product.');
        }

        $currentQuantity = (float) $inventory->quantity;
        $availableQuantity = $currentQuantity - (float) $inventory->reserved_quantity;
        $allowedQuantity = $useAvailable ? $availableQuantity : $currentQuantity;

        if ($quantity > $allowedQuantity) {
            throw new RuntimeException('Quantity exceeds available stock.');
        }

        $inventory->quantity = $currentQuantity - $quantity;
        $inventory->save();

        return $inventory;
    }

    private function query(int $branchId, ?int $warehouseId, int $productId)
    {
        return Inventory::where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId), fn ($query) => $query->whereNull('warehouse_id'));
    }
}
