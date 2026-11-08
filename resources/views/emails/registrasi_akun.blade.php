<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] ?? 'Registrasi Akun - Platform PBVSI' }}</title>
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
            max-width: 600px;
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
            margin-bottom: 12px;
        }
        .credentials-card {
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 18px 20px;
            margin: 22px 0;
        }
        .credential-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13.5px;
        }
        .credential-row:last-child {
            border-bottom: none;
        }
        .cred-label {
            width: 38%;
            color: #64748b;
            font-weight: 600;
        }
        .cred-value {
            width: 62%;
            color: #0f172a;
        }
        .cred-highlight {
            color: #dc2626 !important;
            font-weight: 800 !important;
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 14px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0;
        }
        .btn-activate {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 34px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .cs-box {
            background: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 16px 18px;
            border-radius: 6px;
            margin: 26px 0 10px 0;
            font-size: 13px;
            color: #166534;
            line-height: 1.6;
        }
        .cs-box a {
            color: #15803d;
            font-weight: 700;
            text-decoration: none;
        }
        .cs-box a:hover {
            text-decoration: underline;
        }
        .fallback-link {
            word-break: break-all;
            font-size: 12px;
            color: #64748b;
            background: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-top: 8px;
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
            <p>Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia</p>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="greeting">Yth. {{ $data['full_name'] }},</div>
            <p style="margin: 0 0 16px 0; font-size: 14.5px;">
                Selamat! Akun Anda untuk <strong>{{ $data['role_title'] ?? 'Registrasi PBVSI' }}</strong> telah berhasil didaftarkan di sistem <strong>Platform PBVSI</strong>. Berikut adalah rincian data pendaftaran akun Anda:
            </p>

            <!-- DETAIL KREDENSIAL AKUN -->
            <div class="credentials-card">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600; width: 38%;">Nama Lengkap</td>
                        <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $data['full_name'] }}</td>
                    </tr>
                    @if(!empty($data['phone_number']) && $data['phone_number'] !== '-')
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Nomor HP / WhatsApp</td>
                        <td style="padding: 8px 0; color: #0f172a;">{{ $data['phone_number'] }}</td>
                    </tr>
                    @endif
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Alamat Email</td>
                        <td style="padding: 8px 0; color: #0f172a;">{{ $data['email'] }}</td>
                    </tr>
                    @if(!empty($data['national_id']) && $data['national_id'] !== '-')
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600;">NIK Kependudukan</td>
                        <td style="padding: 8px 0; color: #0f172a; font-family: monospace;">{{ $data['national_id'] }}</td>
                    </tr>
                    @endif
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Username Akun</td>
                        <td style="padding: 8px 0;" class="cred-highlight">{{ $data['username'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #64748b; font-weight: 600;">Kata Sandi (Password)</td>
                        <td style="padding: 8px 0;" class="cred-highlight">{{ $data['password'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- AKTIVASI AKUN -->
            <p style="font-size: 14px; margin: 20px 0 10px 0;">
                Silakan aktivasi akun Anda melalui tautan di bawah ini untuk mengonfirmasi email dan mulai menggunakan <strong>Platform PBVSI</strong>:
            </p>

            <div class="btn-wrapper">
                <a href="{{ $data['activation_url'] }}" class="btn-activate" target="_blank" rel="noopener noreferrer">
                    Aktivasi & Verifikasi Akun Saya
                </a>
            </div>

            <p style="font-size: 12.5px; color: #64748b; margin: 16px 0 4px 0;">
                Atau salin tautan aktivasi berikut ke browser web Anda:
            </p>
            <div class="fallback-link">
                <a href="{{ $data['activation_url'] }}" style="color: #2563eb; text-decoration: none;">{{ $data['activation_url'] }}</a>
            </div>

            <!-- CUSTOMER SUPPORT BOX -->
            <div class="cs-box">
                💬 <strong>Layanan Customer Support PBVSI:</strong><br>
                Jika Anda memiliki kendala atau pertanyaan seputar registrasi akun, silakan hubungi tim kami melalui:
                <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                    <li>WhatsApp: <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $data['cs_whatsapp']) }}" target="_blank"><strong>{{ $data['cs_whatsapp'] }}</strong></a></li>
                    <li>Email: <a href="mailto:{{ $data['cs_email'] }}"><strong>{{ $data['cs_email'] }}</strong></a> atau balas email ini langsung.</li>
                </ul>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;">
                &copy; {{ date('Y') }} Pengurus Pusat Persatuan Bola Voli Seluruh Indonesia (PP PBVSI).
            </p>
            <p style="margin: 0;">
                Email verifikasi pendaftaran akun resmi sistem administrasi bola voli nasional.
            </p>
        </div>
    </div>
</body>
</html>
