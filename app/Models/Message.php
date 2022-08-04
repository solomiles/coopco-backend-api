<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Attributes that are mass assignable
    protected $fillable = ['read', 'seen'];

    /**
     * Return the member that sent a message
     */
    public function fromMember(){
        return $this->belongsTo(Member::class, 'from_id');
    }

    /**
     * Return a member that received the message
     */
    public function toMember(){
        return $this->belongsTo(Member::class, 'to_id');
    }

    /**
     * Return the admin that sent the message
     */
    public function fromAdmin(){
        return $this->belongsTo(Admin::class, 'from_id');
    }

    /**
     * Return the admin that received the message
     */
    public function toAdmin(){
        return $this->belongsTo(Admin::class, 'to_id');
    }
}
