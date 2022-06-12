<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

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
            'firstname' => 'Ikenna',
            'lastname' => 'Adewale',
            'othernames' => 'Musa',
            'email' => 'test@coopco.com.ng',
            'password' => Hash::make('123456')
        ]);
    }
}
