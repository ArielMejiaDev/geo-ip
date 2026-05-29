<?php

namespace ArielMejiaDev\GeoIp;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use JsonSerializable;
use Stringable;

class IpAddress implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    use Conditionable, Dumpable, Macroable, Tappable;

    public function __construct(
        protected string $ip,
        protected IpData $data,
    ) {}

    // ──────────────────────────────────────────────
    //  Data accessors
    // ──────────────────────────────────────────────

    public function ip(): string
    {
        return $this->ip;
    }

    public function country(): ?string
    {
        return $this->data->country;
    }

    public function countryCode(): ?string
    {
        return $this->data->countryCode;
    }

    public function region(): ?string
    {
        return $this->data->region;
    }

    public function city(): ?string
    {
        return $this->data->city;
    }

    public function latitude(): ?float
    {
        return $this->data->latitude;
    }

    public function longitude(): ?float
    {
        return $this->data->longitude;
    }

    public function coordinates(): array
    {
        return [
            'latitude' => $this->data->latitude,
            'longitude' => $this->data->longitude,
        ];
    }

    public function timezone(): ?string
    {
        return $this->data->timezone;
    }

    public function isp(): ?string
    {
        return $this->data->isp;
    }

    public function postalCode(): ?string
    {
        return $this->data->postalCode;
    }

    // ──────────────────────────────────────────────
    //  Boolean checks
    // ──────────────────────────────────────────────

    public function is(string $countryCode): bool
    {
        return strtoupper($this->data->countryCode ?? '') === strtoupper($countryCode);
    }

    public function isNot(string $countryCode): bool
    {
        return ! $this->is($countryCode);
    }

    public function isIn(array $countryCodes): bool
    {
        return in_array(
            strtoupper($this->data->countryCode ?? ''),
            array_map('strtoupper', $countryCodes),
        );
    }

    public function isNotIn(array $countryCodes): bool
    {
        return ! $this->isIn($countryCodes);
    }

    public function isResolved(): bool
    {
        return $this->data->isResolved();
    }

    // ──────────────────────────────────────────────
    //  Domain conditionals
    // ──────────────────────────────────────────────

    public function whenCountry(string $code, callable $callback, ?callable $default = null): static
    {
        return $this->when($this->is($code), $callback, $default);
    }

    public function whenNotCountry(string $code, callable $callback, ?callable $default = null): static
    {
        return $this->when($this->isNot($code), $callback, $default);
    }

    public function whenIn(array $codes, callable $callback, ?callable $default = null): static
    {
        return $this->when($this->isIn($codes), $callback, $default);
    }

    public function whenNotIn(array $codes, callable $callback, ?callable $default = null): static
    {
        return $this->when($this->isNotIn($codes), $callback, $default);
    }

    public function whenResolved(callable $callback, ?callable $default = null): static
    {
        return $this->when($this->isResolved(), $callback, $default);
    }

    // ──────────────────────────────────────────────
    //  Pipeline
    // ──────────────────────────────────────────────

    public function pipe(callable $callback): mixed
    {
        return $callback($this);
    }

    // ──────────────────────────────────────────────
    //  Serialization
    // ──────────────────────────────────────────────

    public function toArray(): array
    {
        return $this->data->toArray();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function toString(): string
    {
        return collect([
            $this->data->city,
            $this->data->region,
            $this->data->country,
        ])->filter()->implode(', ');
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
