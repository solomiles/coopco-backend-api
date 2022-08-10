<?php

namespace App\Traits;

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
     * @param Loan $loan - Loan id
     * 
     * @return array
     */
    public function guarantorRules($loan)
    {
        $guarantors = json_decode($loan->guarantors);

        $validation_rules = [];

        $conditionalRule = $guarantors->have_accounts ? 'exists:members' : 'string';
        for ($i = 1; $i <= 3; $i++) {
            array_push($validation_rules, [
                'guarantor' . $i => 'required|' . $conditionalRule
            ]);
        }

        return $validation_rules;
    }

    /**
     * Loan grant limit validation rule
     * @param Loan $loan
     * 
     * @return array
     */
    public function grantLimitRule($loan) {
        $grantLimit = json_decode($loan->grant_limit);

        $validation_rule = ['amount' => 'required|numeric|min:1'];

        if($grantLimit > 0) {
            $validation_rule['amount'] .= '|max:'.$grantLimit;
        }

        return $validation_rule;
    }
}
