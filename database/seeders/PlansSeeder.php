<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

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
        Plan::create([
            'name' => 'Starter',
            'description' => 'For starting societies',
            'charges' => '[
                {"source": "interest", "type": "percentage", "fee": 5}, 
                {"source": "levy", "type": "flat", "fee": 50000}
            ]',
            'features' => '{
                "members": 200,
                "loan_types": 3,
                "module": "subscription",
                "savings_and_withdrawals": true,
                "transactions_notifications": true,
                "hosting_domain_security": true,
                "support_and_maintenance": true,
                "books_of_account": false,
                "store_management": false,
                "mobile_app": false
            }',
        ]);
    }
}
