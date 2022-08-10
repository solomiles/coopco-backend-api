<?php

namespace App\Traits;

use App\Models\Loan;

trait LoanTrait
{
    /**
     * Calculate loan interest
     * @param Loan $loan - Loan object
     * @param array $data - Loan data array. Format: [amount => float, rate => ['st, mt, lt'], duration => ['st, mt, lt']]
     * 
     * @return float
     */
    public function calculateInterest($loan, $data)
    {
        $formula = json_decode($loan->entity_data)->formula;

        $principal = $data['amount'];
        $rate = json_decode($loan->entity_data)->interest_rate->{$data['rate']};
        $time = json_decode($loan->entity_data)->duration->{$data['duration']};

        $formula = str_replace('P', $principal, $formula);
        $formula = str_replace('R', $rate, $formula);
        $formula = str_replace('T', $time, $formula);

        $i = eval('return ' . $formula . ';');

        return $i;
    }

    /**
     * Compose guarantor validation rules
     * @param int $loanId - Loan id
     * 
     * @return array
     */
    public function guarantorRules($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $guarantors = json_decode($loan->guarantors);

        $validation_rules = [];

        for ($i = 1; $i <= 3; $i++) {
            $guarantors->have_accounts
                ? array_push($validation_rules, [
                    'guarantor' . $i => 'required|exists:members'
                ])
                : array_push($validation_rules, [
                    'guarantor' . $i => 'required|string'
                ]);
        }

        return $validation_rules;
    }
}
