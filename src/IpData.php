<?php

namespace ArielMejiaDev\GeoIp;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class IpData implements Arrayable, JsonSerializable
{
    public function __construct(
        public readonly string $ip,
        public readonly ?string $countryCode = null,
        public readonly ?string $country = null,
        public readonly ?string $region = null,
        public readonly ?string $city = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $timezone = null,
        public readonly ?string $isp = null,
        public readonly ?string $postalCode = null,
    ) {}

    public function isResolved(): bool
    {
        return $this->countryCode !== null;
    }

    public function toArray(): array
    {
        return [
            'ip' => $this->ip,
            'country_code' => $this->countryCode,
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => $this->timezone,
            'isp' => $this->isp,
            'postal_code' => $this->postalCode,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
