<!DOCTYPE html>
<html lang="en">
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
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tampilkan error validasi dari LoginController -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="email@email.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input 
                        type="password"
                        name="password"
                        id="loginPassword"
                        class="form-control"
                        placeholder="Password kamu"
                        required>
                    <button type="button" class="input-group-text" onclick="toggleLoginPassword()">
                        <i class="bi bi-eye" id="loginEye"></i>
                    </button>
                </div>
            </div>
            <button class="btn btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
        <div class="register-link">
            Belum punya akun?
            <a href="/register">Register</a>
            <br>
            <a href="{{ route('password.request') }}" class="text-indigo-400">Lupa Password Euy</a>
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