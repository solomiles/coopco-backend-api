<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    /**
     * Many-to-One relationship with loans table
     */
    public function loan() {
        return $this->belongsTo(Loan::class);
    }
}
