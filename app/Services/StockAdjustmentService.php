<?php

namespace App\Services;

use App\Models\StockAdjustment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function createCompleted(array $data, int $userId): StockAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            $adjustment = StockAdjustment::create([
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'adjustment_no' => $this->nextAdjustmentNo(),
                'type' => $data['type'],
                'reason' => $data['reason'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $userId,
                'status' => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                $quantity = (float) $item['quantity'];

                $adjustment->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $item['unit_cost'] ?? 0,
                ]);

                if ($data['type'] === 'increase') {
                    $this->inventoryService->increase($data['branch_id'], $data['warehouse_id'] ?? null, $item['product_id'], $quantity);
                    $movementType = 'adjustment_in';
                } else {
                    $this->inventoryService->decrease($data['branch_id'], $data['warehouse_id'] ?? null, $item['product_id'], $quantity);
                    $movementType = 'adjustment_out';
                }

                StockMovement::create([
                    'branch_id' => $data['branch_id'],
                    'warehouse_id' => $data['warehouse_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => $movementType,
                    'quantity' => $quantity,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'note' => $data['note'] ?? null,
                    'created_by' => $userId,
                ]);
            }

            return $adjustment->load(['items.product', 'branch', 'warehouse', 'creator']);
        });
    }

    private function nextAdjustmentNo(): string
    {
        $prefix = 'ADJ-'.now()->format('Ymd').'-';
        $count = StockAdjustment::where('adjustment_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
