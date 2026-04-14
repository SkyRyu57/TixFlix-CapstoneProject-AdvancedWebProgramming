<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-secondary text-white">
                    <div class="card-header">Buat Password Baru</div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control bg-dark text-white" value="{{ $email }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Password Baru</label>
                                <input type="password" name="password" class="form-control bg-dark text-white" required>
                            </div>
                            <div class="mb-3">
                                <label>Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control bg-dark text-white" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>