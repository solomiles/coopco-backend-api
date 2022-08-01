<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * Return sender
     */
    public function memberfrom(){
        return $this->belongsTo(Member::class, 'from_id');
    }

    /**
     * Return recipient
     */
    public function memberto(){
        return $this->belongsTo(Member::class, 'to_id');
    }

    /**
     * Return sender
     */
    public function adminfrom(){
        return $this->belongsTo(Admin::class, 'from_id');
    }

    /**
     * Return recipient
     */
    public function adminto(){
        return $this->belongsTo(Admin::class, 'to_id');
    }
}
