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

    // Relasi dengan waiting list
    public function waitingLists()
    {
        return $this->hasMany(WaitingList::class);
    }

    // Cek apakah ada yang waiting
    public function hasWaitingList()
    {
        return $this->waitingLists()->where('status', 'waiting')->exists();
    }

    // Jumlah waiting list
    public function waitingCount()
    {
        return $this->waitingLists()->where('status', 'waiting')->sum('quantity');
    }
}