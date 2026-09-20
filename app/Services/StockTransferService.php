<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function createCompleted(array $data, int $userId): StockTransfer
    {
        return DB::transaction(function () use ($data, $userId) {
            $transfer = StockTransfer::create([
                'transfer_no' => $this->nextTransferNo(),
                'from_branch_id' => $data['from_branch_id'],
                'to_branch_id' => $data['to_branch_id'],
                'from_warehouse_id' => $data['from_warehouse_id'] ?? null,
                'to_warehouse_id' => $data['to_warehouse_id'] ?? null,
                'status' => 'completed',
                'transfer_date' => $data['transfer_date'],
                'note' => $data['note'] ?? null,
                'created_by' => $userId,
            ]);

            foreach ($data['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $product = Product::findOrFail($item['product_id']);

                $this->inventoryService->decrease($data['from_branch_id'], $data['from_warehouse_id'] ?? null, $item['product_id'], $quantity, true);
                $this->inventoryService->increase($data['to_branch_id'], $data['to_warehouse_id'] ?? null, $item['product_id'], $quantity);

                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $product->cost_price,
                ]);

                StockMovement::create([
                    'branch_id' => $data['from_branch_id'],
                    'warehouse_id' => $data['from_warehouse_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => 'transfer_out',
                    'quantity' => $quantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'note' => $data['note'] ?? null,
                    'created_by' => $userId,
                ]);

                StockMovement::create([
                    'branch_id' => $data['to_branch_id'],
                    'warehouse_id' => $data['to_warehouse_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => 'transfer_in',
                    'quantity' => $quantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'note' => $data['note'] ?? null,
                    'created_by' => $userId,
                ]);
            }

            return $transfer->load(['items.product', 'fromBranch', 'toBranch', 'fromWarehouse', 'toWarehouse', 'creator']);
        });
    }

    private function nextTransferNo(): string
    {
        $prefix = 'TRF-'.now()->format('Ymd').'-';
        $count = StockTransfer::where('transfer_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
