<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data akun contoh
        $users = [
            [
                'name'         => 'Ricko',
                'email'        => 'admin@gmail.com',
                'phone_number' => '081234567890',
                'password'     => Hash::make('password123'),
                'role'         => 'admin',
                'country'      => 'Indonesia',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
            [
                'name'         => 'Organizer',
                'email'        => 'organizer@gmail.com',
                'phone_number' => '081234567891',
                'password'     => Hash::make('password123'),
                'role'         => 'organizer',
                'country'      => 'Indonesia',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ],
            [
                'name'         => 'Customer',
                'email'        => 'customer@gmail.com',
                'phone_number' => '081234567892',
                'password'     => Hash::make('password123'),
                'role'         => 'customer',
                'country'      => 'Indonesia',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]
        ];

        // Masukkan data ke tabel users
        foreach ($users as $user) {
            User::create($user);
        }
    }
}