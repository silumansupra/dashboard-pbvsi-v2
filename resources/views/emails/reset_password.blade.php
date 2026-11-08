<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Platform PBVSI</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 30px 15px;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 32px 25px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 12px 0 0 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: #cbd5e1;
        }
        .content {
            padding: 32px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-reset {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            border-radius: 6px;
            margin: 24px 0;
            font-size: 13.5px;
            color: #1e40af;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 20px 0;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .meta-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-table td:first-child {
            font-weight: 600;
            color: #64748b;
            width: 35%;
        }
        .meta-table tr:last-child td {
            border-bottom: none;
        }
        .fallback-link {
            word-break: break-all;
            font-size: 12px;
            color: #64748b;
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            background: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- HEADER -->
        <div class="header">
            <div style="font-size: 28px; line-height: 1;">🏐</div>
            <h1>PLATFORM PBVSI</h1>
            <p>Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia</p>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="greeting">Halo, {{ $user->name }}</div>
            <p style="margin: 0 0 16px 0; font-size: 14.5px;">
                Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda di <strong>Platform PBVSI</strong>. Silakan klik tombol di bawah ini untuk melanjutkan pembuatan kata sandi baru:
            </p>

            <div class="btn-wrapper">
                <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">
                    Atur Ulang Kata Sandi
                </a>
            </div>

            <div class="info-box">
                ⏱️ <strong>Batas Waktu:</strong> Tautan ini hanya berlaku selama <strong>{{ $expiresInMinutes }} menit</strong> sejak email ini dikirimkan demi keamanan akun Anda.
            </div>

            <!-- DETAIL PERMINTAAN -->
            <table class="meta-table">
                <tr>
                    <td>Akun / Username</td>
                    <td><strong>{{ $user->username }}</strong></td>
                </tr>
                <tr>
                    <td>Alamat Email</td>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <td>Waktu Permintaan</td>
                    <td>{{ now()->translatedFormat('d F Y, H:i:s') }} WIB</td>
                </tr>
                <tr>
                    <td>Alamat IP Peminta</td>
                    <td><code>{{ $ipAddress }}</code></td>
                </tr>
            </table>

            <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
                Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser web Anda:
            </p>
            <div class="fallback-link">
                <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
            </div>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">

            <p style="font-size: 12.5px; color: #94a3b8; margin: 0;">
                ⚠️ <strong>Bukan Anda yang meminta?</strong> Jika Anda tidak melakukan permintaan ini, Anda dapat mengabaikan email ini dengan aman. Kata sandi akun Anda tidak akan berubah dan tidak ada tindakan lebih lanjut yang diperlukan.
            </p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;">
                &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bolavoli Seluruh Indonesia (PP PBVSI).
            </p>
            <p style="margin: 0;">
                Email ini dibuat secara otomatis oleh sistem keamanan Platform PBVSI. Jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
