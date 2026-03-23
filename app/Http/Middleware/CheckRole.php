<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user ada di dalam daftar $roles yang diizinkan
        $userRole = Auth::user()->role;
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 3. JIKA TIDAK MEMILIKI AKSES:
        // Jika permintaan datang dari API (AJAX/Postman), kirim JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke halaman ini.',
                'your_role' => $userRole
            ], 403);
        }

        // Jika akses dari Browser, arahkan ke halaman 'home' agar diredirect otomatis ke dashboard yang benar
        return redirect()->route('home')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}