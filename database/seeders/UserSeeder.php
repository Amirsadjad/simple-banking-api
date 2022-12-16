<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kiansoft.ir',
            'email_verified_at' => now(),
            'password' => bcrypt('@Aa1234567'),
            'role' => UserRoleEnum::Admin
        ]);
    }
}
