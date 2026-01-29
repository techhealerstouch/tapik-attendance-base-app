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
                'name' => 'Dummy User' . $i,
                'email' => 'dummyuser' . $i . '@tapik.com',
                'password' => Hash::make('12345678'), // default password
                'activate_code' => 'cd' . $i,
                'activate_status' => 'activated',
                'role' => 'user',
                'qr_code_status' => 1,
                'image' => null,
                'provider' => null,
                'provider_id' => null,
                'littlelink_name' => 'cde' . $i,
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
