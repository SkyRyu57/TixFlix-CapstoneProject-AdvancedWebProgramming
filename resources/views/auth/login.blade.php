<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | CinePass</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:linear-gradient(135deg,#020617,#020617,#0f172a);
height:100vh;
display:flex;
align-items:center;
justify-content:center;
font-family:'Inter',sans-serif;
color:#e2e8f0;
}

.card{
width:420px;
background:#1e293b;
border:none;
border-radius:14px;
padding:40px;
box-shadow:0 25px 50px rgba(0,0,0,0.6);
}

.logo{
font-size:30px;
font-weight:700;
text-align:center;
margin-bottom:5px;
color:#818cf8;
}

.subtitle{
text-align:center;
color:#94a3b8;
margin-bottom:30px;
}

label{
font-size:14px;
color:#cbd5f5;
margin-bottom:6px;
}

.input-group-text{
background:#020617;
border:1px solid #334155;
color:#818cf8;
}

.form-control{
background:#0f172a;
border:1px solid #334155;
color:#e2e8f0;
padding:12px;
}

.form-control::placeholder{
color:#94a3b8;
}

.form-control:focus{
background:#ffffff;
color:#0f172a;
border-color:#6366f1;
box-shadow:0 0 0 2px rgba(99,102,241,0.2);
}

.btn-login{
margin-top:15px;
background:linear-gradient(135deg,#6366f1,#8b5cf6);
border:none;
padding:13px;
font-weight:600;
border-radius:8px;
width:100%;
}

.register-link{
text-align:center;
margin-top:15px;
color:#94a3b8;
}

.register-link a{
color:#818cf8;
text-decoration:none;
font-weight:600;
}

</style>

</head>

<body>

<div class="card">

<div class="logo">
<i class="bi bi-ticket-perforated-fill"></i> CinePass
</div>

<div class="subtitle">
Login untuk membeli atau mengelola event
</div>

        @if(session('success'))
            <div class="bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 mb-6 rounded-r-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block text-slate-400 text-sm font-medium mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="w-full pl-11 pr-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="email@email.com" required>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-slate-400 text-sm font-medium mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="loginPassword" class="w-full pl-11 pr-12 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" placeholder="Password kamu" required>
                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 hover:text-white transition-colors" onclick="toggleLoginPassword()">
                        <i class="bi bi-eye" id="loginEye"></i>
                    </button>
                </div>
            </div>

            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-300 flex justify-center items-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
            </button>

        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            Belum punya akun? <a href="/register" class="text-indigo-400 hover:text-indigo-300 font-medium">Register di sini</a>
        </div>

    </div>

    <script>
        function toggleLoginPassword(){
            let field=document.getElementById("loginPassword");
            let icon=document.getElementById("loginEye");
            
            if(field.type==="password"){
                field.type="text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }else{
                field.type="password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>