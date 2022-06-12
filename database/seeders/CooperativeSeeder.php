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
            'name' => '',
            'description' => '',
            'country_id' => '',
            'location' => '',
            'plan_id' => '',
            'type' => '',
            'customizations' => '',
            'active' => '',
        ]);
    }
}
