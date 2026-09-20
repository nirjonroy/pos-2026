@extends('layouts.admin')

@section('page-title', 'POS')

@section('content')
    <div
        x-data="posScreen({
            productUrl: '{{ route('pos.products') }}',
            checkoutUrl: '{{ route('pos.checkout') }}',
            csrfToken: '{{ csrf_token() }}',
            customers: @js($customers->map(fn ($customer) => ['id' => $customer->id, 'name' => $customer->name])),
        })"
        x-init="loadProducts()"
        class="grid gap-6 xl:grid-cols-[1fr_420px]"
    >
        <section class="space-y-4">
            <div x-show="message" class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" x-text="message"></div>
            <div x-show="error" class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" x-text="error"></div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="grid gap-3 lg:grid-cols-4">
                    <input x-model.debounce.350ms="search" @input="loadProducts" type="text" placeholder="Search product, SKU, barcode" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 lg:col-span-2">

                    <select x-model="categoryId" @change="loadProducts" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <button type="button" @click="exactAdd" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Add SKU/Barcode</button>
                </div>

                <div class="mt-3 grid gap-3 lg:grid-cols-2">
                    <select x-model="branchId" @change="loadProducts" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>

                    <select x-model="warehouseId" @change="loadProducts" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All warehouses</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                <template x-for="product in products" :key="product.id">
                    <button type="button" @click="addProduct(product)" class="rounded-lg border border-gray-200 bg-white p-4 text-left shadow-sm hover:border-blue-300 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50" :disabled="product.available_stock <= 0">
                        <div class="font-semibold text-gray-900" x-text="product.name"></div>
                        <div class="mt-1 text-xs text-gray-500" x-text="product.sku"></div>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="font-semibold text-gray-900" x-text="money(product.selling_price)"></span>
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700" x-text="`Stock ${formatQty(product.available_stock)}`"></span>
                        </div>
                    </button>
                </template>
            </div>

            <div x-show="!loading && products.length === 0" class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                No products found.
            </div>
        </section>

        <aside class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-4">
                <label class="block text-sm font-medium text-gray-700">Customer</label>
                <select x-model="customerId" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Walk-in Customer</option>
                    <template x-for="customer in customers" :key="customer.id">
                        <option :value="customer.id" x-text="customer.name"></option>
                    </template>
                </select>
            </div>

            <div class="max-h-[36vh] overflow-y-auto divide-y divide-gray-100">
                <template x-for="item in cart" :key="item.id">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-900" x-text="item.name"></div>
                                <div class="text-xs text-gray-500" x-text="`${item.sku} - ${money(item.unit_price)}`"></div>
                            </div>
                            <button type="button" @click="removeItem(item.id)" class="text-sm font-semibold text-red-600 hover:text-red-800">Remove</button>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center rounded-md border border-gray-300">
                                <button type="button" @click="decreaseQty(item)" class="px-3 py-1 text-lg leading-none">-</button>
                                <input x-model.number="item.quantity" @input="normalizeQty(item)" type="number" min="1" :max="item.available_stock" class="w-16 border-0 text-center text-sm focus:ring-0">
                                <button type="button" @click="increaseQty(item)" class="px-3 py-1 text-lg leading-none">+</button>
                            </div>
                            <div class="font-semibold text-gray-900" x-text="money(item.quantity * item.unit_price)"></div>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="p-8 text-center text-sm text-gray-500">
                    Cart is empty.
                </div>
            </div>

            <div class="space-y-3 border-t border-gray-200 p-4">
                <div class="flex justify-between text-sm"><span>Subtotal</span><strong x-text="money(subtotal())"></strong></div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="text-sm">
                        <span class="text-gray-600">Discount</span>
                        <input x-model.number="discount" type="number" min="0" step="0.01" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </label>
                    <label class="text-sm">
                        <span class="text-gray-600">Tax</span>
                        <input x-model.number="tax" type="number" min="0" step="0.01" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </label>
                </div>

                <div class="flex justify-between text-base"><span>Grand Total</span><strong x-text="money(grandTotal())"></strong></div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Payments</span>
                        <button type="button" @click="payments.push({ payment_method: 'cash', amount: 0 })" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Add</button>
                    </div>

                    <template x-for="(payment, index) in payments" :key="index">
                        <div class="grid grid-cols-[1fr_1fr_auto] gap-2">
                            <select x-model="payment.payment_method" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank</option>
                            </select>
                            <input x-model.number="payment.amount" type="number" min="0" step="0.01" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="button" @click="payments.splice(index, 1)" class="rounded-md border border-red-200 px-2 text-sm font-semibold text-red-600 hover:bg-red-50" x-show="payments.length > 1">Remove</button>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between text-sm"><span>Paid</span><strong x-text="money(paymentTotal())"></strong></div>
                <div class="flex justify-between text-sm">
                    <span x-text="paymentTotal() >= grandTotal() ? 'Change Preview' : 'Due Preview'"></span>
                    <strong x-text="money(Math.abs(paymentTotal() - grandTotal()))"></strong>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" @click="clearCart" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear Cart</button>
                    <button type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" @click="checkout" :disabled="cart.length === 0 || checkingOut">
                        <span x-text="checkingOut ? 'Processing...' : 'Checkout'"></span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <script>
        function posScreen(config) {
            return {
                productUrl: config.productUrl,
                checkoutUrl: config.checkoutUrl,
                csrfToken: config.csrfToken,
                customers: config.customers,
                products: [],
                cart: [],
                search: '',
                categoryId: '',
                branchId: '',
                warehouseId: '',
                customerId: '',
                discount: 0,
                tax: 0,
                payments: [{ payment_method: 'cash', amount: 0 }],
                loading: false,
                checkingOut: false,
                message: '',
                error: '',
                async loadProducts() {
                    this.loading = true;
                    const params = new URLSearchParams({
                        search: this.search || '',
                        category_id: this.categoryId || '',
                        branch_id: this.branchId || '',
                        warehouse_id: this.warehouseId || '',
                    });
                    const response = await fetch(`${this.productUrl}?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                    this.products = await response.json();
                    this.loading = false;
                },
                exactAdd() {
                    const term = (this.search || '').toLowerCase().trim();
                    const product = this.products.find((item) => item.sku.toLowerCase() === term || (item.barcode || '').toLowerCase() === term);
                    if (product) this.addProduct(product);
                },
                addProduct(product) {
                    const existing = this.cart.find((item) => item.id === product.id);
                    if (existing) {
                        this.increaseQty(existing);
                        return;
                    }
                    if (product.available_stock <= 0) return;
                    this.cart.push({ id: product.id, name: product.name, sku: product.sku, unit_price: product.selling_price, available_stock: product.available_stock, quantity: 1 });
                },
                increaseQty(item) {
                    if (item.quantity < item.available_stock) item.quantity++;
                },
                decreaseQty(item) {
                    if (item.quantity > 1) item.quantity--;
                },
                normalizeQty(item) {
                    if (!item.quantity || item.quantity < 1) item.quantity = 1;
                    if (item.quantity > item.available_stock) item.quantity = item.available_stock;
                },
                removeItem(id) {
                    this.cart = this.cart.filter((item) => item.id !== id);
                },
                clearCart() {
                    this.cart = [];
                    this.discount = 0;
                    this.tax = 0;
                    this.payments = [{ payment_method: 'cash', amount: 0 }];
                },
                subtotal() {
                    return this.cart.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
                },
                grandTotal() {
                    return Math.max(this.subtotal() - Number(this.discount || 0) + Number(this.tax || 0), 0);
                },
                paymentTotal() {
                    return this.payments.reduce((total, payment) => total + Number(payment.amount || 0), 0);
                },
                async checkout() {
                    if (this.checkingOut) return;

                    this.error = '';
                    this.message = '';

                    if (!this.branchId) {
                        this.error = 'Please select a branch before checkout.';
                        return;
                    }

                    this.checkingOut = true;

                    try {
                        const response = await fetch(this.checkoutUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                branch_id: this.branchId,
                                warehouse_id: this.warehouseId || null,
                                customer_id: this.customerId || null,
                                discount: Number(this.discount || 0),
                                tax: Number(this.tax || 0),
                                items: this.cart.map((item) => ({
                                    product_id: item.id,
                                    quantity: item.quantity,
                                })),
                                payments: this.payments
                                    .filter((payment) => Number(payment.amount || 0) > 0)
                                    .map((payment) => ({
                                        payment_method: payment.payment_method,
                                        amount: Number(payment.amount || 0),
                                    })),
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.error = data.message || 'Checkout failed.';
                            return;
                        }

                        this.clearCart();
                        this.message = `Sale ${data.invoice_no} completed.`;
                        window.location.href = data.receipt_url;
                    } catch (error) {
                        this.error = 'Checkout failed. Please try again.';
                    } finally {
                        this.checkingOut = false;
                    }
                },
                money(value) {
                    return Number(value || 0).toFixed(2);
                },
                formatQty(value) {
                    return Number(value || 0).toFixed(3);
                },
            };
        }
    </script>
@endsection
