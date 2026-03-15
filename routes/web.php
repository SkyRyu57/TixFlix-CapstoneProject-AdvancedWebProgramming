<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

// 1. HALAMAN UTAMA
Route::get('/', function () {
    return view('welcome');
});

// 2. AUTHENTICATION (GUEST - Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/register', function () { return view('auth.register'); })->name('register');
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

// 3. TERPROTEKSI (Harus Login)
Route::middleware('auth')->group(function () {
    
    // --- PUSAT KENDALI REDIRECT (PENTING) ---
    Route::get('/home', function () {
        $role = auth()->user()->role;
        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'organizer') return redirect()->route('organizer.dashboard');
        return redirect()->route('dashboard'); // Untuk customer
    })->name('home');

    // --- PROSES KELUAR (LOGOUT) ---
    
    // Jalur Resmi (POST): Benar-benar mengeluarkan user
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Jalur Pengalihan (GET): Jika user ngetik manual /logout di URL
    // Kita arahkan balik ke /home supaya mental ke dashboard masing-masing
    Route::get('/logout', function () {
        return redirect()->route('home');
    });

    // --- DASHBOARD: ADMIN ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return "Halo Admin! Ini Dashboard kamu. <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('admin.dashboard');
    });

    // --- DASHBOARD: ORGANIZER ---
    Route::middleware('role:organizer')->group(function () {
        Route::get('/organizer/dashboard', function () {
            return "Halo Organizer! Kelola event di sini. <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('organizer.dashboard');
    });

    // --- DASHBOARD: CUSTOMER ---
    Route::middleware('role:customer')->group(function () {
        Route::get('/dashboard', function () {
            return "Selamat datang, Customer! <br> <form action='".route('logout')."' method='POST'>".csrf_field()."<button type='submit'>Logout Resmi</button></form>";
        })->name('dashboard');
    });
});