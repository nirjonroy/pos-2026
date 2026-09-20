@php
    $menuGroups = [
        [
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard', 'permission' => 'dashboard.view'],
                ['label' => 'POS', 'route' => 'pos.index', 'active' => 'pos.*', 'permission' => 'pos.access'],
            ],
        ],
        [
            'label' => 'Products',
            'items' => [
                ['label' => 'Products', 'route' => 'products.index', 'active' => 'products.*', 'permission' => 'products.view'],
                ['label' => 'Categories', 'route' => 'categories.index', 'active' => 'categories.*', 'permission' => 'products.view'],
                ['label' => 'Brands', 'route' => 'brands.index', 'active' => 'brands.*', 'permission' => 'products.view'],
                ['label' => 'Units', 'route' => 'units.index', 'active' => 'units.*', 'permission' => 'products.view'],
            ],
        ],
        [
            'label' => 'Inventory',
            'items' => [
                ['label' => 'Stock', 'route' => 'inventory.index', 'active' => 'inventory.*', 'permission' => 'inventory.view'],
                ['label' => 'Stock Adjustments', 'route' => 'stock-adjustments.index', 'active' => 'stock-adjustments.*', 'permission' => 'inventory.adjust'],
                ['label' => 'Stock Transfers', 'route' => 'stock-transfers.index', 'active' => 'stock-transfers.*', 'permission' => 'inventory.transfer'],
            ],
        ],
        [
            'label' => 'Purchases',
            'items' => [
                ['label' => 'Purchases', 'route' => 'purchases.index', 'active' => 'purchases.*', 'permission' => 'purchases.view'],
                ['label' => 'Suppliers', 'route' => 'suppliers.index', 'active' => 'suppliers.*', 'permission' => 'suppliers.view'],
            ],
        ],
        [
            'label' => 'Sales',
            'items' => [
                ['label' => 'Sales', 'route' => 'sales.index', 'active' => 'sales.*', 'permission' => 'sales.view'],
                ['label' => 'Returns', 'route' => null, 'active' => 'returns.*', 'permission' => 'sales.return'],
            ],
        ],
        [
            'items' => [
                ['label' => 'Customers', 'route' => 'customers.index', 'active' => 'customers.*', 'permission' => 'customers.view'],
                ['label' => 'Expenses', 'route' => 'expenses.index', 'active' => 'expenses.*|expense-categories.*|accounting.*', 'permission' => 'expenses.view'],
                ['label' => 'Employees', 'route' => null, 'active' => 'employees.*', 'permission' => 'users.view'],
                ['label' => 'Users', 'route' => 'users.index', 'active' => 'users.*', 'permission' => 'users.view'],
                ['label' => 'Roles', 'route' => 'roles.index', 'active' => 'roles.*', 'permission' => 'roles.view'],
                ['label' => 'Reports', 'route' => 'reports.index', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Settings', 'route' => null, 'active' => 'settings.*', 'permission' => 'settings.manage'],
            ],
        ],
        [
            'label' => 'Reports',
            'items' => [
                ['label' => 'Profit / Loss Report', 'route' => 'reports.show', 'params' => 'profit-loss', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Purchase & Sale', 'route' => 'reports.show', 'params' => 'purchase-sale', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Tax Report', 'route' => 'reports.show', 'params' => 'tax', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Supplier & Customer Report', 'route' => 'reports.show', 'params' => 'supplier-customer', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Customer Groups Report', 'route' => 'reports.show', 'params' => 'customer-groups', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Stock Report', 'route' => 'reports.show', 'params' => 'stock', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Stock Adjustment Report', 'route' => 'reports.show', 'params' => 'stock-adjustment', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Trending Products', 'route' => 'reports.show', 'params' => 'trending-products', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Items Report', 'route' => 'reports.show', 'params' => 'items', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Product Purchase Report', 'route' => 'reports.show', 'params' => 'product-purchase', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Product Sell Report', 'route' => 'reports.show', 'params' => 'product-sell', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Purchase Payment Report', 'route' => 'reports.show', 'params' => 'purchase-payment', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Sell Payment Report', 'route' => 'reports.show', 'params' => 'sell-payment', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Expense Report', 'route' => 'reports.show', 'params' => 'expense', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Register Report', 'route' => 'reports.show', 'params' => 'register', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Sales Representative Report', 'route' => 'reports.show', 'params' => 'sales-representative', 'active' => 'reports.*', 'permission' => 'reports.view'],
                ['label' => 'Activity Log', 'route' => 'reports.show', 'params' => 'activity-log', 'active' => 'reports.*', 'permission' => 'reports.view'],
            ],
        ],
    ];

    $canSee = fn ($item) => empty($item['permission']) || (auth()->check() && auth()->user()->hasPermission($item['permission']));
@endphp

<div class="flex h-full flex-col bg-gray-900 text-gray-100 shadow-xl">
    <div class="flex h-16 items-center justify-between border-b border-gray-800 px-5">
        <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="text-lg font-bold tracking-wide">
            POS Admin
        </a>

        @if ($mobile ?? false)
            <button type="button" class="rounded-md p-2 text-gray-300 hover:bg-gray-800 hover:text-white" @click="sidebarOpen = false">
                <span class="sr-only">Close sidebar</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
        @foreach ($menuGroups as $group)
            <div>
                @if (! empty($group['label']))
                    <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        {{ $group['label'] }}
                    </div>
                @endif

                <div class="space-y-1">
                    @foreach ($group['items'] as $item)
                        @continue(! $canSee($item))
                        @php
                            $hasRoute = $item['route'] && Route::has($item['route']);
                            $href = $hasRoute ? route($item['route'], $item['params'] ?? []) : '#';
                            $active = request()->routeIs($item['active']);
                        @endphp

                        <a href="{{ $href }}"
                           class="flex items-center rounded-md px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <span class="mr-3 h-2 w-2 rounded-full {{ $active ? 'bg-blue-400' : 'bg-gray-600' }}"></span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>
</div>
