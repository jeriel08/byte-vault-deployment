<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create(['name' => 'John Doe']);
        Customer::create(['name' => 'Jane Smith']);
        Customer::create(['name' => 'Alex Brown']);
    }
}