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
        // 1. VALIDASI DATA
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|numeric|digits_between:10,15',
            'password'     => 'required|string|min:6|confirmed',
            'role'         => 'required|in:admin,organizer,customer',
        ]);

        // 2. SIMPAN DATA KE DATABASE (HAPUS COUNTRY)
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