<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Tixflix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0B0F19] flex items-center justify-center min-h-screen py-10">
    <div class="bg-[#1A1D24] p-8 rounded-2xl shadow-2xl w-full max-w-md border border-slate-800">
        
        <div class="flex justify-center items-center gap-2 mb-8">
            <div class="bg-[#FF4D4F] p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Tixflix</h1>
        </div>

        <h2 class="text-xl font-semibold mb-6 text-center text-slate-200">Buat Akun Baru</h2>

        @if ($errors->any())
            <div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg">
                <p class="font-bold text-sm">Gagal mendaftar:</p>
                <ul class="list-disc list-inside text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf 
            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-medium mb-2">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors @error('name') border-red-500 @enderror" 
                       placeholder="Contoh: Ricko IT" required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-medium mb-2">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors @error('email') border-red-500 @enderror" 
                       placeholder="email@contoh.com" required>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-medium mb-2">No. Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" 
                       class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors @error('phone_number') border-red-500 @enderror" 
                       placeholder="08123xxx">
                @error('phone_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-medium mb-2">Daftar Sebagai (Role) *</label>
                <select name="role" class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors appearance-none">
                    <option value="customer" class="bg-slate-900" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer (Penonton)</option>
                    <option value="organizer" class="bg-slate-900" {{ old('role') == 'organizer' ? 'selected' : '' }}>Organizer (Penyelenggara)</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-slate-400 text-sm font-medium mb-2">Password *</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors @error('password') border-red-500 @enderror" 
                       required>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-slate-400 text-sm font-medium mb-2">Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" 
                       required>
            </div>

            <button type="submit" class="w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-300">
                Daftar Sekarang
            </button>

            <p class="mt-6 text-center text-sm text-slate-400">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Login di sini</a>
            </p>
        </form>
    </div>
</body>
</html>