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
