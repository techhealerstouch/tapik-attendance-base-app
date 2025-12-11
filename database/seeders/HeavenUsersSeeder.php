<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HeavenUsersSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 150; $i++) {

            User::create([
                'name' => 'User ' . $i,
                'email' => 'delavictoria12+' . $i . '@gmail.com',
                'password' => Hash::make('password123'), // default password
                'activate_code' => 'CODE' . $i,
                'activate_status' => 'activated',
                'role' => 'user',
                'qr_code_status' => 1,
                'image' => null,
                'provider' => null,
                'provider_id' => null,
                'littlelink_name' => null,
                'littlelink_description' => null,
                'mobile_number' => null,
                'website' => null,
                'block' => 'no',
                'theme' => 'default',
                'rfid_no' => null,
            ]);
        }
    }
}
