<?php

namespace ArielMejiaDev\GeoIp\Drivers;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;
use Illuminate\Support\Facades\Http;

class IpApiDriver implements Driver
{
    public function lookup(string $ip): IpData
    {
        $response = Http::get("http://ip-api.com/json/{$ip}", [
            'fields' => 'status,country,countryCode,regionName,city,lat,lon,timezone,isp,zip',
        ]);

        $data = $response->json();

        if (($data['status'] ?? '') !== 'success') {
            return new IpData(ip: $ip);
        }

        return new IpData(
            ip: $ip,
            countryCode: $data['countryCode'] ?? null,
            country: $data['country'] ?? null,
            region: $data['regionName'] ?? null,
            city: $data['city'] ?? null,
            latitude: $data['lat'] ?? null,
            longitude: $data['lon'] ?? null,
            timezone: $data['timezone'] ?? null,
            isp: $data['isp'] ?? null,
            postalCode: $data['zip'] ?? null,
        );
    }
}
