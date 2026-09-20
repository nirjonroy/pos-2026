@php
    $menuGroups = [
        [
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                ['label' => 'POS', 'route' => 'pos.index', 'active' => 'pos.*'],
            ],
        ],
        [
            'label' => 'Products',
            'items' => [
                ['label' => 'Products', 'route' => 'products.index', 'active' => 'products.*'],
                ['label' => 'Categories', 'route' => 'categories.index', 'active' => 'categories.*'],
                ['label' => 'Brands', 'route' => 'brands.index', 'active' => 'brands.*'],
                ['label' => 'Units', 'route' => 'units.index', 'active' => 'units.*'],
            ],
        ],
        [
            'label' => 'Inventory',
            'items' => [
                ['label' => 'Stock', 'route' => 'inventory.index', 'active' => 'inventory.*'],
                ['label' => 'Stock Adjustments', 'route' => 'stock-adjustments.index', 'active' => 'stock-adjustments.*'],
                ['label' => 'Stock Transfers', 'route' => 'stock-transfers.index', 'active' => 'stock-transfers.*'],
            ],
        ],
        [
            'label' => 'Purchases',
            'items' => [
                ['label' => 'Purchases', 'route' => 'purchases.index', 'active' => 'purchases.*'],
                ['label' => 'Suppliers', 'route' => 'suppliers.index', 'active' => 'suppliers.*'],
            ],
        ],
        [
            'label' => 'Sales',
            'items' => [
                ['label' => 'Sales', 'route' => null, 'active' => 'sales.*'],
                ['label' => 'Returns', 'route' => null, 'active' => 'returns.*'],
            ],
        ],
        [
            'items' => [
                ['label' => 'Customers', 'route' => 'customers.index', 'active' => 'customers.*'],
                ['label' => 'Expenses', 'route' => null, 'active' => 'expenses.*'],
                ['label' => 'Employees', 'route' => null, 'active' => 'employees.*'],
                ['label' => 'Reports', 'route' => null, 'active' => 'reports.*'],
                ['label' => 'Settings', 'route' => null, 'active' => 'settings.*'],
            ],
        ],
    ];
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
                        @php
                            $hasRoute = $item['route'] && Route::has($item['route']);
                            $href = $hasRoute ? route($item['route']) : '#';
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
