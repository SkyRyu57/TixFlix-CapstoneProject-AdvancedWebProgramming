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
        'status'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($event) {
            // Membuat slug otomatis dari title saat event baru dibuat
            $event->slug = Str::slug($event->title);
        });
    }

    // Relasi balik ke User (Organizer)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi balik ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel Ticket (1 Event punya banyak Ticket)
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}