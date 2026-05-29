<?php

namespace ArielMejiaDev\GeoIp\Drivers;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use RuntimeException;

class MaxMindDriver implements Driver
{
    protected Reader $reader;

    public function __construct(string $databasePath)
    {
        if (! file_exists($databasePath)) {
            throw new RuntimeException(
                "MaxMind database not found at [{$databasePath}]. "
                .'Run [php artisan geo-ip:install --maxmind --license=your-key] to download it.'
            );
        }

        $this->reader = new Reader($databasePath);
    }

    public function lookup(string $ip): IpData
    {
        try {
            $record = $this->reader->city($ip);

            return new IpData(
                ip: $ip,
                countryCode: $record->country->isoCode,
                country: $record->country->name,
                region: $record->mostSpecificSubdivision->name,
                city: $record->city->name,
                latitude: $record->location->latitude,
                longitude: $record->location->longitude,
                timezone: $record->location->timeZone,
                postalCode: $record->postal->code,
            );
        } catch (AddressNotFoundException) {
            return new IpData(ip: $ip);
        }
    }
}
