<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Tixflix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0B0F19] flex items-center justify-center min-h-screen py-10">

    <div class="bg-[#1A1D24] p-8 rounded-2xl shadow-2xl w-full max-w-md border border-slate-800">
        
        <div class="flex justify-center items-center gap-2 mb-2">
            <div class="bg-[#FF4D4F] p-2 rounded-xl">
                <i class="bi bi-ticket-perforated-fill text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Tixflix</h1>
        </div>

        <p class="text-center text-slate-400 text-sm mb-6">Buat Password Baru</p>

        @if(session('error'))
            <div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-5">
                <label class="block text-slate-400 text-sm font-medium mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" 
                           class="w-full pl-11 pr-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" 
                           readonly>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-slate-400 text-sm font-medium mb-2">Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" 
                           class="w-full pl-11 pr-12 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" 
                           placeholder="Password baru" required>
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 hover:text-white transition-colors" onclick="togglePassword('password')">
                        <i class="bi bi-eye" id="passwordEye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-slate-400 text-sm font-medium mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="passwordConfirmation" 
                           class="w-full pl-11 pr-12 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" 
                           placeholder="Konfirmasi password baru" required>
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 hover:text-white transition-colors" onclick="togglePassword('passwordConfirmation')">
                        <i class="bi bi-eye" id="passwordConfirmationEye"></i>
                    </button>
                </div>
            </div>

            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-300 flex justify-center items-center gap-2">
                <i class="bi bi-check-circle"></i> Reset Password
            </button>

        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-medium">
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>
        </div>

    </div>

    <script>
        function togglePassword(fieldId) {
            let field = document.getElementById(fieldId);
            let icon = document.getElementById(fieldId + 'Eye');
            
            if(field.type === "password"){
                field.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>