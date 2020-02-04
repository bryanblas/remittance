<?php

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BanksTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $bank = Bank::create([
            'code' => 'BPI',
            'name' => 'Bank of Philippine Islands',
            'status' => '1',
        ]);

        $bank = Bank::create([
            'code' => 'BDO',
            'name' => 'Banco De Oro',
            'status' => '1',
        ]);
    }
}
