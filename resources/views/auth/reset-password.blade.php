<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Tixflix</title>
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
        .form-control{
            background:#0f172a;
            border:1px solid #334155;
            color:#e2e8f0;
            padding:12px;
        }
        .form-control:focus{
            background:#ffffff;
            color:#0f172a;
            border-color:#6366f1;
            box-shadow:0 0 0 2px rgba(99,102,241,0.2);
        }
        .btn-primary{
            margin-top:15px;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            border:none;
            padding:13px;
            font-weight:600;
            border-radius:8px;
            width:100%;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <i class="bi bi-ticket-perforated-fill"></i> Tixflix
        </div>
        <div class="subtitle">
            Buat password baru
        </div>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $email }}" readonly>
            </div>
            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-indigo-400">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>