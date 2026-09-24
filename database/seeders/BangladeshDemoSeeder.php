<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\PurchaseService;
use App\Services\SaleReturnService;
use App\Services\SaleService;
use App\Services\StockAdjustmentService;
use App\Services\StockTransferService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BangladeshDemoSeeder extends Seeder
{
    private const DEMO_EMAILS = [
        'manager.bd@example.com',
        'cashier1.bd@example.com',
        'cashier2.bd@example.com',
        'inventory.bd@example.com',
    ];

    private array $counts = [];

    public function run(): void
    {
        $inventoryService = app(InventoryService::class);
        $purchaseService = app(PurchaseService::class);
        $saleService = app(SaleService::class);
        $returnService = app(SaleReturnService::class);
        $adjustmentService = app(StockAdjustmentService::class);
        $transferService = app(StockTransferService::class);

        $branches = $this->seedBranches();
        $warehouses = $this->seedWarehouses($branches);
        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $units = $this->seedUnits();
        $products = $this->seedProducts($categories, $brands, $units);
        $suppliers = $this->seedSuppliers();
        $customerGroups = $this->seedCustomerGroups();
        $customers = $this->seedCustomers($customerGroups);
        $users = $this->seedUsers();
        $expenseCategories = $this->seedExpenseCategories();

        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $cashiers = collect([$users['cashier1'], $users['cashier2'], $users['manager']]);

        $this->seedOpeningInventory($branches, $warehouses, $products);

        $alreadySeededTransactions = Sale::whereIn('created_by', collect($users)->pluck('id'))->exists();

        if (! $alreadySeededTransactions) {
            $this->seedPurchases($purchaseService, $branches, $warehouses, $suppliers, $products, $admin->id);
            $this->seedSales($saleService, $branches, $warehouses, $customers, $products, $cashiers);
            $this->seedReturns($returnService, $cashiers);
            $this->seedAdjustments($adjustmentService, $branches, $warehouses, $products, $users['inventory']->id);
            $this->seedTransfers($transferService, $branches, $warehouses, $products, $users['inventory']->id);
            $this->shapeLowStock($adjustmentService, $branches, $warehouses, $products, $users['inventory']->id);
            $this->seedExpenses($branches, $expenseCategories, $admin->id);
        }

        $this->counts = [
            'branches' => Branch::whereIn('code', ['DHA', 'MIR', 'UTR'])->count(),
            'warehouses' => Warehouse::whereIn('code', ['WH-DHA', 'WH-MIR', 'WH-UTR'])->count(),
            'categories' => Category::whereIn('name', $this->categoryNames())->count(),
            'brands' => Brand::whereIn('name', $this->brandNames())->count(),
            'units' => Unit::whereIn('short_name', ['pcs', 'kg', 'gm', 'L', 'ml', 'pack'])->count(),
            'products' => Product::where('sku', 'like', 'BD-%')->count(),
            'suppliers' => Supplier::where('email', 'like', '%@bd-demo.test')->count(),
            'customers' => Customer::where('email', 'like', '%@bd-demo.test')->count(),
            'users' => User::whereIn('email', self::DEMO_EMAILS)->count(),
            'inventories' => Inventory::whereIn('branch_id', $branches->pluck('id'))->count(),
            'purchases' => Purchase::where('note', 'like', 'BD-DEMO%')->count(),
            'sales' => Sale::whereIn('created_by', collect($users)->pluck('id'))->count(),
            'returns' => SaleReturn::whereIn('created_by', collect($users)->pluck('id'))->count(),
            'stock_adjustments' => StockAdjustment::whereIn('created_by', [$users['inventory']->id])->count(),
            'stock_transfers' => StockTransfer::whereIn('created_by', [$users['inventory']->id])->count(),
            'expenses' => Expense::where('description', 'like', 'BD-DEMO%')->count(),
        ];

        $this->command?->info('Bangladesh demo seed counts: '.json_encode($this->counts));
    }

    private function seedBranches()
    {
        $data = [
            ['name' => 'Dhanmondi Branch', 'code' => 'DHA', 'phone' => '01711000001', 'email' => 'dhanmondi@bd-demo.test', 'address' => 'House 12, Road 8/A, Dhanmondi, Dhaka'],
            ['name' => 'Mirpur Branch', 'code' => 'MIR', 'phone' => '01711000002', 'email' => 'mirpur@bd-demo.test', 'address' => 'Section 10, Avenue 3, Mirpur, Dhaka'],
            ['name' => 'Uttara Branch', 'code' => 'UTR', 'phone' => '01711000003', 'email' => 'uttara@bd-demo.test', 'address' => 'Sector 7, Road 12, Uttara, Dhaka'],
        ];

        foreach ($data as $row) {
            Branch::updateOrCreate(['code' => $row['code']], $row + ['status' => true]);
        }

        return Branch::whereIn('code', collect($data)->pluck('code'))->get()->keyBy('code');
    }

    private function seedWarehouses($branches)
    {
        $data = [
            ['branch' => 'DHA', 'name' => 'Dhanmondi Main Warehouse', 'code' => 'WH-DHA', 'address' => 'Dhanmondi Branch Back Store'],
            ['branch' => 'MIR', 'name' => 'Mirpur Main Warehouse', 'code' => 'WH-MIR', 'address' => 'Mirpur Branch Back Store'],
            ['branch' => 'UTR', 'name' => 'Uttara Main Warehouse', 'code' => 'WH-UTR', 'address' => 'Uttara Branch Back Store'],
        ];

        foreach ($data as $row) {
            Warehouse::updateOrCreate(
                ['code' => $row['code']],
                [
                    'branch_id' => $branches[$row['branch']]->id,
                    'name' => $row['name'],
                    'address' => $row['address'],
                    'status' => true,
                ]
            );
        }

        return Warehouse::whereIn('code', collect($data)->pluck('code'))->get()->keyBy('branch_id');
    }

    private function seedCategories()
    {
        foreach ($this->categoryNames() as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['parent_id' => null, 'name' => $name, 'status' => true]
            );
        }

        return Category::whereIn('name', $this->categoryNames())->get()->keyBy('name');
    }

    private function seedBrands()
    {
        foreach ($this->brandNames() as $name) {
            Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'status' => true]);
        }

        return Brand::whereIn('name', $this->brandNames())->get()->keyBy('name');
    }

    private function seedUnits()
    {
        $data = [
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'gm'],
            ['name' => 'Liter', 'short_name' => 'L'],
            ['name' => 'Milliliter', 'short_name' => 'ml'],
            ['name' => 'Pack', 'short_name' => 'pack'],
        ];

        foreach ($data as $row) {
            Unit::updateOrCreate(['short_name' => $row['short_name']], $row + ['status' => true]);
        }

        return Unit::whereIn('short_name', collect($data)->pluck('short_name'))->get()->keyBy('short_name');
    }

    private function seedProducts($categories, $brands, $units)
    {
        $products = [
            ['Miniket Rice 5kg', 'BD-RICE-001', '880100000001', 'Rice & Grains', 'Fresh', 'pack', 410, 460, 12],
            ['Nazirshail Rice 5kg', 'BD-RICE-002', '880100000002', 'Rice & Grains', 'Fresh', 'pack', 450, 510, 10],
            ['Atop Rice 5kg', 'BD-RICE-003', '880100000003', 'Rice & Grains', 'Fresh', 'pack', 360, 410, 10],
            ['Masoor Dal 1kg', 'BD-GRAIN-004', '880100000004', 'Rice & Grains', 'ACI', 'kg', 115, 135, 15],
            ['Sugar 1kg', 'BD-GRAIN-005', '880100000005', 'Rice & Grains', 'Fresh', 'kg', 125, 145, 15],
            ['Iodized Salt 1kg', 'BD-GRAIN-006', '880100000006', 'Rice & Grains', 'ACI', 'kg', 32, 40, 20],
            ['Teer Soybean Oil 1L', 'BD-OIL-007', '880100000007', 'Cooking Oil', 'Teer', 'L', 155, 175, 12],
            ['Fresh Soybean Oil 2L', 'BD-OIL-008', '880100000008', 'Cooking Oil', 'Fresh', 'L', 315, 355, 10],
            ['Radhuni Turmeric Powder 200gm', 'BD-SPICE-009', '880100000009', 'Spices', 'Radhuni', 'gm', 78, 95, 15],
            ['Radhuni Chili Powder 200gm', 'BD-SPICE-010', '880100000010', 'Spices', 'Radhuni', 'gm', 92, 110, 15],
            ['Radhuni Cumin Powder 100gm', 'BD-SPICE-011', '880100000011', 'Spices', 'Radhuni', 'gm', 72, 90, 10],
            ['PRAN Chanachur 300gm', 'BD-SNACK-012', '880100000012', 'Snacks', 'PRAN', 'pack', 55, 70, 20],
            ['Olympic Energy Plus Biscuits', 'BD-SNACK-013', '880100000013', 'Snacks', 'Olympic', 'pack', 28, 35, 25],
            ['PRAN Potato Crackers', 'BD-SNACK-014', '880100000014', 'Snacks', 'PRAN', 'pack', 18, 25, 25],
            ['Coca-Cola 1L', 'BD-BEV-015', '880100000015', 'Beverages', 'Coca-Cola', 'L', 70, 85, 18],
            ['Pepsi 1L', 'BD-BEV-016', '880100000016', 'Beverages', 'Pepsi', 'L', 68, 85, 18],
            ['Mineral Water 1L', 'BD-BEV-017', '880100000017', 'Beverages', 'Fresh', 'L', 18, 25, 30],
            ['Milk 1L', 'BD-DAIRY-018', '880100000018', 'Dairy', 'PRAN', 'L', 82, 95, 12],
            ['ACI Savlon Soap', 'BD-PC-019', '880100000019', 'Personal Care', 'ACI', 'pcs', 42, 55, 15],
            ['Sunsilk Shampoo 180ml', 'BD-PC-020', '880100000020', 'Personal Care', 'ACI', 'ml', 185, 220, 8],
            ['Closeup Toothpaste 160gm', 'BD-PC-021', '880100000021', 'Personal Care', 'ACI', 'gm', 105, 130, 10],
            ['Bashundhara Tissue Box', 'BD-HH-022', '880100000022', 'Household', 'Bashundhara', 'pcs', 58, 75, 12],
            ['Bashundhara Toilet Tissue', 'BD-HH-023', '880100000023', 'Household', 'Bashundhara', 'pack', 85, 105, 12],
            ['Wheel Detergent 500gm', 'BD-HH-024', '880100000024', 'Household', 'ACI', 'gm', 62, 78, 10],
            ['Dishwash Liquid 500ml', 'BD-HH-025', '880100000025', 'Household', 'ACI', 'ml', 105, 130, 10],
            ['USB Cable Type-C', 'BD-MOB-026', '880100000026', 'Mobile Accessories', 'ACI', 'pcs', 120, 180, 8],
            ['Mobile Charger 20W', 'BD-MOB-027', '880100000027', 'Mobile Accessories', 'ACI', 'pcs', 420, 550, 5],
            ['Wired Earphone', 'BD-MOB-028', '880100000028', 'Mobile Accessories', 'ACI', 'pcs', 220, 320, 5],
            ['Tempered Glass Protector', 'BD-MOB-029', '880100000029', 'Mobile Accessories', 'ACI', 'pcs', 55, 100, 10],
            ['PRAN Mango Juice 1L', 'BD-BEV-030', '880100000030', 'Beverages', 'PRAN', 'L', 95, 120, 15],
        ];

        foreach ($products as $row) {
            Product::updateOrCreate(
                ['sku' => $row[1]],
                [
                    'category_id' => $categories[$row[3]]->id,
                    'brand_id' => $brands[$row[4]]->id,
                    'unit_id' => $units[$row[5]]->id,
                    'name' => $row[0],
                    'barcode' => $row[2],
                    'cost_price' => $row[6],
                    'selling_price' => $row[7],
                    'minimum_stock' => $row[8],
                    'description' => 'BD-DEMO sample product',
                    'status' => true,
                ]
            );
        }

        return Product::where('sku', 'like', 'BD-%')->get()->values();
    }

    private function seedSuppliers()
    {
        $names = [
            'Dhaka Consumer Distributors',
            'Bengal Grocery Supply',
            'Padma Trading Corporation',
            'Metro Beverage Distribution',
            'Bangladesh Household Supply',
            'Uttara FMCG Traders',
            'Mirpur Retail Supply',
            'Dhanmondi Daily Needs',
        ];

        foreach ($names as $index => $name) {
            Supplier::updateOrCreate(
                ['email' => 'supplier'.($index + 1).'@bd-demo.test'],
                [
                    'name' => $name,
                    'company_name' => $name,
                    'phone' => '0182200000'.($index + 1),
                    'address' => 'Demo Supplier Road '.($index + 1).', Dhaka',
                    'opening_due' => $index % 3 === 0 ? 1500 : 0,
                    'status' => true,
                ]
            );
        }

        return Supplier::where('email', 'like', '%@bd-demo.test')->get()->values();
    }

    private function seedCustomerGroups()
    {
        foreach (['Regular', 'Retail', 'VIP', 'Corporate'] as $name) {
            CustomerGroup::updateOrCreate(['name' => $name], ['description' => 'BD-DEMO '.$name.' customers', 'status' => true]);
        }

        return CustomerGroup::whereIn('name', ['Regular', 'Retail', 'VIP', 'Corporate'])->get()->keyBy('name');
    }

    private function seedCustomers($groups)
    {
        $names = [
            'Rahim Ahmed', 'Karim Hasan', 'Nusrat Jahan', 'Tasnim Akter', 'Fahim Chowdhury',
            'Imran Hossain', 'Sadia Rahman', 'Mehedi Hasan', 'Farzana Islam', 'Arif Mahmud',
            'Jannatul Ferdous', 'Shakib Rahman', 'Mithila Khan', 'Nayeem Islam', 'Rokeya Begum',
            'Hasan Mahmud', 'Tania Sultana', 'Sabbir Ahmed', 'Mahin Rahman', 'Samira Chowdhury',
        ];
        $groupNames = ['Regular', 'Retail', 'VIP', 'Corporate'];

        foreach ($names as $index => $name) {
            Customer::updateOrCreate(
                ['email' => 'customer'.($index + 1).'@bd-demo.test'],
                [
                    'customer_group_id' => $groups[$groupNames[$index % 4]]->id,
                    'name' => $name,
                    'phone' => '017330000'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'address' => 'House '.($index + 10).', Demo Road, Dhaka',
                    'credit_limit' => $index % 5 === 0 ? 5000 : 1000,
                    'opening_due' => $index % 6 === 0 ? 650 : 0,
                    'status' => true,
                ]
            );
        }

        return Customer::where('email', 'like', '%@bd-demo.test')->get()->values();
    }

    private function seedUsers(): array
    {
        $data = [
            'manager' => ['name' => 'Manager', 'email' => 'manager.bd@example.com', 'role' => 'manager'],
            'cashier1' => ['name' => 'Cashier 1', 'email' => 'cashier1.bd@example.com', 'role' => 'cashier'],
            'cashier2' => ['name' => 'Cashier 2', 'email' => 'cashier2.bd@example.com', 'role' => 'cashier'],
            'inventory' => ['name' => 'Inventory Manager', 'email' => 'inventory.bd@example.com', 'role' => 'inventory-manager'],
        ];

        $users = [];
        foreach ($data as $key => $row) {
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                ['name' => $row['name'], 'password' => Hash::make('12345678')]
            );

            $role = Role::where('slug', $row['role'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            $users[$key] = $user;
        }

        return $users;
    }

    private function seedOpeningInventory($branches, $warehouses, $products): void
    {
        foreach ($branches as $branch) {
            $warehouse = $warehouses[$branch->id] ?? null;
            foreach ($products as $index => $product) {
                Inventory::updateOrCreate(
                    ['branch_id' => $branch->id, 'warehouse_id' => $warehouse?->id, 'product_id' => $product->id],
                    ['quantity' => 140 + (($index * 7 + $branch->id) % 80), 'reserved_quantity' => 0]
                );
            }
        }
    }

    private function seedPurchases(PurchaseService $service, $branches, $warehouses, $suppliers, $products, int $userId): void
    {
        for ($i = 0; $i < 18; $i++) {
            $branch = $branches->values()[$i % $branches->count()];
            $warehouse = $warehouses[$branch->id] ?? null;
            $items = [];

            for ($j = 0; $j < 3; $j++) {
                $product = $products[($i * 3 + $j) % $products->count()];
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => 12 + (($i + $j) % 10),
                    'unit_cost' => (float) $product->cost_price,
                    'discount' => $j === 1 ? 20 : 0,
                    'tax' => $j === 2 ? 15 : 0,
                ];
            }

            $purchase = $service->createDraft([
                'supplier_id' => $suppliers[$i % $suppliers->count()]->id,
                'branch_id' => $branch->id,
                'warehouse_id' => $warehouse?->id,
                'purchase_date' => now()->subDays(45 - ($i * 2))->toDateString(),
                'note' => 'BD-DEMO purchase batch '.$i,
                'items' => $items,
            ], $userId);

            if ($i < 15) {
                $service->receive($purchase);
                $amount = $i % 4 === 0 ? round((float) $purchase->total * 0.6, 2) : (float) $purchase->total;
                $service->addPayment($purchase->fresh(), [
                    'payment_method' => ['cash', 'bkash', 'nagad', 'bank', 'card'][$i % 5],
                    'amount' => $amount,
                    'reference_no' => 'BDPUR'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                    'paid_at' => now()->subDays(44 - ($i * 2)),
                ], $userId);
            }
        }
    }

    private function seedSales(SaleService $service, $branches, $warehouses, $customers, $products, $cashiers): void
    {
        for ($i = 0; $i < 70; $i++) {
            $branch = $branches->values()[$i % $branches->count()];
            $warehouse = $warehouses[$branch->id] ?? null;
            $customer = $i % 5 === 0 ? null : $customers[$i % $customers->count()];
            $items = [];

            for ($j = 0; $j < 2 + ($i % 2); $j++) {
                $product = $products[($i * 2 + $j) % $products->count()];
                $items[] = ['product_id' => $product->id, 'quantity' => 1 + (($i + $j) % 3)];
            }

            $subtotal = collect($items)->sum(fn ($item) => (float) Product::find($item['product_id'])->selling_price * $item['quantity']);
            $discount = $i % 7 === 0 ? 25 : 0;
            $tax = $i % 6 === 0 ? 10 : 0;
            $total = $subtotal - $discount + $tax;
            $partial = $i % 9 === 0;
            $paid = $partial ? round($total * 0.65, 2) : $total;
            $method = ['cash', 'bkash', 'nagad', 'card', 'bank'][$i % 5];
            $payments = $i % 8 === 0
                ? [['payment_method' => 'cash', 'amount' => round($paid * 0.45, 2)], ['payment_method' => 'bkash', 'amount' => round($paid * 0.55, 2)]]
                : [['payment_method' => $method, 'amount' => $paid]];

            $result = $service->checkout([
                'branch_id' => $branch->id,
                'warehouse_id' => $warehouse?->id,
                'customer_id' => $partial ? ($customer?->id ?? $customers[$i % $customers->count()]->id) : $customer?->id,
                'items' => $items,
                'discount' => $discount,
                'tax' => $tax,
                'payments' => $payments,
            ], $cashiers[$i % $cashiers->count()]->id);

            $saleDate = now()->subDays(44 - ($i % 45))->setTime(10 + ($i % 10), ($i * 7) % 60);
            Sale::whereKey($result['sale_id'])->update(['sale_date' => $saleDate, 'created_at' => $saleDate, 'updated_at' => $saleDate]);
        }
    }

    private function seedReturns(SaleReturnService $service, $cashiers): void
    {
        $sales = Sale::with('items')->whereIn('created_by', $cashiers->pluck('id'))->where('status', 'completed')->take(6)->get();

        foreach ($sales as $index => $sale) {
            $item = $sale->items->first();
            if (! $item || (float) $item->quantity < 1) {
                continue;
            }

            $return = $service->createReturn($sale, [
                'reason' => ['Customer changed mind', 'Damaged packet', 'Wrong item selected'][$index % 3],
                'refund_method' => ['cash', 'bkash', 'nagad', 'card', 'bank', 'store_credit'][$index % 6],
                'items' => [['sale_item_id' => $item->id, 'quantity' => 1]],
            ], $cashiers[$index % $cashiers->count()]->id);

            $date = $sale->sale_date->copy()->addDays(1);
            $return->update(['return_date' => $date, 'created_at' => $date, 'updated_at' => $date]);
        }
    }

    private function seedAdjustments(StockAdjustmentService $service, $branches, $warehouses, $products, int $userId): void
    {
        $reasons = [
            ['decrease', 'Damaged goods', 'BD-DEMO damaged goods adjustment'],
            ['increase', 'Physical stock correction', 'BD-DEMO counting surplus'],
            ['decrease', 'Expired item', 'BD-DEMO expired item adjustment'],
            ['increase', 'Counting difference', 'BD-DEMO stock count correction'],
        ];

        foreach ($reasons as $index => $row) {
            $branch = $branches->values()[$index % $branches->count()];
            $warehouse = $warehouses[$branch->id] ?? null;
            $product = $products[($index * 5) % $products->count()];

            $service->createCompleted([
                'branch_id' => $branch->id,
                'warehouse_id' => $warehouse?->id,
                'type' => $row[0],
                'reason' => $row[1],
                'note' => $row[2],
                'items' => [['product_id' => $product->id, 'quantity' => 2 + $index, 'unit_cost' => $product->cost_price]],
            ], $userId);
        }
    }

    private function seedTransfers(StockTransferService $service, $branches, $warehouses, $products, int $userId): void
    {
        $branchList = $branches->values();
        for ($i = 0; $i < 6; $i++) {
            $from = $branchList[$i % 3];
            $to = $branchList[($i + 1) % 3];
            $product = $products[($i * 4) % $products->count()];

            $service->createCompleted([
                'from_branch_id' => $from->id,
                'to_branch_id' => $to->id,
                'from_warehouse_id' => ($warehouses[$from->id] ?? null)?->id,
                'to_warehouse_id' => ($warehouses[$to->id] ?? null)?->id,
                'transfer_date' => now()->subDays(20 - $i)->toDateString(),
                'note' => 'BD-DEMO branch transfer '.$i,
                'items' => [['product_id' => $product->id, 'quantity' => 3 + $i]],
            ], $userId);
        }
    }

    private function shapeLowStock(StockAdjustmentService $service, $branches, $warehouses, $products, int $userId): void
    {
        $targets = [
            [$products[25], 0],
            [$products[26], 0],
            [$products[27], 2],
            [$products[28], 3],
            [$products[19], 4],
        ];

        $branch = $branches->first();
        $warehouse = $warehouses[$branch->id] ?? null;

        foreach ($targets as [$product, $targetQty]) {
            $inventory = Inventory::where('branch_id', $branch->id)->where('warehouse_id', $warehouse?->id)->where('product_id', $product->id)->first();
            $current = (float) ($inventory?->quantity ?? 0);
            $decrease = max($current - $targetQty, 0);

            if ($decrease <= 0) {
                continue;
            }

            $service->createCompleted([
                'branch_id' => $branch->id,
                'warehouse_id' => $warehouse?->id,
                'type' => 'decrease',
                'reason' => 'Demo low stock shaping',
                'note' => 'BD-DEMO low stock report setup',
                'items' => [['product_id' => $product->id, 'quantity' => $decrease, 'unit_cost' => $product->cost_price]],
            ], $userId);
        }
    }

    private function seedExpenseCategories()
    {
        $names = ['Shop Rent', 'Electricity Bill', 'Internet Bill', 'Staff Refreshment', 'Transportation', 'Packaging', 'Maintenance', 'Miscellaneous'];

        foreach ($names as $name) {
            ExpenseCategory::updateOrCreate(['name' => $name], ['status' => true]);
        }

        return ExpenseCategory::whereIn('name', $names)->get()->values();
    }

    private function seedExpenses($branches, $categories, int $userId): void
    {
        for ($i = 0; $i < 24; $i++) {
            $date = now()->subDays(29 - ($i % 30))->toDateString();
            $expenseNo = 'EXP-BD-DEMO-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);

            Expense::updateOrCreate(
                ['expense_no' => $expenseNo],
                [
                    'branch_id' => $branches->values()[$i % $branches->count()]->id,
                    'expense_category_id' => $categories[$i % $categories->count()]->id,
                    'expense_date' => $date,
                    'amount' => 350 + (($i * 175) % 5500),
                    'payment_method' => ['cash', 'card', 'bkash', 'nagad', 'bank'][$i % 5],
                    'reference_no' => 'BDEXP'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                    'description' => 'BD-DEMO expense record '.$i,
                    'created_by' => $userId,
                ]
            );
        }
    }

    private function categoryNames(): array
    {
        return ['Rice & Grains', 'Cooking Oil', 'Spices', 'Snacks', 'Beverages', 'Dairy', 'Personal Care', 'Household', 'Mobile Accessories'];
    }

    private function brandNames(): array
    {
        return ['PRAN', 'Radhuni', 'Fresh', 'Teer', 'ACI', 'Olympic', 'Bashundhara', 'Coca-Cola', 'Pepsi'];
    }
}
