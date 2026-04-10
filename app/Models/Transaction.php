<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'ticket_id',
        'event_id',
        'total_price',
        'quantity',
        'status',
        'expires_at',
        'paid_at',
        'reference_number',
        'snap_token',
        'payment_method',
    ];

    // app/Models/Transaction.php
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}