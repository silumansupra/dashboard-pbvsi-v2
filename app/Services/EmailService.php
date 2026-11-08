<?php

namespace App\Services;

use App\Mail\UserRegistrationVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailService
{
    /**
     * Kirim email verifikasi akun baru (kompatibel penuh dengan format CI3 & Laravel v2)
     *
     * @param array $data Data pendaftaran akun: full_name, username, password, email, phone_number, national_id, userid, reg_token
     * @return bool
     */
    public static function kirimEmailVerifikasi(array $data): bool
    {
        try {
            $email = $data['email'] ?? null;
            if (!$email) {
                Log::warning('kirimEmailVerifikasi: Alamat email tidak ditemukan dalam data.');
                return false;
            }

            $userId = $data['userid'] ?? ($data['user_id'] ?? ($data['id'] ?? null));
            $token = $data['reg_token'] ?? ($data['token'] ?? Str::random(32));

            // Jika userid adalah ID numerik, amankan dengan encrypt_id
            $sessionParam = $userId;
            if (is_numeric($userId)) {
                $sessionParam = encrypt_id((int)$userId, 'user');
            }

            // Bangun URL aktivasi verifikasi akun
            $urlVerifikasi = route('verifikasi.akun', [
                'data_id' => base64_encode((string)time()),
                'session' => $sessionParam,
                'token' => $token,
            ]);

            $payload = [
                'full_name' => $data['full_name'] ?? ($data['nama_user'] ?? ($data['name'] ?? 'Anggota PBVSI')),
                'username' => $data['username'] ?? '-',
                'password' => $data['password'] ?? '-',
                'email' => $email,
                'phone_number' => $data['phone_number'] ?? ($data['phone'] ?? '-'),
                'national_id' => $data['national_id'] ?? ($data['nik'] ?? '-'),
                'role_title' => $data['title'] ?? ($data['role_title'] ?? 'Registrasi Wasit - PBVSI'),
                'activation_url' => $urlVerifikasi,
                'subject' => $data['subject'] ?? 'Registrasi Wasit - PBVSI',
                'cs_whatsapp' => '+62 817-0283-833',
                'cs_email' => 'support@pbvsi.or.id',
            ];

            // Kirim email via Mailer SMTP
            Mail::to($email)->send(new UserRegistrationVerificationMail($payload));

            // Catat ke Audit Log
            AuditLogService::log(
                'auth.verification_email_sent',
                "Email verifikasi dan aktivasi akun dikirim ke {$email} untuk {$payload['full_name']} ({$payload['username']}).",
                [
                    'metadata' => [
                        'email' => $email,
                        'username' => $payload['username'],
                        'activation_url' => $urlVerifikasi,
                    ]
                ]
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email verifikasi akun: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            AuditLogService::log(
                'auth.verification_email_error',
                "Gagal mengirim email verifikasi ke " . ($data['email'] ?? 'unknown') . ": " . $e->getMessage(),
                ['metadata' => ['data' => $data]]
            );

            return false;
        }
    }

    /**
     * Helper langsung dari Model User
     */
    public static function kirimEmailVerifikasiUser(User $user, ?string $rawPassword = null, string $roleTitle = 'Registrasi Wasit - PBVSI'): bool
    {
        $regToken = Str::random(32);

        // Simpan token sementara di remember_token jika kosong
        if (empty($user->remember_token)) {
            $user->remember_token = $regToken;
            $user->save();
        } else {
            $regToken = $user->remember_token;
        }

        return self::kirimEmailVerifikasi([
            'userid' => $user->id,
            'full_name' => $user->name,
            'username' => $user->username,
            'password' => $rawPassword ?? ($user->raw_password ?? '(Sesuai pendaftaran Anda)'),
            'email' => $user->email,
            'phone_number' => $user->phone ?? '-',
            'national_id' => $user->profile?->nik ?? '-',
            'reg_token' => $regToken,
            'subject' => $roleTitle,
            'title' => $roleTitle,
        ]);
    }
}
