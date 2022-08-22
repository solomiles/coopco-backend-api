<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Traits\LoanTrait;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    use LoanTrait;

    /**
     * Get loan details and form fields
     * @param Request $request
     * @param int $loanId
     * 
     * @return Response
     */
    public function getOne(Request $request, int $loanId) {
        $loan = Loan::findOrFail($loanId);

        $loan->balance = 100000;
        $loan->grant_limit = $this->getAttr($loan, 'grant_limit');
        $loan->subFields = $this->subFields($loan);
        $loan->applicationFields = $this->applicationFields($loan);

        unset($loan->entity_data);
        
        return response([
            'status' => true,
            'message' => 'Fetch Successful',
            'data' => $loan
        ], 200);
    }
}
