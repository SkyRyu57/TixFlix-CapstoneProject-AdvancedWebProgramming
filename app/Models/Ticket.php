<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'stock',
        'description'
    ];

    // Relasi balik: Satu Tiket dimiliki oleh satu Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}