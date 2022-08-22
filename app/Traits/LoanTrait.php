<?php

namespace App\Traits;

use App\Models\Member;
use Illuminate\Support\Facades\DB;

trait LoanTrait
{

    /**
     * Get loan entity data attribute value
     * 
     * @param Loan $loan
     * @param string $attributeName
     * @param boolean $all - Optionally get all attributes
     * 
     * @return mixed
     */
    public function getAttr($loan, $attributeName, $all = false)
    {
        $entityData = json_decode($loan->entity_data);

        return $all ? $entityData : $entityData->{$attributeName};
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
        $guarantors = $this->getAttr($loan, 'guarantors');

        $validationRule = ['guarantors' => 'required|array|size:' . $guarantors->number];

        $conditionalRule = $guarantors->have_accounts ? 'int|exists:members,id' : 'string';

        $validationRule['guarantors.*'] = $conditionalRule;

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

    /**
     * Compose loan subscription fields
     * @param Loan $loan
     * 
     * @return array
     */
    public function subFields($loan)
    {
        $fixedInterest = $this->getAttr($loan, 'fixed_interest');
        $accumulatedInterest = $this->getAttr($loan, 'accumulated_interest');

        $subFields = [
            'amount' => [
                'type' => 'number',
                'disabled' => false,
                'hidden' => false
            ],
            'interest' => [
                'type' => 'number',
                'disabled' => true,
                'hidden' => false
            ]
        ];


        if ($fixedInterest) {
            $entityData['deduction'] = [
                'type' => 'number',
                'disabled' => true,
                'hidden' => false
            ];
        }

        if ($accumulatedInterest) {
            $subFields['accumulated_interest'] = [
                'type' => 'number',
                'disabled' => true,
                'hidden' => false
            ];
        }


        return $subFields;
    }

    /**
     * Compose loan application fields
     * @param Loan $loan
     * 
     * @return array
     */
    public function applicationFields($loan)
    {
        $duration = $this->getAttr($loan, 'duration');
        $guarantors = $this->getAttr($loan, 'guarantors');

        $applicationFields = [];
        
        if (count((array)$duration) > 1) {
            $applicationFields['duration'] = [
                'type' => 'select',
                'options' => [],
                'hidden' => false,
                'disabled' => false
            ];

            foreach ($duration as $key => $value) {
                $formattedLabel = str_replace('_', ' ', $key);
                $formattedLabel = ucwords($formattedLabel) . ' (' . $value . ' months)';

                array_push($applicationFields['duration']['options'], [
                    'value' => $key,
                    'label' => $formattedLabel
                ]);
            }
        }

        if ($guarantors->number > 0) {
            $members = DB::select("SELECT id as value, concat(firstname, ' ', lastname, ' ', othernames) as label from members where deleted_at is null  order by label");

            $applicationFields['guarantors'] = [];

            for ($i = 1; $i <= $guarantors->number; $i++) {
                if ($guarantors->have_accounts) {
                    array_push($applicationFields['guarantors'], [
                        'type' => 'select',
                        'options' => $members,
                        'hidden' => false,
                        'disabled' => false
                    ]);
                } else {
                    array_push($applicationFields['guarantors'], [
                        [
                            'type' => 'text',
                            'hidden' => false,
                            'disabled' => false
                        ],
                        [
                            'type' => 'email',
                            'hidden' => false,
                            'disabled' => false
                        ]
                    ]);
                }
            }
        }

        return $applicationFields;
    }
}
