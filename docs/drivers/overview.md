# Drivers Overview

GeoIp uses a driver-based architecture. Each driver implements the `ArielMejiaDev\GeoIp\Contracts\Driver` interface and resolves IP addresses to geolocation data.

## Available Drivers

| Driver | Config Value | Requirements | Rate Limit |
|--------|-------------|-------------|------------|
| [DB-IP Lite](/drivers/dbip) | `dbip` (default) | `php artisan geo-ip:install` | None (local) |
| [MaxMind](/drivers/maxmind) | `maxmind` | License key + `php artisan geo-ip:install --maxmind` | None (local) |
| [ip-api](/drivers/ip-api) | `ip-api` | None | 45 req/min |
| [ipinfo](/drivers/ipinfo) | `ipinfo` | `IPINFO_TOKEN` | Depends on plan |
| Null | `null` | None | N/A |

## Switching Drivers

Set the `GEOIP_DRIVER` environment variable:

```env
GEOIP_DRIVER=dbip
```

Or update `config/geo-ip.php` directly:

```php
'driver' => 'maxmind',
```

### At Runtime

You can also override the driver on the fly using the `driver()` method. See [Runtime Driver](/advanced/runtime-driver) for details.

```php
GeoIp::driver('ip-api')->lookup('8.8.8.8');
```

## Data Fields

All drivers return an `IpData` value object with these fields. Some fields may be `null` depending on the driver's capabilities:

| Field | Type | Description |
|-------|------|-------------|
| `ip` | `string` | The queried IP address |
| `countryCode` | `?string` | ISO 3166-1 alpha-2 code (e.g., `US`) |
| `country` | `?string` | Full country name |
| `region` | `?string` | State or region name |
| `city` | `?string` | City name |
| `latitude` | `?float` | Geographic latitude |
| `longitude` | `?float` | Geographic longitude |
| `timezone` | `?string` | IANA timezone (e.g., `America/Los_Angeles`) |
| `isp` | `?string` | Internet service provider |
| `postalCode` | `?string` | Postal / ZIP code |

## Null Driver

The `null` driver returns an `IpData` object with only the `ip` field populated. All other fields are `null`. Use it for testing:

```env
GEOIP_DRIVER=null
```
