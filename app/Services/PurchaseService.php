<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function createDraft(array $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $totals = $this->totals($data['items']);

            $purchase = Purchase::create(array_merge($totals, [
                'supplier_id' => $data['supplier_id'],
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'purchase_no' => $this->nextPurchaseNo(),
                'purchase_date' => $data['purchase_date'],
                'paid_amount' => 0,
                'due_amount' => $totals['total'],
                'status' => 'draft',
                'note' => $data['note'] ?? null,
                'created_by' => $userId,
            ]));

            $this->syncItems($purchase, $data['items']);

            return $purchase;
        });
    }

    public function updateDraft(Purchase $purchase, array $data): Purchase
    {
        if ($purchase->status !== 'draft') {
            throw new RuntimeException('Only draft purchases can be edited.');
        }

        return DB::transaction(function () use ($purchase, $data) {
            $totals = $this->totals($data['items']);

            $purchase->update(array_merge($totals, [
                'supplier_id' => $data['supplier_id'],
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'due_amount' => $totals['total'] - (float) $purchase->paid_amount,
                'note' => $data['note'] ?? null,
            ]));

            $purchase->items()->delete();
            $this->syncItems($purchase, $data['items']);

            return $purchase;
        });
    }

    public function receive(Purchase $purchase): Purchase
    {
        if ($purchase->status !== 'draft') {
            throw new RuntimeException('Only draft purchases can be received.');
        }

        return DB::transaction(function () use ($purchase) {
            $purchase = Purchase::with('items')->lockForUpdate()->findOrFail($purchase->id);

            if ($purchase->status !== 'draft') {
                throw new RuntimeException('This purchase has already been processed.');
            }

            foreach ($purchase->items as $item) {
                $this->inventoryService->increase($purchase->branch_id, $purchase->warehouse_id, $item->product_id, (float) $item->quantity);

                StockMovement::create([
                    'branch_id' => $purchase->branch_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'product_id' => $item->product_id,
                    'type' => 'purchase',
                    'quantity' => $item->quantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'note' => $purchase->note,
                    'created_by' => $purchase->created_by,
                ]);
            }

            $purchase->update(['status' => 'received']);

            return $purchase;
        });
    }

    public function addPayment(Purchase $purchase, array $data, int $userId): PurchasePayment
    {
        if ($purchase->status === 'cancelled') {
            throw new RuntimeException('Cancelled purchases cannot receive payments.');
        }

        return DB::transaction(function () use ($purchase, $data, $userId) {
            $purchase = Purchase::lockForUpdate()->findOrFail($purchase->id);
            $amount = (float) $data['amount'];

            if (((float) $purchase->paid_amount + $amount) > (float) $purchase->total) {
                throw new RuntimeException('Payment cannot exceed purchase total.');
            }

            $payment = PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'payment_method' => $data['payment_method'],
                'amount' => $amount,
                'reference_no' => $data['reference_no'] ?? null,
                'paid_at' => $data['paid_at'],
                'created_by' => $userId,
            ]);

            $purchase->paid_amount = (float) $purchase->paid_amount + $amount;
            $purchase->due_amount = (float) $purchase->total - (float) $purchase->paid_amount;
            $purchase->save();

            return $payment;
        });
    }

    public function cancel(Purchase $purchase): void
    {
        if ($purchase->status === 'received') {
            throw new RuntimeException('Received purchases cannot be cancelled.');
        }

        $purchase->update(['status' => 'cancelled']);
    }

    private function syncItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitCost = (float) $item['unit_cost'];
            $discount = (float) ($item['discount'] ?? 0);
            $tax = (float) ($item['tax'] ?? 0);

            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'discount' => $discount,
                'tax' => $tax,
                'total' => ($quantity * $unitCost) - $discount + $tax,
            ]);
        }
    }

    private function totals(array $items): array
    {
        $subtotal = $discount = $tax = 0;

        foreach ($items as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['unit_cost'];
            $discount += (float) ($item['discount'] ?? 0);
            $tax += (float) ($item['tax'] ?? 0);
        }

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $subtotal - $discount + $tax,
        ];
    }

    private function nextPurchaseNo(): string
    {
        $prefix = 'PUR-'.now()->format('Ymd').'-';
        $count = Purchase::where('purchase_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
