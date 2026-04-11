<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Eticket;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'reference_number',
        'snap_token'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Membuat nomor referensi unik, contoh: TRX-1710482938
            $transaction->reference_number = 'TRX-' . time() . strtoupper(Str::random(4));
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
}