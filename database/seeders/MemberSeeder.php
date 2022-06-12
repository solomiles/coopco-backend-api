<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Member::create([
            'firstname' => '',
            'lastname' => '',
            'othernames' => '',
            'email' => '',
            'password' => '',
            'gender' => '',
            'cooperative_id' => '',
            'phone' => '',
        ]);
    }
}
