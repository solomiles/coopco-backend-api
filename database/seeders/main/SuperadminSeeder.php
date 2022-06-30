<?php

namespace Database\Seeders\main;

use App\Models\Superadmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Superadmin::create([
            'name' => 'Test Coopco Admin',
            'username' => 'superadmin',
            'password' => Hash::make('123456')
        ]);
    }
}
