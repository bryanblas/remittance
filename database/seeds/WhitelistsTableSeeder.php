<?php

use Illuminate\Database\Seeder;
use App\Models\Whitelist;

class WhitelistsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Whitelist::truncate();

        $whitelist = Whitelist::create([
            'name' => 'Merck\'s Machine',
            'ip_address' => '192.168.10.1',
            'status' => 1
        ]);

        $whitelist = Whitelist::create([
            'name' => 'Fort Legend Wifi',
            'ip_address' => '122.49.220.26',
            'status' => 1
        ]);
    }
}
