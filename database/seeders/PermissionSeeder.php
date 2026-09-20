<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        'Dashboard' => ['dashboard.view'],
        'POS' => ['pos.access'],
        'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
        'Inventory' => ['inventory.view', 'inventory.adjust', 'inventory.transfer'],
        'Purchases' => ['purchases.view', 'purchases.create', 'purchases.receive', 'purchases.payment'],
        'Sales' => ['sales.view', 'sales.create', 'sales.return'],
        'Customers' => ['customers.view', 'customers.manage'],
        'Suppliers' => ['suppliers.view', 'suppliers.manage'],
        'Expenses' => ['expenses.view', 'expenses.manage'],
        'Reports' => ['reports.view'],
        'Users' => ['users.view', 'users.manage'],
        'Roles' => ['roles.view', 'roles.manage'],
        'Settings' => ['settings.manage'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $group => $permissions) {
            foreach ($permissions as $slug) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => Str::headline(str_replace('.', ' ', $slug)),
                        'group_name' => $group,
                    ]
                );
            }
        }
    }
}
