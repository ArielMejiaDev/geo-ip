<?php

namespace ArielMejiaDev\GeoIp\Contracts;

use ArielMejiaDev\GeoIp\IpData;

interface Driver
{
    public function lookup(string $ip): IpData;
}
