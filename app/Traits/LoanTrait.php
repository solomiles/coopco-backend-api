<?php

namespace App\Traits;

use App\Models\Member;

trait LoanTrait
{

    /**
     * Get loan entity data attribute value
     * 
     * @param Loan $loan
     * @param string $attributeName
     * 
     * @return mixed
     */
    public function getAttr($loan, $attributeName)
    {
        $entityData = json_decode($loan->entity_data);

        return $entityData->{$attributeName};
    }

    /**
     * Check if a loan has guarantors
     * 
     * @param Loan $loan
     * @return boolean
     */
    public function hasGuarantors($loan)
    {
        return $this->getAttr($loan, 'guarantors')->number > 0;
    }

    /**
     * Get and format loan approvers from request object
     * 
     * @param Loan $loan
     * @param Request $request
     * 
     * @return array
     */
    public function getApprovers($loan, $request)
    {
        $approversData = [];

        $approvers = $this->getAttr($loan, 'approvers');

        foreach ($approvers as $approver) {
            if ($approver == 'guarantor' && $this->hasGuarantors($loan)) {
                $guarantorsNum = $this->getAttr($loan, 'guarantors')->number;
                $haveAccounts = $this->getAttr($loan, 'guarantors')->have_accounts;

                for ($i = 0; $i < $guarantorsNum; $i++) {
                    if ($haveAccounts) {
                        $member = Member::findOrFail($request->guarantors[$i]);
                        $name = implode(' ', [$member->firstname, $member->lastname, $member->othernames]);

                        array_push($approversData, [
                            'approver_name' => $name,
                            'approver_type' => 'guarantor',
                            'approver_id' => $member->id
                        ]);
                    } else {
                        array_push($approversData, [
                            'approver_name' => $request->guarantors[$i],
                            'approver_type' => 'guarantor'
                        ]);
                    }
                }
            } else {
                array_push($approversData, ['approver_type' => $approver]);
            }
        }

        return $approversData;
    }

    /**
     * Calculate loan interest
     * @param Loan $loan - Loan object
     * @param array $data - Loan data array. Format: [amount => float, rate => ['st, mt, lt'], duration => ['st, mt, lt']]
     * 
     * @return float
     */
    public function calculateInterest($loan, $data)
    {
        $formula = $this->getAttr($loan, 'formula');

        $principal = $data['amount'];
        $rate = $this->getAttr($loan, 'interest_rate')->{$data['rate']};
        $time = $this->getAttr($loan, 'duration')->{$data['duration']};

        $formula = str_replace('P', $principal, $formula);
        $formula = str_replace('R', $rate, $formula);
        $formula = str_replace('T', $time, $formula);

        $i = eval('return ' . $formula . ';');

        return $i;
    }

    /**
     * Compose guarantor validation rule
     * @param Loan $loan - Loan id
     * 
     * @return array
     */
    public function guarantorRule($loan)
    {
        $guarantors = $this->getAttr($loan, 'guarantors')->guarantors;

        $validationRule = [];

        $conditionalRule = $guarantors->have_accounts ? 'exists:members' : 'string';

        $validationRule['guarantor.*'] = 'required|' . $conditionalRule;

        return $validationRule;
    }

    /**
     * Loan grant limit validation rule
     * @param Loan $loan
     * 
     * @return array
     */
    public function grantLimitRule($loan)
    {
        $grantLimit = $this->getAttr($loan, 'grant_limit');

        $validationRule = ['amount' => 'required|numeric|min:1'];

        if ($grantLimit > 0) {
            $validationRule['amount'] .= '|max:' . $grantLimit;
        }

        return $validationRule;
    }
}
