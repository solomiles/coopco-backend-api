<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApprover extends Model
{
    use HasFactory;

    protected $fillable = ['approver_name', 'approver_type', 'loan_application_id', 'approver_id', 'from_approver'];
}
