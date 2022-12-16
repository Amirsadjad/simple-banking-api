<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(['Arisha Barron','Branden Gibson','Rhonda Church','Georgina Hazel'])
            ->each(fn($name) => Customer::create(compact('name')));
    }
}
