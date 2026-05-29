<?php

namespace ArielMejiaDev\GeoIp\Commands;

use ArielMejiaDev\GeoIp\GeoIp;
use Illuminate\Console\Command;

class GeoIpCommand extends Command
{
    public $signature = 'geo-ip:lookup {ip=8.8.8.8 : The IP address to look up}';

    public $description = 'Look up geolocation data for an IP address';

    public function handle(GeoIp $geoIp): int
    {
        $ip = $this->argument('ip');

        $this->components->info("Looking up [{$ip}] using the [{$this->driver()}] driver.");

        try {
            $data = $geoIp->lookup($ip);
        } catch (\Throwable $e) {
            $this->components->error("Lookup failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->table(
            ['Field', 'Value'],
            collect($data->toArray())
                ->map(fn ($value, $key) => [$key, $value ?? '-'])
                ->values()
                ->all(),
        );

        return self::SUCCESS;
    }

    protected function driver(): string
    {
        return config('geo-ip.driver', 'dbip');
    }
}
