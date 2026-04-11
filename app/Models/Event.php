<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'location',
        'banner',
        'start_date',
        'end_date',
        'status',
        'approved_by', // Admin yang acc
        'edited_by',   // Admin yang edit terakhir
        'approved_at'
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            $event->slug = Str::slug($event->title);
        });
    }

    // Relasi balik ke User (Organizer)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Admin yang menyetujui
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel Ticket (1 Event punya banyak Ticket)
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Relasi dengan reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Update rating
    public function updateRating()
    {
        $this->avg_rating = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }
    public function waitingRequests()
    {
        return $this->hasMany(\App\Models\WaitingRequest::class);
    }
}