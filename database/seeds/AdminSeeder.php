<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class AdminSeeder
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'billing_id' => '0',
            'login' => 'master',
            'email' => 'admin@smm.uz',
            'password' => bcrypt('webmaster'),
            'role_id' => '1',
            'ip' => '',
        ]);
    }
}
