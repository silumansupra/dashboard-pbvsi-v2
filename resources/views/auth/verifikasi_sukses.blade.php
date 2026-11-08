<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Verifikasi Akun Berhasil - Platform PBVSI">
    <title>Aktivasi Akun Berhasil &bull; Platform PBVSI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 10%, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem 1rem;
            color: #334155;
        }
        .card-wrapper {
            width: 100%;
            max-width: 480px;
            margin: auto;
        }
        .success-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08);
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }
        .btn-login:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.45);
        }
    </style>
</head>
<body>
    <div class="card-wrapper">
        <div class="success-card">
            <div class="icon-circle">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <h2 class="fw-bold text-dark mb-2">Akun Berhasil Diaktivasi!</h2>
            <p class="text-muted small mb-4">
                Selamat, alamat email Anda telah berhasil diverifikasi dan akun Anda di <strong>Platform PBVSI</strong> kini telah aktif sepenuhnya.
            </p>

            <div class="p-3 bg-light rounded-3 border text-start mb-4">
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="small text-muted">Nama Lengkap:</span>
                    <span class="small fw-bold text-dark">{{ $user->name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="small text-muted">Username:</span>
                    <span class="small font-monospace fw-bold text-primary">{{ $user->username }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="small text-muted">Status Akun:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">AKTIF & TERVERIFIKASI</span>
                </div>
            </div>

            <a href="{{ route('login') }}" class="btn-login mb-3">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk ke Platform PBVSI</span>
            </a>

            <div class="small text-muted mt-3">
                Butuh bantuan? WhatsApp: <a href="https://wa.me/628170283833" class="text-decoration-none text-muted fw-semibold" target="_blank">+62 817-0283-833</a>
            </div>
        </div>

        <div class="text-center mt-3">
            <p class="small text-white-50 mb-0">
                &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia
            </p>
        </div>
    </div>
</body>
</html>
