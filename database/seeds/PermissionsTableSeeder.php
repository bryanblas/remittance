<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'change_password']);
        Permission::create(['name' => 'users_view']);
        Permission::create(['name' => 'users_create']);
        Permission::create(['name' => 'users_update']);
        Permission::create(['name' => 'users_delete']);
        Permission::create(['name' => 'users_export']);
        Permission::create(['name' => 'users_all']);

        Permission::create(['name' => 'roles_view']);
        Permission::create(['name' => 'roles_create']);
        Permission::create(['name' => 'roles_update']);
        Permission::create(['name' => 'roles_delete']);

        Permission::create(['name' => 'outlets_approval_request']);
        Permission::create(['name' => 'outlet_requirements_view']);

        Permission::create(['name' => 'submitted_outlet_requirements_create']);
        Permission::create(['name' => 'submitted_outlet_requirements_update']);
        Permission::create(['name' => 'submitted_outlet_requirements_delete']);
        Permission::create(['name' => 'submitted_outlet_requirements_view']);
        Permission::create(['name' => 'submitted_outlet_requirements_approval']);

        Permission::create(['name' => 'banks_view']);
        Permission::create(['name' => 'banks_create']);
        Permission::create(['name' => 'banks_update']);
        Permission::create(['name' => 'banks_delete']);
        Permission::create(['name' => 'banks_export']);

        Permission::create(['name' => 'bank_accounts_view']);
        Permission::create(['name' => 'bank_accounts_create']);
        Permission::create(['name' => 'bank_accounts_update']);
        Permission::create(['name' => 'bank_accounts_delete']);
        Permission::create(['name' => 'bank_accounts_export']);
        Permission::create(['name' => 'bank_accounts_get']);

        Permission::create(['name' => 'merchants_view']);
        Permission::create(['name' => 'merchants_create']);
        Permission::create(['name' => 'merchants_update']);
        Permission::create(['name' => 'merchants_update_status']);
        Permission::create(['name' => 'merchants_delete']);
        Permission::create(['name' => 'merchants_export']);
        Permission::create(['name' => 'merchants_import']);

        Permission::create(['name' => 'deposits_view']);
        Permission::create(['name' => 'deposits_create']);
        Permission::create(['name' => 'deposits_update']);
        Permission::create(['name' => 'deposits_delete']);
        Permission::create(['name' => 'deposits_export']);
        Permission::create(['name' => 'deposits_update_status']);
        Permission::create(['name' => 'deposits_summary']);

        Permission::create(['name' => 'withdrawals_view']);
        Permission::create(['name' => 'withdrawals_create']);
        Permission::create(['name' => 'withdrawals_update']);
        Permission::create(['name' => 'withdrawals_delete']);
        Permission::create(['name' => 'withdrawals_export']);

        Permission::create(['name' => 'exchange_rates_view']);
        Permission::create(['name' => 'exchange_rates_create']);
        Permission::create(['name' => 'exchange_rates_update']);
        Permission::create(['name' => 'exchange_rates_delete']);
        Permission::create(['name' => 'exchange_rates_export']);

        Permission::create(['name' => 'balances_view']);
        Permission::create(['name' => 'balances_create']);
        Permission::create(['name' => 'balances_update']);
        Permission::create(['name' => 'balances_delete']);
        Permission::create(['name' => 'balances_export']);

        Permission::create(['name' => 'kyc_view']);
        Permission::create(['name' => 'kyc_create']);
        Permission::create(['name' => 'kyc_update']);
        Permission::create(['name' => 'kyc_delete']);
        Permission::create(['name' => 'kyc_update_status']);

        Permission::create(['name' => 'frontend']);
    }
}
