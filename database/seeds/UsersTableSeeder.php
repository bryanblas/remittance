<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@test.com',
            'password' => 'Test12345!',
            'active' => 1
        ]);
        $user->assignRole('Super Admin');

        $user = User::create([
            'first_name' => 'Internal',
            'last_name' => 'Banking',
            'email' => 'ib@test.com',
            'password' => 'Test12345!',
            'active' => 1
        ]);
        $user->assignRole('Internal Banking');
    }
}
