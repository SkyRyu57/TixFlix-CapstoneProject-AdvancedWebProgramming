<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'transaction_id', 'order_id', 'amount', 
        'proof_image', 'notes', 'status', 'expired_at', 'verified_at', 'rejected_reason'
    ];
    
    protected $casts = [
        'expired_at' => 'datetime',
        'verified_at' => 'datetime',
        'amount' => 'integer',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}