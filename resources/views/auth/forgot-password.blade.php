<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Tixflix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
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

        <p class="text-center text-slate-400 text-sm mb-6">Reset Password</p>

        <div id="alertMessage"></div>

        <form id="forgotPasswordForm">
            @csrf

            <div class="mb-6">
                <label class="block text-slate-400 text-sm font-medium mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" 
                           class="w-full pl-11 pr-4 py-3 bg-[#0B0F19] text-white border border-slate-700 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-colors" 
                           placeholder="email@contoh.com" required>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-300 flex justify-center items-center gap-2">
                <i class="bi bi-envelope-paper"></i> Kirim Link Reset Password
            </button>

        </form>

        <div class="mt-6 text-center text-sm text-slate-400">
            <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-medium">
                <i class="bi bi-arrow-left"></i> Kembali ke Login
            </a>
        </div>

    </div>

    <script>
        // GANTI DENGAN KREDENSIAL EMAILJS ANDA
        const PUBLIC_KEY = "cyL07vwYulPBfKkjP";
        const SERVICE_ID = "service_32g1zvn";
        const TEMPLATE_ID = "template_9la4llo";
        
        // Inisialisasi EmailJS (versi 3)
        emailjs.init(PUBLIC_KEY);
        
        console.log("EmailJS initialized");

        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const submitBtn = document.getElementById('submitBtn');
            const alertDiv = document.getElementById('alertMessage');
            
            if (!email) {
                alertDiv.innerHTML = '<div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">Email harus diisi!</div>';
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="loading-spinner mr-2"></div> Mengirim...';
            
            try {
                // 1. Cek email di database
                const checkResponse = await fetch('/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const checkResult = await checkResponse.json();
                
                if (!checkResult.exists) {
                    alertDiv.innerHTML = '<div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">❌ Email tidak ditemukan!</div>';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-envelope-paper"></i> Kirim Link Reset Password';
                    return;
                }
                
                // 2. Buat token reset
                const tokenResponse = await fetch('/create-reset-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const tokenResult = await tokenResponse.json();
                
                if (!tokenResult.success) {
                    alertDiv.innerHTML = '<div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">❌ Gagal membuat token!</div>';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-envelope-paper"></i> Kirim Link Reset Password';
                    return;
                }
                
                // 3. Kirim email via EmailJS - TAMBAHKAN to_email
                const templateParams = {
                    user_name: checkResult.user_name,
                    reset_link: tokenResult.reset_link,
                    year: new Date().getFullYear(),
                    to_email: email,  // <-- INI PENTING!
                    message: 'Klik tombol di bawah untuk mereset password Anda.'
                };
                
                console.log("Sending email to:", email);
                console.log("Template params:", templateParams);
                
                const response = await emailjs.send(
                    SERVICE_ID,
                    TEMPLATE_ID,
                    templateParams
                );
                
                console.log("EmailJS response:", response);
                
                if (response.status === 200) {
                    alertDiv.innerHTML = '<div class="bg-green-500/10 border-l-4 border-green-500 text-green-500 p-4 mb-6 rounded-r-lg text-sm">✅ Link reset password telah dikirim ke ' + email + '! Silakan cek inbox atau folder spam.</div>';
                    document.getElementById('email').value = '';
                } else {
                    alertDiv.innerHTML = '<div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">❌ Gagal mengirim email. Status: ' + response.status + '</div>';
                }
                
            } catch (error) {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="bg-red-500/10 border-l-4 border-red-500 text-red-500 p-4 mb-6 rounded-r-lg text-sm">❌ Terjadi kesalahan: ' + (error.text || error.message) + '</div>';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-envelope-paper"></i> Kirim Link Reset Password';
            }
        });
    </script>
</body>
</html>