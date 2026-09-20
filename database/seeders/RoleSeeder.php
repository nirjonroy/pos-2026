<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin' => Permission::pluck('slug')->all(),
            'Manager' => [
                'dashboard.view',
                'pos.access',
                'products.view',
                'products.create',
                'products.edit',
                'inventory.view',
                'inventory.adjust',
                'inventory.transfer',
                'purchases.view',
                'purchases.create',
                'purchases.receive',
                'purchases.payment',
                'sales.view',
                'sales.create',
                'sales.return',
                'customers.view',
                'customers.manage',
                'suppliers.view',
                'suppliers.manage',
                'expenses.view',
                'expenses.manage',
                'reports.view',
                'users.view',
            ],
            'Cashier' => [
                'dashboard.view',
                'pos.access',
                'sales.view',
                'sales.create',
                'sales.return',
                'customers.view',
            ],
            'Inventory Manager' => [
                'dashboard.view',
                'products.view',
                'inventory.view',
                'inventory.adjust',
                'inventory.transfer',
                'purchases.view',
                'purchases.create',
                'purchases.receive',
                'suppliers.view',
            ],
        ];

        foreach ($roles as $name => $permissionSlugs) {
            $role = Role::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => null]
            );

            $role->permissions()->sync(Permission::whereIn('slug', $permissionSlugs)->pluck('id'));
        }

        $admin = User::where('email', 'admin@gmail.com')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        if ($admin && $adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
