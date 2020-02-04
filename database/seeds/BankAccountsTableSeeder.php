<?php

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $bankAccount = BankAccount::create([
            'type' => '1',
            'bank_id' => '1',
            'bank_branch' => 'BPI Net Quad BGC',
            'number' => '12345678',
            'name' => 'Pesofwd Inc',
            'status' => '1'
        ]);

        $bankAccount = BankAccount::create([
            'type' => '1',
            'bank_id' => '2',
            'bank_branch' => 'BDO Fort Legend BGC',
            'number' => '87654321',
            'name' => 'Pesofwd Inc',
            'status' => '1'
        ]);
    }
}
