<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo('change_password');

        $role->givePermissionTo('users_view');
        $role->givePermissionTo('users_create');
        $role->givePermissionTo('users_update');
        $role->givePermissionTo('users_delete');
        $role->givePermissionTo('users_export');
        $role->givePermissionTo('users_all');

        $role->givePermissionTo('roles_view');
        $role->givePermissionTo('roles_create');
        $role->givePermissionTo('roles_update');
        $role->givePermissionTo('roles_delete');

        $role->givePermissionTo('outlet_requirements_view');
        $role->givePermissionTo('submitted_outlet_requirements_view');
        $role->givePermissionTo('submitted_outlet_requirements_create');
        $role->givePermissionTo('submitted_outlet_requirements_update');
        $role->givePermissionTo('submitted_outlet_requirements_delete');
        $role->givePermissionTo('submitted_outlet_requirements_approval');

        $role->givePermissionTo('banks_view');
        $role->givePermissionTo('banks_create');
        $role->givePermissionTo('banks_update');
        $role->givePermissionTo('banks_delete');
        $role->givePermissionTo('banks_export');

        $role->givePermissionTo('bank_accounts_view');
        $role->givePermissionTo('bank_accounts_create');
        $role->givePermissionTo('bank_accounts_update');
        $role->givePermissionTo('bank_accounts_delete');
        $role->givePermissionTo('bank_accounts_export');

        $role->givePermissionTo('merchants_view');
        $role->givePermissionTo('merchants_create');
        $role->givePermissionTo('merchants_update');
        $role->givePermissionTo('merchants_update_status');
        $role->givePermissionTo('merchants_delete');
        $role->givePermissionTo('merchants_export');
        $role->givePermissionTo('merchants_import');

        $role->givePermissionTo('deposits_view');
        $role->givePermissionTo('deposits_create');
        $role->givePermissionTo('deposits_update');
        $role->givePermissionTo('deposits_delete');
        $role->givePermissionTo('deposits_export');
        $role->givePermissionTo('deposits_update_status');
        $role->givePermissionTo('deposits_summary');

        $role->givePermissionTo('withdrawals_view');
        $role->givePermissionTo('withdrawals_create');
        $role->givePermissionTo('withdrawals_update');
        $role->givePermissionTo('withdrawals_delete');
        $role->givePermissionTo('withdrawals_export');

        $role->givePermissionTo('kyc_view');
        $role->givePermissionTo('kyc_create');
        $role->givePermissionTo('kyc_update');
        $role->givePermissionTo('kyc_delete');
        $role->givePermissionTo('kyc_update_status');

        $role->givePermissionTo('exchange_rates_view');
        $role->givePermissionTo('exchange_rates_create');
        $role->givePermissionTo('exchange_rates_update');
        $role->givePermissionTo('exchange_rates_delete');
        $role->givePermissionTo('exchange_rates_export');

        $role->givePermissionTo('balances_view');
        $role->givePermissionTo('balances_create');
        $role->givePermissionTo('balances_update');
        $role->givePermissionTo('balances_delete');
        $role->givePermissionTo('balances_export');

        $role = Role::create(['name' => 'Internal Banking']);
        $role->givePermissionTo('change_password');

        $role->givePermissionTo('banks_view');
        $role->givePermissionTo('banks_create');
        $role->givePermissionTo('banks_update');
        $role->givePermissionTo('banks_delete');
        $role->givePermissionTo('banks_export');

        $role->givePermissionTo('bank_accounts_view');
        $role->givePermissionTo('bank_accounts_create');
        $role->givePermissionTo('bank_accounts_update');
        $role->givePermissionTo('bank_accounts_delete');
        $role->givePermissionTo('bank_accounts_export');

        $role->givePermissionTo('deposits_view');
        $role->givePermissionTo('deposits_create');
        $role->givePermissionTo('deposits_update');
        $role->givePermissionTo('deposits_delete');
        $role->givePermissionTo('deposits_export');

        $role->givePermissionTo('withdrawals_view');
        $role->givePermissionTo('withdrawals_create');
        $role->givePermissionTo('withdrawals_update');
        $role->givePermissionTo('withdrawals_delete');
        $role->givePermissionTo('withdrawals_export');

        $role = Role::create(['name' => 'Merchant']);
        $role->givePermissionTo('frontend');
        $role->givePermissionTo('deposits_view');
        $role->givePermissionTo('deposits_create');
        $role->givePermissionTo('deposits_update');
        $role->givePermissionTo('deposits_summary');

        $role->givePermissionTo('withdrawals_view');
        $role->givePermissionTo('withdrawals_create');
        $role->givePermissionTo('withdrawals_update');

        $role->givePermissionTo('kyc_view');
        $role->givePermissionTo('kyc_create');

        $role->givePermissionTo('bank_accounts_view');
        $role->givePermissionTo('exchange_rates_view');
        $role->givePermissionTo('balances_view');
    }
}
