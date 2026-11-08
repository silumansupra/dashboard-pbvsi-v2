<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BunnyCDNService
{
    protected string $storageZone;
    protected string $accessKey;
    protected string $hostname;
    protected string $baseUrl;

    public function __construct()
    {
        $this->storageZone = config('services.bunnycdn.storage_zone', 'pbvsi');
        $this->accessKey   = config('services.bunnycdn.access_key', '6f8b3df9-5063-4a9c-bdb7dec01911-1a05-418e');
        $this->hostname    = config('services.bunnycdn.hostname', 'storage.bunnycdn.com');
        $this->baseUrl     = config('services.bunnycdn.base_url', 'https://cdn.pbvsi.or.id/');
    }

    /**
     * Upload an UploadedFile or local file to BunnyCDN Storage.
     *
     * @param UploadedFile|string $file
     * @param string $remoteDir e.g. 'uploads/klub'
     * @param string|null $customFilename
     * @return string|null Relative path in BunnyCDN (e.g. 'uploads/klub/logo-123.png') or null on failure
     */
    public function upload($file, string $remoteDir = 'uploads/klub', ?string $customFilename = null): ?string
    {
        try {
            $remoteDir = trim($remoteDir, '/');

            if ($file instanceof UploadedFile) {
                $ext = $file->getClientOriginalExtension() ?: 'png';
                $filename = $customFilename ?: (Str::random(24) . '.' . $ext);
                $fileContent = file_get_contents($file->getRealPath());
                $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            } elseif (is_string($file) && file_exists($file)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION) ?: 'png';
                $filename = $customFilename ?: (Str::random(24) . '.' . $ext);
                $fileContent = file_get_contents($file);
                $mimeType = mime_content_type($file) ?: 'application/octet-stream';
            } else {
                Log::error("BunnyCDN upload failed: file invalid or not found.", ['file' => $file]);
                return null;
            }

            $remotePath = "{$remoteDir}/{$filename}";
            $uploadUrl = "https://{$this->hostname}/{$this->storageZone}/{$remotePath}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "AccessKey: {$this->accessKey}",
                "Content-Type: {$mimeType}",
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                return $remotePath;
            }

            Log::error("BunnyCDN upload HTTP error", [
                'code' => $httpCode,
                'error' => $curlError,
                'response' => $response,
                'url' => $uploadUrl
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error("BunnyCDN upload exception: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return null;
        }
    }

    /**
     * Upload dan kompres gambar secara optimal (Resize proporsional + WebP compression) ke BunnyCDN.
     * Mengurangi ukuran file dari 5-10MB menjadi ~150-300KB dengan kualitas visual tetap tajam.
     *
     * @param UploadedFile|string $file
     * @param string $remoteDir e.g. 'uploads/kyc' atau 'uploads/avatars'
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality (1-100)
     * @return string|null Path relatif di BunnyCDN atau null jika gagal
     */
    public function uploadOptimizedImage(
        $file,
        string $remoteDir = 'uploads/kyc',
        int $maxWidth = 1600,
        int $maxHeight = 1600,
        int $quality = 82
    ): ?string {
        $realPath = null;
        if ($file instanceof UploadedFile) {
            $realPath = $file->getRealPath();
        } elseif (is_string($file) && file_exists($file)) {
            $realPath = $file;
        }

        if (!$realPath || !file_exists($realPath)) {
            Log::error("BunnyCDN uploadOptimizedImage failed: invalid source file.");
            return null;
        }

        // Jika ekstensi GD tidak aktif, fallback langsung ke upload biasa
        if (!extension_loaded('gd')) {
            return $this->upload($file, $remoteDir);
        }

        $imageInfo = @getimagesize($realPath);
        if (!$imageInfo) {
            return $this->upload($file, $remoteDir);
        }

        [$origW, $origH, $imageType] = $imageInfo;

        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG  => @imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : null,
            default => null,
        };

        if (!$sourceImage) {
            return $this->upload($file, $remoteDir);
        }

        // Perbaiki orientasi EXIF untuk foto kamera smartphone
        if ($imageType === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($realPath);
                if (!empty($exif['Orientation'])) {
                    $sourceImage = match ((int) $exif['Orientation']) {
                        3 => imagerotate($sourceImage, 180, 0),
                        6 => imagerotate($sourceImage, -90, 0),
                        8 => imagerotate($sourceImage, 90, 0),
                        default => $sourceImage,
                    };
                    $origW = imagesx($sourceImage);
                    $origH = imagesy($sourceImage);
                }
            } catch (\Throwable $e) {
                // Abaikan kesalahan pembacaan EXIF jika metadata rusak
            }
        }

        // Hitung skala proporsional gambar
        $targetW = $origW;
        $targetH = $origH;
        if ($origW > $maxWidth || $origH > $maxHeight) {
            $scale = min($maxWidth / $origW, $maxHeight / $origH);
            $targetW = (int) max(1, round($origW * $scale));
            $targetH = (int) max(1, round($origH * $scale));
        }

        $destImage = imagecreatetruecolor($targetW, $targetH);

        // Pertahankan transparansi alpha channel
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
        imagefilledrectangle($destImage, 0, 0, $targetW, $targetH, $transparent);

        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        // Simpan file terkompresi sementara di direktori temp lokal
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $randomHash = Str::random(32);
        $tempFile = $tempDir . '/' . $randomHash . '.webp';
        $customFilename = $randomHash . '.webp';

        $saved = false;
        if (function_exists('imagewebp')) {
            $saved = @imagewebp($destImage, $tempFile, $quality);
        }

        if (!$saved) {
            // Fallback ke JPEG jika format WebP bermasalah
            $tempFile = $tempDir . '/' . $randomHash . '.jpg';
            $customFilename = $randomHash . '.jpg';
            $saved = @imagejpeg($destImage, $tempFile, $quality);
        }

        imagedestroy($sourceImage);
        imagedestroy($destImage);

        if (!$saved || !file_exists($tempFile)) {
            return $this->upload($file, $remoteDir);
        }

        // Upload file hasil optimasi ke BunnyCDN
        $remotePath = $this->upload($tempFile, $remoteDir, $customFilename);

        // Hapus file sementara
        @unlink($tempFile);

        return $remotePath;
    }

    /**
     * Delete a file from BunnyCDN Storage.
     *
     * @param string $remotePath
     * @return bool
     */
    public function delete(string $remotePath): bool
    {
        try {
            $remotePath = ltrim($remotePath, '/');
            $deleteUrl = "https://{$this->hostname}/{$this->storageZone}/{$remotePath}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $deleteUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "AccessKey: {$this->accessKey}",
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($httpCode === 200 || $httpCode === 204);
        } catch (\Throwable $e) {
            Log::error("BunnyCDN delete exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert stored logo / photo path to a full BunnyCDN URL or fallback to default image.
     *
     * @param string|null $path
     * @param int|null $userId
     * @param string|null $fallback
     * @return string
     */
    public function getUrl(?string $path, ?int $userId = null, ?string $fallback = null): string
    {
        $defaultFallback = $fallback ?: asset('images/no-user.svg');

        if (empty($path)) {
            return $defaultFallback;
        }

        // Already full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $base = rtrim($this->baseUrl, '/');
        $cleanPath = ltrim($path, '/');

        // If path is just a filename (e.g. hash.jpg/png) without directory and userId is provided
        if (!str_contains($cleanPath, '/') && $userId) {
            return "{$base}/pbvsi/uploads/images/profil/{$userId}/{$cleanPath}";
        }

        return "{$base}/{$cleanPath}";
    }
}
