<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // 1. Ini fungsi bawaan kamu untuk halaman Dashboard (Home)
    public function index()
    {
        $pilihanEvents = Event::with('category')->inRandomOrder()->take(3)->get();
        $terbaruEvents = Event::with('category')->latest()->take(6)->get();

        return view('dashboard.customer', compact('pilihanEvents', 'terbaruEvents'));
    }

    // 2. INI TAMBAHAN BARU: Fungsi khusus untuk halaman Concerts
    public function concerts()
    {
        // Mengambil data event khusus kategori 'concert' dan status 'published'
        $events = Event::with('category')
            ->whereHas('category', function ($query) {
                // PENTING: Cek tabel categories di database kamu.
                // Kalau nama slug-nya bahasa Indonesia, ganti 'concert' jadi 'konser'
                $query->where('slug', 'concert'); 
            })
            ->where('status', 'published') // Cek status pastikan 'published'
            ->latest()
            ->get();

        // Melempar variabel $events ke tampilan concerts.blade.php
        return view('dashboard.concerts', compact('events')); 
    }
}