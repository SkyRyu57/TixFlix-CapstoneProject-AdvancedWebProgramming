<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi (Mass Assignment).
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'country',
    ];

    /**
     * Atribut yang disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ==========================================
     * HELPER FUNGSI ROLE
     * ==========================================
     */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    public function isCustomer()
    {
        return $this->role === 'user';
    }

    /**
     * ==========================================
     * RELASI ELOQUENT
     * ==========================================
     */

    // Event yang dibuat oleh user ini (sebagai Organizer)
    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    // Event yang disetujui oleh user ini (jika dia Admin)
    public function approvedEvents()
    {
        return $this->hasMany(Event::class, 'approved_by');
    }

    // Event yang pernah diedit oleh user ini (jika dia Admin)
    public function editedEvents()
    {
        return $this->hasMany(Event::class, 'edited_by');
    }

    // Antrean/Waiting List yang dimiliki user ini
    public function waitingLists()
    {
        return $this->hasMany(WaitingList::class);
    }
}   