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
            'phone_number' => 'nullable|numeric|digits_between:10,15', // Validasi angka & panjang
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'required|in:admin,organizer,customer', // Pastikan sesuai Enum
        ]);

        // 2. SIMPAN DATA KE DATABASE
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ]);

        // 3. AUTO LOGIN & REDIRECT
        return redirect()->route('login');
    }
}