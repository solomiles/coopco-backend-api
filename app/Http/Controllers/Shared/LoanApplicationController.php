<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanApprover;
use App\Traits\LoanTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    use LoanTrait;

    /**
     * Apply for a loan
     * 
     * @param Request $request
     * @param int $loanId
     * 
     * @return Response
     */
    public function apply(Request $request, int $loanId)
    {
        $loan = Loan::findOrFail($loanId);

        $validate = $this->validator($request, $loan);

        if ($validate->fails()) {
            return response([
                'status' => false,
                'errors' => $validate->errors()->messages()
            ], 400);
        }

        $application = new LoanApplication();
        $application->amount = $request->amount;
        $application->purpose = $request->purpose;
        $application->loan_id = $loan->id;

        $application->save();

        $this->storeApprovers($loan, $application, $request);

        return response([
            'status' => true,
            'message' => 'Loan Application Sent'
        ], 200);
    }

    /**
     * Store loan approvers
     * 
     * @param Loan $loan
     * @param LoanApplication $application
     * @param Request $request
     * 
     * @return void;
     */
    public function storeApprovers($loan, $application, $request) {
        $appoversData = $this->getApprovers($loan, $request);

        $lastApproverId = 0;
        $lastApproverType = '';

        foreach($appoversData as $data) {
            $data['loan_application_id'] = $application->id;

            if($lastApproverId != 0 && $lastApproverType != $data['approver_type']) {
                $data['from_approver'] = $lastApproverId;
            }

            $approver = new LoanApprover($data);
            $approver->save();

            $lastApproverId = $approver->id;
            $lastApproverType = $approver->approver_type;
        }

        return;
    }

    /**
     * Loan application validation rules
     * 
     * @param Request $request
     * @param Loan $loan
     * 
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($request, $loan)
    {
        $specialRules = array_merge($this->guarantorRule($loan), $this->grantLimitRule($loan));

        return Validator::make($request->all(), array_merge([
            'purpose' => 'string'
        ], $specialRules));
    }
}
