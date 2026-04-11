<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - Tixflix</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: linear-gradient(145deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            padding: 40px;
            color: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ff2d55;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #ff2d55;
            margin: 0;
            font-size: 28px;
        }
        .content {
            text-align: center;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #ff2d55 0%, #ff5e3a 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            margin: 20px 0;
            font-weight: bold;
            transition: all 0.3s;
        }
        .button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(255,45,85,0.4);
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        .note {
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Tixflix</h1>
                <p>Reset Password</p>
            </div>
            <div class="content">
                <h2>Halo, {{ $userName }}!</h2>
                <p>Kami menerima permintaan untuk mereset password akun Tixflix Anda.</p>
                <p>Klik tombol di bawah untuk membuat password baru:</p>
                <a href="{{ $resetLink }}" class="button">Reset Password</a>
                <p class="note">Link ini akan kadaluarsa dalam 60 menit.</p>
                <p class="note">Jika Anda tidak meminta reset password, abaikan email ini.</p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Tixflix. All rights reserved.</p>
                <p>Platform Tiket Event Terpercaya</p>
            </div>
        </div>
    </div>
</body>
</html>