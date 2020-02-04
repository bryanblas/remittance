<?php

use Illuminate\Database\Seeder;

class SystemSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'property_name' => 'USER_LOCK_DURATION',
                'value' => '24',
                'description' => 'Number of hours the user will be locked when max login attempts is reached.'
            ],
            [
                'property_name' => 'MAX_LOGIN_ATTEMPTS',
                'value' => '5',
                'description' => 'Number of attempts the user can try login login before lock down.'
            ],
            [
                'property_name' => 'PAGINATION_VALUE',
                'value' => '10',
                'description' => 'Number of items to be displayed in the table/list.'
            ],
            [
                'property_name' => 'ENABLE_MAINTENANCE',
                'value' => '0',
                'description' => 'This toggles the maintenance page in the UI.'
            ],
            [
                'property_name' => 'ENABLE_IP_RESTRICTION',
                'value' => '0',
                'description' => 'This toggle allows to enable or disable the checking of IP during login.'
            ],
        ];

        DB::table('system_settings')->insert($settings);
    }
}
