<?php

namespace ArielMejiaDev\GeoIp\Drivers;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;

class NullDriver implements Driver
{
    public function lookup(string $ip): IpData
    {
        return new IpData(ip: $ip);
    }
}
