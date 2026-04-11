<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Eticket;

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
        'snap_token'
        'payment_method',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Membuat nomor referensi unik, contoh: TRX-1710482938
            $transaction->reference_number = 'TRX-' . time() . strtoupper(Str::random(4));
        });
    }

    // Relasi: Transaksi ini milik siapa?
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}