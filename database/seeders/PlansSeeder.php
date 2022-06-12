<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plans;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Plans::create([
            'name' => 'Gold',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
            'charge' => '500000',
            'charge_source' => 'account',
            'features' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
        ]);
    }
}
