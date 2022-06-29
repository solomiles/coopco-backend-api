<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens;

    protected $hidden = [
        'password', 'remember_token', 'type',
    ];
}
