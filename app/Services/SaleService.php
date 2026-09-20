<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function checkout(array $data, int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {
            $items = $this->buildItems($data['items']);
            $subtotal = array_sum(array_column($items, 'line_total'));
            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $total = max($subtotal - $discount + $tax, 0);
            $payments = collect($data['payments'] ?? [])
                ->filter(fn ($payment) => (float) ($payment['amount'] ?? 0) > 0)
                ->values();

            $nonCashTendered = (float) $payments
                ->where('payment_method', '!=', 'cash')
                ->sum(fn ($payment) => (float) $payment['amount']);
            $cashTendered = (float) $payments
                ->where('payment_method', 'cash')
                ->sum(fn ($payment) => (float) $payment['amount']);

            if ($nonCashTendered > $total) {
                throw new RuntimeException('Non-cash payments cannot exceed sale total.');
            }

            $tendered = $nonCashTendered + $cashTendered;
            $paidAmount = min($tendered, $total);
            $dueAmount = $total - $paidAmount;
            $change = max($tendered - $total, 0);

            if ($dueAmount > 0 && empty($data['customer_id'])) {
                throw new RuntimeException('Customer is required when there is a due amount.');
            }

            $sale = Sale::create([
                'branch_id' => $data['branch_id'],
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'invoice_no' => $this->nextInvoiceNo(),
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $dueAmount > 0 ? 'partial' : 'completed',
                'created_by' => $userId,
            ]);

            foreach ($items as $item) {
                $this->inventoryService->decrease($data['branch_id'], $data['warehouse_id'] ?? null, $item['product_id'], $item['quantity'], true);

                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost' => $item['unit_cost'],
                    'discount' => 0,
                    'tax' => 0,
                    'total' => $item['line_total'],
                ]);

                StockMovement::create([
                    'branch_id' => $data['branch_id'],
                    'warehouse_id' => $data['warehouse_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => 'sale',
                    'quantity' => -1 * $item['quantity'],
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'note' => null,
                    'created_by' => $userId,
                ]);
            }

            $this->storePayments($sale, $payments, $total, $userId);

            return [
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'total' => (float) $sale->total,
                'paid' => (float) $sale->paid_amount,
                'due' => (float) $sale->due_amount,
                'change' => $change,
            ];
        });
    }

    private function buildItems(array $requestItems): array
    {
        $productIds = collect($requestItems)->pluck('product_id')->unique()->values();
        $products = Product::whereIn('id', $productIds)->where('status', true)->get()->keyBy('id');

        return collect($requestItems)->map(function (array $item) use ($products) {
            $product = $products->get((int) $item['product_id']);

            if (! $product) {
                throw new RuntimeException('Selected product is not available.');
            }

            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $product->selling_price;

            return [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => (float) $product->cost_price,
                'line_total' => $quantity * $unitPrice,
            ];
        })->all();
    }

    private function storePayments(Sale $sale, $payments, float $total, int $userId): void
    {
        $nonCashPayments = $payments->where('payment_method', '!=', 'cash');
        $cashPayments = $payments->where('payment_method', 'cash');
        $remaining = $total;

        foreach ($nonCashPayments as $payment) {
            $amount = (float) $payment['amount'];
            if ($amount <= 0) {
                continue;
            }

            Payment::create([
                'sale_id' => $sale->id,
                'payment_method' => $payment['payment_method'],
                'amount' => $amount,
                'reference_no' => null,
                'paid_at' => now(),
                'received_by' => $userId,
                'note' => null,
            ]);

            $remaining -= $amount;
        }

        foreach ($cashPayments as $payment) {
            $amount = min((float) $payment['amount'], max($remaining, 0));
            if ($amount <= 0) {
                continue;
            }

            Payment::create([
                'sale_id' => $sale->id,
                'payment_method' => 'cash',
                'amount' => $amount,
                'reference_no' => null,
                'paid_at' => now(),
                'received_by' => $userId,
                'note' => null,
            ]);

            $remaining -= $amount;
        }
    }

    private function nextInvoiceNo(): string
    {
        $prefix = 'SAL-'.now()->format('Ymd').'-';
        $count = Sale::where('invoice_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
