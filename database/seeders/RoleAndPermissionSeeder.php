<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsArray = [
            // IdType Permissions
            'read_id_type',
            'write_id_type',
            'delete_id_type',

            // Product Permissions
            'read_product',
            'write_product',
            'delete_product',

            //Product Type Permissions
            'read_product_type',
            'write_product_type',
            'delete_product_type',

            // Tax Permissions
            'read_tax',
            'write_tax',
            'delete_tax',

            // Customer Permissions
            'read_customer',
            'write_customer',
            'delete_customer',

            //Vendor Permissions
            'read_vendor',
            'write_vendor',
            'delete_vendor',

            // Purchase Permissions
            'read_purchase',
            'write_purchase',
            'delete_purchase',

            // Sale Permissions
            'read_sale',
            'write_sale',
            'delete_sale',

            // Credit Permission
            'read_credit',
            'write_credit',
            'delete_credit',

            // Debit Permission
            'read_debit',
            'write_debit',
            'delete_debit',

            // Receipt Permission
            'read_receipt',
            'write_receipt',
            'delete_receipt',

            // Voucher Permission
            'read_voucher',
            'write_voucher',
            'delete_voucher',

            // Income permission
            'read_income',
            'write_income',
            'delete_income',

            // Expense permission
            'read_expense',
            'write_expense',
            'delete_expense'
        ];

        foreach ($permissionsArray as $permissionItem) {
            Permission::query()->create([
                'name' => $permissionItem
            ]);
        }

        // Admin Role
        $adminRole = Role::query()->create([
            'name' => 'admin'
        ]);

        foreach (Permission::all() as $permission) {
            $adminRole->givePermissionTo($permission);
        }

        //Operator Role
        $operatorRole = Role::query()->create([
            'name' => 'operator'
        ]);

        $permissions = Permission::query()->whereIn('name', [
            'read_id_type',

            'read_product',
            'write_product',

            'read_product_type',
            'write_product_type',

            'read_tax',

            'read_customer',
            'write_customer',

            'read_vendor',
            'write_vendor',

            'read_purchase',
            'write_purchase',
            'delete_purchase',

            'read_sale',
            'write_sale',

            'read_credit',
            'write_credit',

            'read_debit',
            'write_debit',

            'read_receipt',
            'write_receipt',

            'read_voucher',
            'write_voucher',

            'read_income',
            'write_income',

            'read_expense',
            'write_expense',
        ])->get();

        foreach ($permissions as $permission) {
            $operatorRole->givePermissionTo($permission);
        }

        // Vendor Role
        $vendorRole = Role::query()->create([
            'name' => 'vendor'
        ]);

        $permissions = Permission::query()->whereIn('name', [
            'read_purchase',
            'read_voucher',
            'read_vendor',
        ])->get();

        foreach ($permissions as $permission) {
            $vendorRole->givePermissionTo($permission);
        }

        // Customer Role
        $customerRole = Role::query()->create([
            'name' => 'customer'
        ]);

        $permissions = Permission::query()->whereIn('name', [
            'read_sale',
            'read_receipt',
            'read_customer',
        ])->get();

        foreach ($permissions as $permission) {
            $customerRole->givePermissionTo($permission);
        }
    }
}
