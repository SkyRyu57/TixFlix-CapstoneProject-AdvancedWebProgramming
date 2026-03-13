<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel users
        $users = User::all();
        
        // Mengirim data ke view 'users.index'
        return view('users.index', compact('users'));
    }
}