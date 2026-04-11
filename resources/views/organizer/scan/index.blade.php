@extends('organizer.layouts.master')

@section('title', 'Scan Tiket')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="glass-card rounded-2xl p-4">
                <h3 class="mb-3 text-center">📷 Scan Tiket Masuk</h3>

                <!-- Kamera -->
                <video id="video" autoplay playsinline style="width: 100%; max-width: 500px; margin: 0 auto; display: block; border-radius: 8px;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <div id="qr-result" class="mt-3 text-center"></div>

                <hr>
                <div class="text-center">
                    <button class="btn btn-secondary btn-sm" id="useUploadBtn">📂 Upload Foto Tiket</button>
                </div>
                <div id="uploadArea" style="display: none;" class="mt-3">
                    <input type="file" id="qr-upload" class="form-control bg-dark text-white" accept="image/*">
                    <div id="upload-result" class="mt-2 text-center"></div>
                </div>
                <form method="POST" action="{{ route('organizer.scan.process') }}" id="manualForm" class="mt-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="ticket_code" class="form-control bg-dark text-white" placeholder="Atau masukkan kode tiket">
                        <button class="btn btn-primary" type="submit">Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        let scanning = false;

        // ========== KAMERA ==========
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(stream) {
                    video.srcObject = stream;
                    video.setAttribute('playsinline', true);
                    video.play();
                    scanning = true;
                    requestAnimationFrame(scan);
                })
                .catch(function(err) {
                    console.error(err);
                    document.getElementById('qr-result').innerHTML = '<div class="alert alert-warning">Kamera tidak tersedia atau akses ditolak. Gunakan upload atau manual.</div>';
                });
        }

        function scan() {
            if (!scanning) return;
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, canvas.width, canvas.height);
                if (code) {
                    scanning = false;
                    submitScan(code.data, 'qr-result');
                    // Optional: stop scanning after success
                    // setTimeout(() => { scanning = true; requestAnimationFrame(scan); }, 3000);
                }
            }
            requestAnimationFrame(scan);
        }

        startCamera();

        // ========== UPLOAD ==========
        const useUploadBtn = document.getElementById('useUploadBtn');
        const uploadArea = document.getElementById('uploadArea');
        const uploadInput = document.getElementById('qr-upload');
        const uploadResult = document.getElementById('upload-result');

        useUploadBtn.addEventListener('click', function() {
            uploadArea.style.display = 'block';
            useUploadBtn.disabled = true;
        });

        uploadInput.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];
            uploadResult.innerHTML = 'Memproses...';
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, canvas.width, canvas.height);
                    if (code) {
                        submitScan(code.data, 'upload-result');
                        uploadResult.innerHTML = '';
                    } else {
                        uploadResult.innerHTML = '<div class="alert alert-danger">Gagal membaca QR dari gambar.</div>';
                        setTimeout(() => uploadResult.innerHTML = '', 3000);
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        // ========== MANUAL ==========
        const manualForm = document.getElementById('manualForm');
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const ticketCode = manualForm.querySelector('input[name="ticket_code"]').value;
            if (!ticketCode.trim()) return;
            submitScan(ticketCode, 'qr-result');
            manualForm.reset();
        });

        // ========== FUNGSI SUBMIT ==========
        function submitScan(ticketCode, resultElementId) {
            fetch('{{ route("organizer.scan.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ticket_code: ticketCode })
            })
            .then(res => res.json())
            .then(data => {
                const resultDiv = document.getElementById(resultElementId);
                const alertClass = data.success ? 'alert-success' : 'alert-danger';
                resultDiv.innerHTML = `<div class="alert ${alertClass}">${data.message}</div>`;
                setTimeout(() => resultDiv.innerHTML = '', 3000);
            })
            .catch(err => {
                console.error(err);
                document.getElementById(resultElementId).innerHTML = '<div class="alert alert-danger">Error jaringan</div>';
            });
        }
    });
</script>
@endpush