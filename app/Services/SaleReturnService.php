<?php

namespace App\Services;

use App\Models\RefundPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleReturnService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function createReturn(Sale $sale, array $data, int $userId): SaleReturn
    {
        if ($sale->status === 'cancelled') {
            throw new RuntimeException('Cancelled sales cannot be returned.');
        }

        return DB::transaction(function () use ($sale, $data, $userId) {
            $sale = Sale::with('items')->lockForUpdate()->findOrFail($sale->id);
            $returnItems = $this->buildReturnItems($sale, $data['items']);

            if (empty($returnItems)) {
                throw new RuntimeException('Please enter at least one return quantity.');
            }

            $totalAmount = array_sum(array_column($returnItems, 'total'));

            $saleReturn = SaleReturn::create([
                'sale_id' => $sale->id,
                'return_no' => $this->nextReturnNo(),
                'branch_id' => $sale->branch_id,
                'return_date' => now(),
                'total_amount' => $totalAmount,
                'reason' => $data['reason'] ?? null,
                'status' => 'completed',
                'created_by' => $userId,
            ]);

            foreach ($returnItems as $item) {
                $saleReturn->items()->create([
                    'sale_item_id' => $item['sale_item_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);

                $this->inventoryService->increase($sale->branch_id, $sale->warehouse_id, $item['product_id'], $item['quantity']);

                StockMovement::create([
                    'branch_id' => $sale->branch_id,
                    'warehouse_id' => $sale->warehouse_id,
                    'product_id' => $item['product_id'],
                    'type' => 'sale_return',
                    'quantity' => $item['quantity'],
                    'reference_type' => SaleReturn::class,
                    'reference_id' => $saleReturn->id,
                    'note' => $data['reason'] ?? null,
                    'created_by' => $userId,
                ]);
            }

            RefundPayment::create([
                'sale_return_id' => $saleReturn->id,
                'payment_method' => $data['refund_method'],
                'amount' => $totalAmount,
                'reference_no' => $data['reference_no'] ?? null,
                'refunded_at' => now(),
                'created_by' => $userId,
            ]);

            $this->updateSaleTotals($sale, $totalAmount);

            return $saleReturn->load(['sale', 'items.product', 'refundPayments.creator', 'creator']);
        });
    }

    public function returnableItems(Sale $sale)
    {
        $returned = SaleReturnItem::query()
            ->selectRaw('sale_item_id, SUM(quantity) as returned_quantity')
            ->whereHas('saleReturn', fn ($query) => $query->where('sale_id', $sale->id)->where('status', 'completed'))
            ->groupBy('sale_item_id')
            ->pluck('returned_quantity', 'sale_item_id');

        return $sale->items->map(function (SaleItem $item) use ($returned) {
            $returnedQuantity = (float) ($returned[$item->id] ?? 0);
            $soldQuantity = (float) $item->quantity;

            return [
                'item' => $item,
                'sold_quantity' => $soldQuantity,
                'returned_quantity' => $returnedQuantity,
                'returnable_quantity' => max($soldQuantity - $returnedQuantity, 0),
            ];
        });
    }

    private function buildReturnItems(Sale $sale, array $requestItems): array
    {
        $sale->loadMissing('items');
        $saleItems = $sale->items->keyBy('id');
        $returnableItems = $this->returnableItems($sale)->keyBy(fn ($row) => $row['item']->id);
        $items = [];

        foreach ($requestItems as $requestItem) {
            $quantity = (float) ($requestItem['quantity'] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $saleItem = $saleItems->get((int) $requestItem['sale_item_id']);

            if (! $saleItem) {
                throw new RuntimeException('Invalid sale item selected.');
            }

            $returnableQuantity = (float) $returnableItems[$saleItem->id]['returnable_quantity'];

            if ($quantity > $returnableQuantity) {
                throw new RuntimeException('Return quantity exceeds returnable quantity.');
            }

            $items[] = [
                'sale_item_id' => $saleItem->id,
                'product_id' => $saleItem->product_id,
                'quantity' => $quantity,
                'unit_price' => (float) $saleItem->unit_price,
                'total' => $quantity * (float) $saleItem->unit_price,
            ];
        }

        return $items;
    }

    private function updateSaleTotals(Sale $sale, float $returnAmount): void
    {
        $sale->subtotal = max((float) $sale->subtotal - $returnAmount, 0);
        $sale->total = max((float) $sale->total - $returnAmount, 0);
        $sale->paid_amount = min((float) $sale->paid_amount, (float) $sale->total);
        $sale->due_amount = max((float) $sale->total - (float) $sale->paid_amount, 0);
        $sale->status = (float) $sale->due_amount > 0 ? 'partial' : 'completed';
        $sale->save();
    }

    private function nextReturnNo(): string
    {
        $prefix = 'RET-'.now()->format('Ymd').'-';
        $count = SaleReturn::where('return_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
