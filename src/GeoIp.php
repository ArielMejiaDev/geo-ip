<?php

namespace ArielMejiaDev\GeoIp;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\Drivers\DbIpDriver;
use ArielMejiaDev\GeoIp\Drivers\IpApiDriver;
use ArielMejiaDev\GeoIp\Drivers\IpInfoDriver;
use ArielMejiaDev\GeoIp\Drivers\MaxMindDriver;
use ArielMejiaDev\GeoIp\Drivers\NullDriver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

class GeoIp
{
    use Macroable;

    public function __construct(
        protected Driver $driver,
        protected ?int $cacheTtl = null,
        protected array $config = [],
    ) {}

    public function driver(string $name): self
    {
        return new self(
            driver: $this->createDriver($name, $this->config),
            cacheTtl: $this->cacheTtl,
            config: $this->config,
        );
    }

    public static function createDriver(string $name, array $config): Driver
    {
        return match ($name) {
            'dbip' => new DbIpDriver(
                databasePath: $config['drivers']['dbip']['database_path']
                    ?? storage_path('app/geoip/dbip-city-lite.mmdb'),
            ),
            'maxmind' => new MaxMindDriver(
                databasePath: $config['drivers']['maxmind']['database_path']
                    ?? storage_path('app/geoip/GeoLite2-City.mmdb'),
            ),
            'ip-api' => new IpApiDriver,
            'ipinfo' => new IpInfoDriver(
                token: $config['drivers']['ipinfo']['token'] ?? '',
            ),
            'null' => new NullDriver,
            default => throw new InvalidArgumentException("Unsupported GeoIp driver [{$name}]."),
        };
    }

    public function of(string $ip): IpAddress
    {
        return new IpAddress($ip, $this->resolve($ip));
    }

    public function fromRequest(): IpAddress
    {
        return $this->of(request()->ip());
    }

    public function lookup(string $ip): IpData
    {
        return $this->resolve($ip);
    }

    public function country(string $ip): ?string
    {
        return $this->resolve($ip)->country;
    }

    public function countryCode(string $ip): ?string
    {
        return $this->resolve($ip)->countryCode;
    }

    public function region(string $ip): ?string
    {
        return $this->resolve($ip)->region;
    }

    public function city(string $ip): ?string
    {
        return $this->resolve($ip)->city;
    }

    public function timezone(string $ip): ?string
    {
        return $this->resolve($ip)->timezone;
    }

    public function coordinates(string $ip): array
    {
        $data = $this->resolve($ip);

        return [
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
        ];
    }

    protected function resolve(string $ip): IpData
    {
        if ($this->cacheTtl === null) {
            return $this->driver->lookup($ip);
        }

        return Cache::remember(
            "geo-ip:{$ip}",
            $this->cacheTtl,
            fn () => $this->driver->lookup($ip),
        );
    }
}
