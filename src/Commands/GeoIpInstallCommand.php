<?php

namespace ArielMejiaDev\GeoIp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class GeoIpInstallCommand extends Command
{
    public $signature = 'geo-ip:install
        {--maxmind : Download from MaxMind instead of DB-IP (requires license key)}
        {--license= : Your MaxMind license key (only with --maxmind)}';

    public $description = 'Publish the config file and download the geolocation database';

    public function handle(): int
    {
        $this->publishConfig();

        try {
            if ($this->option('maxmind')) {
                $databasePath = config(
                    'geo-ip.drivers.maxmind.database_path',
                    storage_path('app/geoip/GeoLite2-City.mmdb'),
                );

                $this->ensureDirectoryExists(dirname($databasePath));
                $this->downloadMaxMind($databasePath);
            } else {
                $databasePath = config(
                    'geo-ip.drivers.dbip.database_path',
                    storage_path('app/geoip/dbip-city-lite.mmdb'),
                );

                $this->ensureDirectoryExists(dirname($databasePath));
                $this->downloadDbIpLite($databasePath);
            }
        } catch (\Throwable $e) {
            $this->components->error("Installation failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->components->info("Database installed at [{$databasePath}].");

        return self::SUCCESS;
    }

    // ──────────────────────────────────────────────
    //  DB-IP Lite (default — free, no signup)
    // ──────────────────────────────────────────────

    protected function downloadDbIpLite(string $databasePath): void
    {
        $yearMonth = now()->format('Y-m');
        $url = "https://download.db-ip.com/free/dbip-city-lite-{$yearMonth}.mmdb.gz";

        $this->components->info("Downloading DB-IP Lite database ({$yearMonth})...");

        $tempFile = tempnam(sys_get_temp_dir(), 'geoip').'.mmdb.gz';

        try {
            $response = Http::timeout(300)->get($url);

            if ($response->failed()) {
                throw new RuntimeException("Download failed (HTTP {$response->status()}).");
            }

            file_put_contents($tempFile, $response->body());
            $this->decompressGzip($tempFile, $databasePath);
        } finally {
            $this->cleanupFiles($tempFile);
        }
    }

    protected function decompressGzip(string $source, string $destination): void
    {
        $gz = gzopen($source, 'rb');
        $out = fopen($destination, 'wb');

        if ($gz === false || $out === false) {
            throw new RuntimeException('Failed to open files for decompression.');
        }

        while (! gzeof($gz)) {
            fwrite($out, gzread($gz, 8192));
        }

        gzclose($gz);
        fclose($out);
    }

    // ──────────────────────────────────────────────
    //  MaxMind GeoLite2 (optional — requires license)
    // ──────────────────────────────────────────────

    protected function downloadMaxMind(string $databasePath): void
    {
        $licenseKey = $this->option('license')
            ?? config('geo-ip.drivers.maxmind.license_key')
            ?? $this->ask('Enter your MaxMind license key');

        if (empty($licenseKey)) {
            throw new RuntimeException(
                'A MaxMind license key is required. Get one free at https://www.maxmind.com/en/geolite2/signup'
            );
        }

        $this->components->info('Downloading GeoLite2-City database from MaxMind...');

        $tempFile = tempnam(sys_get_temp_dir(), 'geoip').'.tar.gz';

        try {
            $response = Http::timeout(300)->get('https://download.maxmind.com/app/geoip_download', [
                'edition_id' => 'GeoLite2-City',
                'license_key' => $licenseKey,
                'suffix' => 'tar.gz',
            ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    "Download failed (HTTP {$response->status()}). Please verify your MaxMind license key."
                );
            }

            file_put_contents($tempFile, $response->body());
            $this->extractTarGz($tempFile, $databasePath);
        } finally {
            $this->cleanupFiles($tempFile);
        }
    }

    protected function extractTarGz(string $archivePath, string $databasePath): void
    {
        $extractDir = sys_get_temp_dir().'/geoip_extract_'.uniqid();
        mkdir($extractDir);

        try {
            $phar = new PharData($archivePath);
            $phar->extractTo($extractDir);

            $mmdbFile = $this->findMmdbFile($extractDir);

            if ($mmdbFile === null) {
                throw new RuntimeException('Could not find .mmdb file in the downloaded archive.');
            }

            copy($mmdbFile, $databasePath);
        } finally {
            File::deleteDirectory($extractDir);
        }
    }

    protected function findMmdbFile(string $directory): ?string
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() === 'mmdb') {
                return $file->getPathname();
            }
        }

        return null;
    }

    // ──────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────

    protected function publishConfig(): void
    {
        if (file_exists(config_path('geo-ip.php'))) {
            $this->components->info('Config file already published.');

            return;
        }

        $this->call('vendor:publish', [
            '--tag' => 'geo-ip-config',
        ]);

        $this->components->info('Config file published.');
    }

    protected function ensureDirectoryExists(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function cleanupFiles(string ...$files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }

            // Also clean up .tar leftover from PharData decompression
            $tarFile = str_replace('.tar.gz', '.tar', $file);

            if ($tarFile !== $file && file_exists($tarFile)) {
                unlink($tarFile);
            }
        }
    }
}
