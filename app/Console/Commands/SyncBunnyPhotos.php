<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class SyncBunnyPhotos extends Command
{
    protected $signature = 'pbvsi:sync-photos {--limit=0 : Limit number of directories processed}';
    protected $description = 'Sync legacy photos from BunnyCDN (Kejurnas, Livoli, PBVSI) to database';

    public function handle(): int
    {
        $accessKey = config('services.bunnycdn.access_key', '6f8b3df9-5063-4a9c-bdb7dec01911-1a05-418e');
        $storageZone = config('services.bunnycdn.storage_zone', 'pbvsi');

        $this->info("Fetching directories from BunnyCDN...");

        $sources = [
            'kejurnas' => 'kejurnas/uploads/images/profil/',
            'livoli'   => 'livoli/uploads/images/profil/',
            'pbvsi'    => 'pbvsi/uploads/images/profil/',
        ];

        $totalSynced = 0;

        foreach ($sources as $sourceKey => $baseDir) {
            $this->info("Fetching listing for {$baseDir}...");
            $res = Http::withHeaders([
                'AccessKey' => $accessKey,
                'Accept' => 'application/json',
            ])->get("https://storage.bunnycdn.com/{$storageZone}/" . ltrim($baseDir, '/'));

            if (!$res->successful()) {
                $this->error("Failed to list {$baseDir}: " . $res->status());
                continue;
            }

            $dirs = array_filter($res->json(), fn($item) => $item['IsDirectory'] && is_numeric($item['ObjectName']));
            $dirs = array_values($dirs);

            // Filter out user IDs that already have foto_profil_path in member_pemain
            $existingUids = DB::table('member_pemain')
                ->whereNotNull('foto_profil_path')
                ->pluck('user_id')
                ->filter()
                ->flip()
                ->toArray();
            
            $dirs = array_filter($dirs, fn($d) => !isset($existingUids[(int)$d['ObjectName']]));
            $dirs = array_values($dirs);

            $totalDirs = count($dirs);
            $this->info("Found {$totalDirs} pending user directories in {$sourceKey}.");

            $limit = (int)$this->option('limit');
            if ($limit > 0 && $limit < $totalDirs) {
                $dirs = array_slice($dirs, 0, $limit);
                $totalDirs = $limit;
            }

            $chunkSize = 30;
            $chunks = array_chunk($dirs, $chunkSize);

            $bar = $this->output->createProgressBar($totalDirs);
            $bar->start();

            foreach ($chunks as $chunk) {
                $responses = Http::pool(fn (Pool $pool) => 
                    array_map(fn ($d) => 
                        $pool->withHeaders(['AccessKey' => $accessKey, 'Accept' => 'application/json'])
                             ->get("https://storage.bunnycdn.com/{$storageZone}/{$baseDir}{$d['ObjectName']}/"),
                        $chunk
                    )
                );

                foreach ($responses as $i => $r) {
                    if ($r instanceof \Illuminate\Http\Client\Response && $r->successful()) {
                        $files = $r->json();
                        $uid = (int)$chunk[$i]['ObjectName'];
                        
                        // Pick non-thumb file first
                        $origFiles = array_values(array_filter($files, fn($f) => !str_starts_with($f['ObjectName'], 'thumb_') && preg_match('/\.(jpg|jpeg|png|webp)$/i', $f['ObjectName'])));
                        
                        if (!empty($origFiles)) {
                            $filename = $origFiles[0]['ObjectName'];
                            $fullRelativePath = "{$baseDir}{$uid}/{$filename}";

                            // Update member_pemain
                            DB::table('member_pemain')
                                ->where('user_id', $uid)
                                ->whereNull('foto_profil_path')
                                ->update(['foto_profil_path' => $fullRelativePath]);

                            // Update core_users
                            DB::table('core_users')
                                ->where('id', $uid)
                                ->whereNull('avatar_path')
                                ->update(['avatar_path' => $fullRelativePath]);

                            $totalSynced++;
                        }
                    }
                    $bar->advance();
                }
            }

            $bar->finish();
            $this->newLine();
        }

        // Also sync any from dev_dashpbvsi.tb_userfile if still null
        $this->info("Checking legacy dev_dashpbvsi.tb_userfile...");
        try {
            $legacyFiles = DB::select("SELECT userid, file_foto FROM dev_dashpbvsi.tb_userfile WHERE file_foto IS NOT NULL AND file_foto != ''");
            $legacyCount = 0;
            foreach ($legacyFiles as $lf) {
                $updated = DB::table('member_pemain')
                    ->where('user_id', $lf->userid)
                    ->whereNull('foto_profil_path')
                    ->update(['foto_profil_path' => $lf->file_foto]);
                if ($updated) {
                    $legacyCount++;
                }
            }
            $this->info("Synced {$legacyCount} records from tb_userfile to member_pemain.");
        } catch (\Throwable $e) {
            $this->warn("Note on tb_userfile sync: " . $e->getMessage());
        }

        $this->info("Photo sync completed! Total synced: {$totalSynced}");
        return Command::SUCCESS;
    }
}
