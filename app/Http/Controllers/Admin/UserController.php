<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")->orWhere('email', 'ilike', "%{$search}%");
            });
        }
        if ($request->filled('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            if ($request->status == 'active') $query->where('is_active', true);
            elseif ($request->status == 'suspended') $query->where('is_active', false);
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:admin,organizer,customer',
            'is_active' => 'required|boolean',
        ]);
        $user->update($request->only('name', 'email', 'phone_number', 'role', 'is_active'));
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function toggleSuspend(User $user)
    {
        if ($user->id == auth()->id()) return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        $user->is_active = !$user->is_active;
        $user->save();
        return back()->with('success', 'Status user diubah.');
    }
}