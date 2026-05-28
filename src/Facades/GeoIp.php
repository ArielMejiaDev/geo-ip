<?php

namespace ArielMejiaDev\GeoIp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ArielMejiaDev\GeoIp\GeoIp
 */
class GeoIp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ArielMejiaDev\GeoIp\GeoIp::class;
    }
}
