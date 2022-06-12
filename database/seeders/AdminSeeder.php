<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Admin::create([
            'name' => 'COOPCO',
            'username' => 'admin',
            'password' => '123456',
            'type' => 'default',
            'schema' => 'example',
            'cooperative_id' => '1',
        ]);
    }
}
