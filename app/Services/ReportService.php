<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\RefundPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockAdjustmentItem;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public const REPORTS = [
        'profit-loss' => 'Profit / Loss Report',
        'purchase-sale' => 'Purchase & Sale',
        'tax' => 'Tax Report',
        'supplier-customer' => 'Supplier & Customer Report',
        'customer-groups' => 'Customer Groups Report',
        'stock' => 'Stock Report',
        'stock-adjustment' => 'Stock Adjustment Report',
        'trending-products' => 'Trending Products',
        'items' => 'Items Report',
        'product-purchase' => 'Product Purchase Report',
        'product-sell' => 'Product Sell Report',
        'purchase-payment' => 'Purchase Payment Report',
        'sell-payment' => 'Sell Payment Report',
        'expense' => 'Expense Report',
        'register' => 'Register Report',
        'sales-representative' => 'Sales Representative Report',
        'activity-log' => 'Activity Log',
    ];

    public function dashboard(array $filters = []): array
    {
        $today = today();
        $branchId = $filters['branch_id'] ?? null;
        $date = $filters['date'] ?? null;
        $day = $date ? Carbon::parse($date) : $today;

        $sales = Sale::query()->when($branchId, fn ($q) => $q->where('branch_id', $branchId));
        $purchases = Purchase::query()->when($branchId, fn ($q) => $q->where('branch_id', $branchId));
        $expenses = Expense::query()->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $todaySales = (clone $sales)->whereDate('sale_date', $day)->sum('total');
        $todayPurchases = (clone $purchases)->whereDate('purchase_date', $day)->sum('total');
        $todayExpenses = (clone $expenses)->whereDate('expense_date', $day)->sum('amount');
        $todayCogs = SaleItem::whereHas('sale', fn ($q) => $q->whereDate('sale_date', $day)->when($branchId, fn ($qq) => $qq->where('branch_id', $branchId)))
            ->selectRaw('COALESCE(SUM(quantity * unit_cost),0) as total')->value('total');

        $chart = (clone $sales)
            ->whereDate('sale_date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('DATE(sale_date) as label, SUM(total) as total')
            ->groupByRaw('DATE(sale_date)')
            ->orderBy('label')
            ->get();

        return [
            'branches' => Branch::orderBy('name')->get(),
            'filters' => $filters,
            'cards' => [
                "Today's Sales" => $todaySales,
                "Today's Purchases" => $todayPurchases,
                "Today's Expenses" => $todayExpenses,
                "Today's Profit" => $todaySales - $todayCogs - $todayExpenses,
                'Total Sales' => (clone $sales)->sum('total'),
                'Total Purchases' => (clone $purchases)->sum('total'),
                'Sales Due' => (clone $sales)->sum('due_amount'),
                'Purchase Due' => (clone $purchases)->sum('due_amount'),
                'Total Customers' => Customer::count(),
                'Total Suppliers' => Supplier::count(),
                'Total Products' => Product::count(),
                'Low Stock Products' => $this->lowStockQuery($branchId)->count(),
            ],
            'salesChart' => $chart,
            'recentSales' => Sale::with(['customer', 'branch'])->when($branchId, fn ($q) => $q->where('branch_id', $branchId))->latest('sale_date')->limit(10)->get(),
            'lowStockProducts' => $this->lowStockQuery($branchId)->limit(10)->get(),
            'topProducts' => SaleItem::with('product')->selectRaw('product_id, SUM(quantity) as qty, SUM(total) as total')->groupBy('product_id')->orderByDesc('qty')->limit(5)->get(),
            'paymentMethods' => Payment::selectRaw('payment_method, SUM(amount) as total')->groupBy('payment_method')->orderBy('payment_method')->get(),
        ];
    }

    public function report(string $slug, Request $request): array
    {
        [$from, $to] = $this->dateRange($request);
        $branchId = $request->input('branch_id');
        $base = ['title' => self::REPORTS[$slug] ?? 'Report', 'slug' => $slug, 'filters' => $this->filters(), 'from' => $from, 'to' => $to];

        return array_merge($base, match ($slug) {
            'profit-loss' => $this->profitLoss($from, $to, $branchId),
            'purchase-sale' => $this->purchaseSale($from, $to, $branchId),
            'tax' => $this->tax($from, $to, $branchId),
            'supplier-customer' => $this->supplierCustomer($from, $to),
            'customer-groups' => $this->customerGroups($from, $to),
            'stock' => $this->stock($request),
            'stock-adjustment' => $this->stockAdjustments($request, $from, $to),
            'trending-products' => $this->trendingProducts($request, $from, $to),
            'items' => $this->items($request, $from, $to),
            'product-purchase' => $this->productPurchase($request, $from, $to),
            'product-sell' => $this->productSell($request, $from, $to),
            'purchase-payment' => $this->purchasePayment($request, $from, $to),
            'sell-payment' => $this->sellPayment($request, $from, $to),
            'expense' => $this->expense($request, $from, $to),
            'register' => $this->register($request, $from, $to),
            'sales-representative' => $this->salesRepresentative($request, $from, $to),
            'activity-log' => $this->activityLog($request, $from, $to),
            default => $this->emptyReport(),
        });
    }

    private function profitLoss(string $from, string $to, $branchId): array
    {
        $grossSales = Sale::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to])->sum('total');
        $returns = SaleReturn::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(return_date)'), [$from, $to])->sum('total_amount');
        $cogs = SaleItem::whereHas('sale', fn ($q) => $q->when($branchId, fn ($qq) => $qq->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to]))->selectRaw('COALESCE(SUM(quantity * unit_cost),0) as total')->value('total');
        $returnCogs = SaleReturnItem::whereHas('saleReturn', fn ($q) => $q->when($branchId, fn ($qq) => $qq->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(return_date)'), [$from, $to]))->join('sale_items', 'sale_items.id', '=', 'sale_return_items.sale_item_id')->selectRaw('COALESCE(SUM(sale_return_items.quantity * sale_items.unit_cost),0) as total')->value('total');
        $expenses = Expense::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween('expense_date', [$from, $to])->sum('amount');
        $netSales = $grossSales - $returns;
        $netCogs = $cogs - $returnCogs;
        return $this->simple(['Metric', 'Amount'], collect([
            ['Gross Sales', $grossSales], ['Sales Returns', $returns], ['Net Sales', $netSales], ['COGS', $netCogs], ['Gross Profit', $netSales - $netCogs], ['Expenses', $expenses], ['Net Profit', $netSales - $netCogs - $expenses],
        ]), ['Net Profit' => $netSales - $netCogs - $expenses]);
    }

    private function purchaseSale(string $from, string $to, $branchId): array
    {
        $sales = Sale::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to])->selectRaw('DATE(sale_date) date, SUM(total) sale_amount, SUM(due_amount) sale_due')->groupByRaw('DATE(sale_date)');
        $purchases = Purchase::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween('purchase_date', [$from, $to])->selectRaw('purchase_date date, SUM(total) purchase_amount, SUM(due_amount) purchase_due')->groupBy('purchase_date')->get()->keyBy('date');
        $returns = SaleReturn::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(return_date)'), [$from, $to])->selectRaw('DATE(return_date) date, SUM(total_amount) total')->groupByRaw('DATE(return_date)')->pluck('total', 'date');
        $rows = $sales->get()->map(fn ($s) => [$s->date, (float)($purchases[$s->date]->purchase_amount ?? 0), (float)$s->sale_amount, (float)($purchases[$s->date]->purchase_due ?? 0), (float)$s->sale_due, (float)($returns[$s->date] ?? 0), (float)$s->sale_amount - (float)($returns[$s->date] ?? 0)]);
        return $this->simple(['Date', 'Purchase Amount', 'Sale Amount', 'Purchase Due', 'Sale Due', 'Sale Return', 'Net Sales'], $rows);
    }

    private function tax(string $from, string $to, $branchId): array
    {
        $salesTax = Sale::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to])->sum('tax');
        $purchaseTax = Purchase::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->whereBetween('purchase_date', [$from, $to])->sum('tax');
        return $this->simple(['Metric', 'Amount'], collect([['Sales Tax', $salesTax], ['Purchase Tax', $purchaseTax], ['Total Tax Collected', $salesTax], ['Total Tax Paid', $purchaseTax], ['Net Tax', $salesTax - $purchaseTax]]));
    }

    private function supplierCustomer(string $from, string $to): array
    {
        $customers = Customer::leftJoin('sales', 'sales.customer_id', '=', 'customers.id')->selectRaw('customers.name, COALESCE(SUM(sales.total),0) total, COALESCE(SUM(sales.paid_amount),0) paid, COALESCE(SUM(sales.due_amount),0) due, COUNT(sales.id) tx')->groupBy('customers.id', 'customers.name')->limit(50)->get()->map(fn ($r) => ['Customer: '.$r->name, $r->total, $r->paid, $r->due, '-', $r->tx]);
        $suppliers = Supplier::leftJoin('purchases', 'purchases.supplier_id', '=', 'suppliers.id')->selectRaw('suppliers.name, COALESCE(SUM(purchases.total),0) total, COALESCE(SUM(purchases.paid_amount),0) paid, COALESCE(SUM(purchases.due_amount),0) due, COUNT(purchases.id) tx')->groupBy('suppliers.id', 'suppliers.name')->limit(50)->get()->map(fn ($r) => ['Supplier: '.$r->name, $r->total, $r->paid, $r->due, '-', $r->tx]);
        return $this->simple(['Name', 'Total', 'Paid', 'Due', 'Returns', 'Transaction Count'], $customers->concat($suppliers));
    }

    private function customerGroups(string $from, string $to): array
    {
        $rows = CustomerGroup::leftJoin('customers', 'customers.customer_group_id', '=', 'customer_groups.id')->leftJoin('sales', 'sales.customer_id', '=', 'customers.id')->selectRaw('customer_groups.name, COUNT(DISTINCT customers.id) customers, COALESCE(SUM(sales.total),0) total, COALESCE(SUM(sales.paid_amount),0) paid, COALESCE(SUM(sales.due_amount),0) due, COALESCE(AVG(sales.total),0) avg_sale')->groupBy('customer_groups.id', 'customer_groups.name')->get()->map(fn ($r) => [$r->name, $r->customers, $r->total, $r->paid, $r->due, $r->avg_sale, '-']);
        return $this->simple(['Customer Group', 'Customers', 'Total Sales', 'Total Paid', 'Total Due', 'Average Sale', 'Returns'], $rows);
    }

    private function stock(Request $request): array
    {
        $rows = Inventory::with(['product.category', 'branch', 'warehouse'])->when($request->branch_id, fn ($q) => $q->where('branch_id', $request->branch_id))->paginate(25)->withQueryString();
        return ['headers' => ['Product', 'SKU', 'Category', 'Branch', 'Warehouse', 'Quantity', 'Reserved', 'Available', 'Minimum Stock', 'Stock Status', 'Inventory Value'], 'rows' => $rows->through(fn ($i) => [$i->product->name, $i->product->sku, $i->product->category?->name, $i->branch->name, $i->warehouse?->name ?? '-', $i->quantity, $i->reserved_quantity, (float)$i->quantity - (float)$i->reserved_quantity, $i->product->minimum_stock, (float)$i->quantity <= 0 ? 'Out of Stock' : ((float)$i->quantity <= (float)$i->product->minimum_stock ? 'Low Stock' : 'In Stock'), (float)$i->quantity * (float)$i->product->cost_price]), 'totals' => []];
    }

    private function stockAdjustments(Request $request, string $from, string $to): array
    {
        $rows = StockAdjustmentItem::with(['stockAdjustment.branch', 'stockAdjustment.warehouse', 'stockAdjustment.creator', 'product'])->whereHas('stockAdjustment', fn ($q) => $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]))->paginate(25)->withQueryString();
        return ['headers' => ['Adjustment No', 'Date', 'Branch', 'Warehouse', 'Type', 'Product', 'Quantity', 'Unit Cost', 'Reason', 'Created By', 'Status'], 'rows' => $rows->through(fn ($i) => [$i->stockAdjustment->adjustment_no, $i->stockAdjustment->created_at->format('Y-m-d'), $i->stockAdjustment->branch->name, $i->stockAdjustment->warehouse?->name ?? '-', $i->stockAdjustment->type, $i->product->name, $i->quantity, $i->unit_cost, $i->stockAdjustment->reason, $i->stockAdjustment->creator?->name, $i->stockAdjustment->status]), 'totals' => []];
    }

    private function trendingProducts(Request $request, string $from, string $to): array
    {
        $rows = SaleItem::with('product')->whereHas('sale', fn ($q) => $q->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to]))->selectRaw('product_id, SUM(quantity) qty, SUM(total) revenue, COUNT(DISTINCT sale_id) tx')->groupBy('product_id')->orderByDesc('qty')->limit(20)->get()->values()->map(fn ($r, $i) => [$i + 1, $r->product->name, $r->product->sku, $r->qty, $r->revenue, $r->tx]);
        return $this->simple(['Rank', 'Product', 'SKU', 'Qty Sold', 'Revenue', 'Transaction Count'], $rows);
    }

    private function items(Request $request, string $from, string $to): array
    {
        $rows = Product::with(['category', 'brand'])->limit(100)->get()->map(function ($p) use ($from, $to) {
            $purchaseQty = PurchaseItem::where('product_id', $p->id)->whereHas('purchase', fn ($q) => $q->whereBetween('purchase_date', [$from, $to]))->sum('quantity');
            $soldQty = SaleItem::where('product_id', $p->id)->whereHas('sale', fn ($q) => $q->whereBetween(DB::raw('DATE(sale_date)'), [$from, $to]))->sum('quantity');
            $returnedQty = SaleReturnItem::where('product_id', $p->id)->whereHas('saleReturn', fn ($q) => $q->whereBetween(DB::raw('DATE(return_date)'), [$from, $to]))->sum('quantity');
            $stock = Inventory::where('product_id', $p->id)->sum('quantity');
            return [$p->name, $p->sku, $p->category?->name, $p->brand?->name, $purchaseQty, $soldQty, $returnedQty, $stock, $purchaseQty * (float)$p->cost_price, $soldQty * (float)$p->selling_price, ($soldQty * ((float)$p->selling_price - (float)$p->cost_price))];
        });
        return $this->simple(['Product', 'SKU', 'Category', 'Brand', 'Purchase Qty', 'Sold Qty', 'Returned Qty', 'Current Stock', 'Purchase Value', 'Sales Value', 'Gross Profit'], $rows);
    }

    private function productPurchase(Request $request, string $from, string $to): array { return $this->paged(PurchaseItem::with(['purchase.supplier','product'])->whereHas('purchase', fn($q)=>$q->whereBetween('purchase_date',[$from,$to]))->paginate(25)->withQueryString(), ['Date','Purchase No','Supplier','Product','Quantity','Unit Cost','Discount','Tax','Total'], fn($i)=>[$i->purchase->purchase_date->format('Y-m-d'),$i->purchase->purchase_no,$i->purchase->supplier->name,$i->product->name,$i->quantity,$i->unit_cost,$i->discount,$i->tax,$i->total]); }
    private function productSell(Request $request, string $from, string $to): array { return $this->paged(SaleItem::with(['sale.customer','sale.creator','product'])->whereHas('sale', fn($q)=>$q->whereBetween(DB::raw('DATE(sale_date)'),[$from,$to]))->paginate(25)->withQueryString(), ['Date','Invoice','Customer','Product','Quantity','Unit Price','Discount','Tax','Total','Cashier'], fn($i)=>[$i->sale->sale_date->format('Y-m-d'),$i->sale->invoice_no,$i->sale->customer?->name ?? 'Walk-in',$i->product->name,$i->quantity,$i->unit_price,$i->discount,$i->tax,$i->total,$i->sale->creator?->name]); }
    private function purchasePayment(Request $request, string $from, string $to): array { return $this->paged(PurchasePayment::with(['purchase.supplier','creator'])->whereBetween(DB::raw('DATE(paid_at)'),[$from,$to])->paginate(25)->withQueryString(), ['Date','Purchase No','Supplier','Payment Method','Reference','Amount','Created By'], fn($p)=>[$p->paid_at->format('Y-m-d'),$p->purchase->purchase_no,$p->purchase->supplier->name,ucfirst($p->payment_method),$p->reference_no,$p->amount,$p->creator?->name], ['Total Payment'=>PurchasePayment::whereBetween(DB::raw('DATE(paid_at)'),[$from,$to])->sum('amount')]); }
    private function sellPayment(Request $request, string $from, string $to): array { return $this->paged(Payment::with(['sale.customer','receiver'])->whereBetween(DB::raw('DATE(paid_at)'),[$from,$to])->paginate(25)->withQueryString(), ['Date','Invoice','Customer','Payment Method','Reference','Amount','Received By'], fn($p)=>[$p->paid_at->format('Y-m-d'),$p->sale->invoice_no,$p->sale->customer?->name ?? 'Walk-in',ucfirst($p->payment_method),$p->reference_no,$p->amount,$p->receiver?->name], ['Total Received'=>Payment::whereBetween(DB::raw('DATE(paid_at)'),[$from,$to])->sum('amount')]); }
    private function expense(Request $request, string $from, string $to): array { return $this->paged(Expense::with(['category','branch','creator'])->whereBetween('expense_date',[$from,$to])->paginate(25)->withQueryString(), ['Expense No','Date','Category','Branch','Payment Method','Amount','Created By','Description'], fn($e)=>[$e->expense_no,$e->expense_date->format('Y-m-d'),$e->category->name,$e->branch->name,ucfirst($e->payment_method),$e->amount,$e->creator?->name,$e->description], ['Total Expense'=>Expense::whereBetween('expense_date',[$from,$to])->sum('amount')]); }
    private function register(Request $request, string $from, string $to): array { $rows = Payment::with(['sale.branch','receiver'])->whereBetween(DB::raw('DATE(paid_at)'),[$from,$to])->get()->groupBy(fn($p)=>$p->paid_at->format('Y-m-d').'|'.$p->sale->branch->name.'|'.($p->receiver?->name ?? '-'))->map(fn($g,$k)=>array_merge(explode('|',$k), [$g->count(), $g->where('payment_method','cash')->sum('amount'), $g->where('payment_method','card')->sum('amount'), $g->where('payment_method','bkash')->sum('amount'), $g->where('payment_method','nagad')->sum('amount'), $g->where('payment_method','bank')->sum('amount'), $g->sum('amount'), 0, $g->sum('amount')])); return $this->simple(['Date','Branch','Cashier','Transactions','Cash','Card','bKash','Nagad','Bank','Total Collected','Refunds','Net Collection'],$rows->values()); }
    private function salesRepresentative(Request $request, string $from, string $to): array { $rows = User::leftJoin('sales','sales.created_by','=','users.id')->selectRaw('users.name, COUNT(sales.id) count, COALESCE(SUM(sales.total),0) gross, COALESCE(SUM(sales.paid_amount),0) payments, COALESCE(AVG(sales.total),0) avg_sale')->groupBy('users.id','users.name')->get()->map(fn($r)=>[$r->name,$r->count,$r->gross,0,$r->gross,$r->avg_sale,$r->payments]); return $this->simple(['User','Sales Count','Gross Sales','Returns','Net Sales','Average Sale','Payments Collected'],$rows); }
    private function activityLog(Request $request, string $from, string $to): array { $rows = collect(); Sale::with(['creator','branch'])->whereBetween(DB::raw('DATE(sale_date)'),[$from,$to])->limit(50)->get()->each(fn($s)=>$rows->push([$s->sale_date,$s->creator?->name,'Sale Created',$s->invoice_no,$s->branch->name,'Total '.$s->total])); Purchase::with(['creator','branch'])->whereBetween('purchase_date',[$from,$to])->limit(50)->get()->each(fn($p)=>$rows->push([$p->created_at,$p->creator?->name,'Purchase Created/Received',$p->purchase_no,$p->branch->name,'Total '.$p->total])); Expense::with(['creator','branch'])->whereBetween('expense_date',[$from,$to])->limit(50)->get()->each(fn($e)=>$rows->push([$e->created_at,$e->creator?->name,'Expense Created',$e->expense_no,$e->branch->name,$e->description])); return $this->simple(['Date/Time','User','Activity','Reference','Branch','Description'],$rows->sortByDesc(0)->values()); }

    private function dateRange(Request $request): array { return [$request->input('from_date', now()->startOfMonth()->toDateString()), $request->input('to_date', now()->endOfMonth()->toDateString())]; }
    private function filters(): array { return ['branches'=>Branch::orderBy('name')->get(),'warehouses'=>Warehouse::orderBy('name')->get(),'products'=>Product::orderBy('name')->get(),'categories'=>Category::orderBy('name')->get(),'brands'=>Brand::orderBy('name')->get(),'customers'=>Customer::orderBy('name')->get(),'suppliers'=>Supplier::orderBy('name')->get(),'users'=>User::orderBy('name')->get(),'expenseCategories'=>ExpenseCategory::orderBy('name')->get()]; }
    private function simple(array $headers, Collection $rows, array $totals = []): array { return ['headers'=>$headers,'rows'=>$rows,'totals'=>$totals]; }
    private function paged(LengthAwarePaginator $paginator, array $headers, callable $map, array $totals = []): array { return ['headers'=>$headers,'rows'=>$paginator->through($map),'totals'=>$totals]; }
    private function emptyReport(): array { return ['headers'=>[],'rows'=>collect(),'totals'=>[]]; }
    private function lowStockQuery($branchId = null) { return Inventory::with('product')->when($branchId, fn($q)=>$q->where('branch_id',$branchId))->whereHas('product', fn($q)=>$q->whereColumn('inventories.quantity','<=','products.minimum_stock')); }
}
