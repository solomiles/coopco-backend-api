<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCredentials extends Model
{
    use HasFactory;

    protected $table = 'email_credentials';
}
