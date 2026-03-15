<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Eticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'ticket_id',
        'user_id',
        'ticket_code',
        'is_scanned',
        'scanned_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($eticket) {
            // Membuat kode tiket unik, contoh: TIX-ABC123XYZ
            // Kode ini yang nanti bakal lu ubah jadi QR Code di frontend/email
            $eticket->ticket_code = 'TIX-' . strtoupper(Str::random(10));
        });
    }

    // Relasi ke transaksi
    public function transaction() { return $this->belongsTo(Transaction::class); }
    // Relasi ke jenis tiket
    public function ticket() { return $this->belongsTo(Ticket::class); }
    // Relasi ke pemilik
    public function user() { return $this->belongsTo(User::class); }
}