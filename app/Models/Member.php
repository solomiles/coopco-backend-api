<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'othernames',
        'email',
        'phone',
        'gender',
    ];
}
