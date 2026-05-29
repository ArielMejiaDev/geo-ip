# Custom Drivers

You can create your own driver by implementing the `Driver` interface.

## Creating a Driver

Implement `ArielMejiaDev\GeoIp\Contracts\Driver`:

```php
<?php

namespace App\GeoIp;

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;

class MyApiDriver implements Driver
{
    public function lookup(string $ip): IpData
    {
        // Call your API or data source
        $response = Http::get("https://my-api.com/lookup/{$ip}");
        $data = $response->json();

        return new IpData(
            ip: $ip,
            countryCode: $data['country_code'] ?? null,
            country: $data['country_name'] ?? null,
            region: $data['region'] ?? null,
            city: $data['city'] ?? null,
            latitude: $data['lat'] ?? null,
            longitude: $data['lng'] ?? null,
            timezone: $data['timezone'] ?? null,
            isp: $data['isp'] ?? null,
            postalCode: $data['postal'] ?? null,
        );
    }
}
```

## Registering the Driver

Bind your driver in a service provider:

```php
use ArielMejiaDev\GeoIp\GeoIp;
use App\GeoIp\MyApiDriver;

public function register(): void
{
    $this->app->singleton(GeoIp::class, fn () => new GeoIp(
        driver: new MyApiDriver,
        cacheTtl: 3600,
    ));
}
```

## Error Handling

When a lookup fails, return an `IpData` object with only the `ip` field:

```php
public function lookup(string $ip): IpData
{
    try {
        // ... your lookup logic
    } catch (\Throwable $e) {
        return new IpData(ip: $ip);
    }
}
```

This ensures `isResolved()` returns `false` and all other accessors return `null` gracefully.

## IpData Fields

The `IpData` constructor accepts these named parameters:

| Parameter | Type | Required |
|-----------|------|----------|
| `ip` | `string` | Yes |
| `countryCode` | `?string` | No |
| `country` | `?string` | No |
| `region` | `?string` | No |
| `city` | `?string` | No |
| `latitude` | `?float` | No |
| `longitude` | `?float` | No |
| `timezone` | `?string` | No |
| `isp` | `?string` | No |
| `postalCode` | `?string` | No |
