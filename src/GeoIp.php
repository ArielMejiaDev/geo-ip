<?php

namespace ArielMejiaDev\GeoIp;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\Macroable;

class GeoIp
{
    use Macroable;

    public function __construct(
        protected Driver $driver,
        protected ?int $cacheTtl = null,
    ) {}

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
