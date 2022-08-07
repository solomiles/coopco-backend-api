<?php

namespace App\Traits;

trait LoanTrait
{
    /**
     * Calculate loan interest
     * @param Loan $loan - Loan object
     * @param array $data - Loan data array. Format: [amount => float, rate => 'st, mt, lt', duration => 'st, mt, lt']
     */
    public function calculateInterest($loan, $data) {
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
}
