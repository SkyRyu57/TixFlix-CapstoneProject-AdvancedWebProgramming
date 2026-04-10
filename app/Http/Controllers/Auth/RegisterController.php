<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // 1. VALIDASI DATA dengan unique untuk email dan phone_number
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'password'     => 'required|string|min:6|confirmed',
            'role'         => 'required|in:admin,organizer,customer',
        ], [
            'email.unique' => 'Email sudah terdaftar! Silakan gunakan email lain.',
            'phone_number.unique' => 'Nomor telepon sudah terdaftar! Silakan gunakan nomor lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // 2. SIMPAN DATA KE DATABASE
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
        ]);

        // 3. AUTO LOGIN setelah registrasi
        Auth::login($user);

        // 4. REDIRECT BERDASARKAN ROLE
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'organizer') {
            return redirect()->route('organizer.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
}