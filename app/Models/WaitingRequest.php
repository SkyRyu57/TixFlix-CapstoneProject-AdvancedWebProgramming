<?php
// app/Models/WaitingRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingRequest extends Model
{
    protected $fillable = ['user_id', 'event_id', 'quantity', 'notes', 'status', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}