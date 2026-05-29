<?php

namespace ArielMejiaDev\GeoIp\Facades;

use ArielMejiaDev\GeoIp\IpAddress;
use ArielMejiaDev\GeoIp\IpData;
use Illuminate\Support\Facades\Facade;

/**
 * @method static IpAddress of(string $ip)
 * @method static IpAddress fromRequest()
 * @method static IpData lookup(string $ip)
 * @method static string|null country(string $ip)
 * @method static string|null countryCode(string $ip)
 * @method static string|null region(string $ip)
 * @method static string|null city(string $ip)
 * @method static string|null timezone(string $ip)
 * @method static array coordinates(string $ip)
 *
 * @see \ArielMejiaDev\GeoIp\GeoIp
 */
class GeoIp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ArielMejiaDev\GeoIp\GeoIp::class;
    }
}
