<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Eticket;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'reference_number',
        'snap_token',
        'payment_method',
        'paid_at',
        'payment_proof',
        'payment_notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->reference_number)) {
                $transaction->reference_number = 'TRX-' . time() . strtoupper(Str::random(4));
            }
        });
    }

    public function etickets()
    {
        return $this->hasMany(Eticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}