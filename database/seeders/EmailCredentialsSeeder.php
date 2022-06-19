<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailCredentials;

class EmailCredentialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        EmailCredentials::create([
            'mailer' =>  'smtp',
            'host' =>  'smtp.gmail.com',
            'port' =>  '465',
            'username' =>  'test@coopco.com.ng',
            'password' =>  '@coopcoTEST123',
            'encryption' =>  'ssl',
            'from_address' =>  'test@coopco.com.ng',
            'from_name' =>  'Coopco Test Cooperative'
        ]);
    }
}
