<?php

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $exchangeRates = ExchangeRate::create([
            'currency_from' => 'USD',
            'currency_to' => 'PHP',
            'rate' => '51.60'
        ]);

        $exchangeRates = ExchangeRate::create([
            'currency_from' => 'PHP',
            'currency_to' => 'USD',
            'rate' => '0.019 '
        ]);
    }
}
