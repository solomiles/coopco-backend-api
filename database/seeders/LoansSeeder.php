<?php

namespace Database\Seeders;

use App\Models\Loan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Loan::create(
            [
                'name' => 'Cooperative Loan',
                'short_name' => 'COOP-L',
                'entity_data' => '
                {
                    "formula": "(P*R*(T+1)/200)",
                    "interest_rate": {
                        "lt": 0.67,
                        "st": 15
                    },
                    "duration": {
                        "lt": 36,
                        "st": 15
                    },
                    "grant_limit": 1000000,
                    "deduction": true,
                    "accumulated_interest": true,
                    "guarantors": {
                        "number": 3,
                        "have_accounts": true
                    }
                }'
            ]
        );
    }
}
