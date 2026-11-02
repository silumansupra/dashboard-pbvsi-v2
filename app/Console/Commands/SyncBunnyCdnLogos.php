<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncBunnyCdnLogos extends Command
{
    protected $signature = 'pbvsi:sync-bunny-logos {--cache : Use cached scan if available}';
    protected $description = 'Scan existing team logos from BunnyCDN Storage and sync to trn_peserta_tim and org_klub';

    protected string $accessKey;
    protected string $cacheFile;

    public function __construct()
    {
        parent::__construct();
        $this->accessKey = config('services.bunnycdn.access_key', '6f8b3df9-5063-4a9c-bdb7dec01911-1a05-418e');
        $this->cacheFile = storage_path('app/bunny_logos_cache.json');
    }

    public function handle()
    {
        $this->info("=========================================================");
        $this->info("   BUNNYCDN LOGO DISCOVERY & SYNC TO DB_PBVSI_V2");
        $this->info("=========================================================");

        $data = null;
        if ($this->option('cache') && file_exists($this->cacheFile)) {
            $this->info("Loading from local cache: {$this->cacheFile}");
            $data = json_decode(file_get_contents($this->cacheFile), true);
        }

        if (!$data) {
            $data = $this->scanFromBunny();
            @file_put_contents($this->cacheFile, json_encode($data, JSON_PRETTY_PRINT));
            $this->info("Saved scan results to cache: {$this->cacheFile}");
        }

        $this->syncToDatabase($data);

        return 0;
    }

    protected function scanFromBunny(): array
    {
        $this->info("Connecting to BunnyCDN Storage API...");

        // 1. Scan Kejurnas
        $this->line("Fetching folder list: kejurnas/uploads/images/logo/ ...");
        $kejurnasDirs = $this->getBunnyList('kejurnas/uploads/images/logo/');
        $this->info("Found " . count($kejurnasDirs) . " directories in Kejurnas.");

        $kejurnasLogos = [];
        $bar = $this->output->createProgressBar(count($kejurnasDirs));
        $bar->start();
        foreach ($kejurnasDirs as $dir) {
            $bar->advance();
            if (!($dir['IsDirectory'] ?? false)) continue;
            $oldId = $dir['ObjectName'];
            $files = $this->getBunnyList("kejurnas/uploads/images/logo/{$oldId}/");
            $mainImg = $this->pickBestImage($files);
            if ($mainImg) {
                $kejurnasLogos[$oldId] = "kejurnas/uploads/images/logo/{$oldId}/{$mainImg}";
            }
        }
        $bar->finish();
        $this->newLine();
        $this->info("Valid logo files discovered in Kejurnas: " . count($kejurnasLogos));

        // 2. Scan Livoli
        $this->line("Fetching folder list: livoli/uploads/images/logo/ ...");
        $livoliDirs = $this->getBunnyList('livoli/uploads/images/logo/');
        $this->info("Found " . count($livoliDirs) . " directories in Livoli.");

        $livoliLogos = [];
        $bar = $this->output->createProgressBar(count($livoliDirs));
        $bar->start();
        foreach ($livoliDirs as $dir) {
            $bar->advance();
            if (!($dir['IsDirectory'] ?? false)) continue;
            $oldId = $dir['ObjectName'];
            $files = $this->getBunnyList("livoli/uploads/images/logo/{$oldId}/");
            $mainImg = $this->pickBestImage($files);
            if ($mainImg) {
                $livoliLogos[$oldId] = "livoli/uploads/images/logo/{$oldId}/{$mainImg}";
            }
        }
        $bar->finish();
        $this->newLine();
        $this->info("Valid logo files discovered in Livoli: " . count($livoliLogos));

        return [
            'kejurnas' => $kejurnasLogos,
            'livoli' => $livoliLogos,
        ];
    }

    protected function pickBestImage(array $files): ?string
    {
        // Sort files to pick the latest / non-thumb first
        $candidates = [];
        foreach ($files as $f) {
            if ($f['IsDirectory'] ?? false) continue;
            $name = $f['ObjectName'];
            if (!preg_match('/\.(jpg|jpeg|png|webp)$/i', $name)) continue;

            $isThumb = str_starts_with($name, 'thumb_');
            $candidates[] = [
                'name' => $name,
                'is_thumb' => $isThumb,
                'size' => $f['Length'] ?? 0,
            ];
        }

        if (empty($candidates)) return null;

        // Prefer non-thumb with larger size
        usort($candidates, function($a, $b) {
            if ($a['is_thumb'] !== $b['is_thumb']) {
                return $a['is_thumb'] ? 1 : -1;
            }
            return $b['size'] <=> $a['size'];
        });

        return $candidates[0]['name'];
    }

    protected function syncToDatabase(array $data)
    {
        $this->info("\n--- SYNCING DISCOVERED LOGOS TO DATABASE ---");

        $kejurnasLogos = $data['kejurnas'] ?? [];
        $livoliLogos = $data['livoli'] ?? [];

        // Combine maps
        $allLogos = $livoliLogos + $kejurnasLogos; // livoli takes precedence, or kejurnas

        // Get trn_peserta_tim migration mappings: source_id -> target_id
        $pesertaMap = DB::table('sys_migration_maps')
            ->where('target_table', 'trn_peserta_tim')
            ->pluck('target_id', 'source_id')
            ->toArray();

        $updatedPeserta = 0;
        $updatedKlub = 0;
        $klubLogoMap = [];

        foreach ($allLogos as $oldId => $logoPath) {
            if (isset($pesertaMap[$oldId])) {
                $pesertaId = $pesertaMap[$oldId];

                // Update trn_peserta_tim
                DB::table('trn_peserta_tim')
                    ->where('id', $pesertaId)
                    ->update(['logo_tim_path' => $logoPath]);

                $updatedPeserta++;

                // Get participant team to identify its master club
                $peserta = DB::table('trn_peserta_tim')->where('id', $pesertaId)->first();
                if ($peserta && $peserta->klub_id) {
                    $klubLogoMap[$peserta->klub_id] = $logoPath;
                }
            }
        }

        // Update org_klub
        foreach ($klubLogoMap as $klubId => $logoPath) {
            DB::table('org_klub')
                ->where('id', $klubId)
                ->update(['logo_path' => $logoPath]);
            $updatedKlub++;
        }

        $this->info("✓ Successfully updated {$updatedPeserta} tournament teams in trn_peserta_tim with BunnyCDN logos.");
        $this->info("✓ Successfully updated {$updatedKlub} master clubs in org_klub with official BunnyCDN logos.");
    }

    protected function getBunnyList(string $path): array
    {
        $url = "https://storage.bunnycdn.com/pbvsi/" . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["AccessKey: {$this->accessKey}"]);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true) ?: [];
    }
}
