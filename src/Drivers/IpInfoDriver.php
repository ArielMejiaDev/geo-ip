<?php

namespace ArielMejiaDev\GeoIp\Drivers;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;
use Illuminate\Support\Facades\Http;

class IpInfoDriver implements Driver
{
    public function __construct(
        protected string $token,
    ) {}

    public function lookup(string $ip): IpData
    {
        $response = Http::withToken($this->token)
            ->get("https://ipinfo.io/{$ip}/json");

        $data = $response->json();

        if (isset($data['error'])) {
            return new IpData(ip: $ip);
        }

        [$latitude, $longitude] = isset($data['loc'])
            ? array_map('floatval', explode(',', $data['loc']))
            : [null, null];

        return new IpData(
            ip: $ip,
            countryCode: $data['country'] ?? null,
            country: $data['country'] ?? null,
            region: $data['region'] ?? null,
            city: $data['city'] ?? null,
            latitude: $latitude,
            longitude: $longitude,
            timezone: $data['timezone'] ?? null,
            isp: $data['org'] ?? null,
            postalCode: $data['postal'] ?? null,
        );
    }
}
