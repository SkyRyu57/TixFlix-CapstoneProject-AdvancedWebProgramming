<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // CEK APAKAH EMAIL TERDAFTAR
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // KASUS 1: AKUN TIDAK DITEMUKAN
            return back()->with('account_not_found', 'Email ' . $request->email . ' belum terdaftar. Silakan registrasi terlebih dahulu.')
                         ->withInput($request->only('email'));
        }

        // CEK APAKAH PASSWORD SESUAI
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if ($user->role == 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role == 'organizer') {
                return redirect()->intended('/organizer/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        // KASUS 2: PASSWORD SALAH (email ditemukan tapi password salah)
        return back()->with('wrong_password', 'Password yang Anda masukkan salah. Silakan coba lagi.')
                     ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}