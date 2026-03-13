<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // 1. Sesuaikan Primary Key karena menggunakan VARCHAR(50)
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. Sesuaikan nama kolom Timestamp
    const CREATED_AT = 'tgl_dibuat';
    const UPDATED_AT = 'tgl_diubah';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',       // Harus dimasukkan ke fillable agar bisa diisi string manual (misal UUID)
        'nama',     // Diubah dari 'name' menjadi 'nama'
        'email',
        'no_telp',  // Kolom baru
        'role',     // Kolom baru
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        // 'remember_token', // Saya hapus karena tidak ada di tabelmu. Bisa dikembalikan jika nanti kamu menambahkannya di DB.
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime', // Saya hapus karena kolom ini tidak ada di tabelmu
            'password' => 'hashed', // Biarkan ini, sangat berguna agar Laravel otomatis melakukan hashing (bcrypt) saat save password
        ];
    }
}