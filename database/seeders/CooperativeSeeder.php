<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cooperative;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Cooperative::create([
            'name' => 'Test Cooperative',
            'country_id' => '1',
            'plan_id' => '1',
            'type' => 'testcoop.com'
        ]);
    }
}
