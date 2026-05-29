# GeoIp for Laravel

Resolve a visitor's country and geolocation from their IP address using swappable drivers — DB-IP Lite (default, free, no signup), MaxMind GeoLite2, ip-api, or ipinfo.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arielmejiadev/geo-ip.svg?style=flat-square)](https://packagist.org/packages/arielmejiadev/geo-ip)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/arielmejiadev/geo-ip/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/arielmejiadev/geo-ip/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/arielmejiadev/geo-ip.svg?style=flat-square)](https://packagist.org/packages/arielmejiadev/geo-ip)

## Documentation

Full documentation is available at **[arielmejiadev.github.io/geo-ip](https://arielmejiadev.github.io/geo-ip/)**.

## Quick Start

```bash
composer require arielmejiadev/geo-ip
php artisan geo-ip:install
```

This publishes the config and downloads the free DB-IP Lite database (no signup or API key needed).

## Usage

### One-shot lookups

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::country('8.8.8.8');       // 'United States'
GeoIp::countryCode('8.8.8.8');   // 'US'
GeoIp::city('8.8.8.8');          // 'Mountain View'
GeoIp::timezone('8.8.8.8');      // 'America/Los_Angeles'
GeoIp::coordinates('8.8.8.8');   // ['latitude' => 37.386, 'longitude' => -122.084]
```

### Fluent API

```php
$ip = GeoIp::of('8.8.8.8');

$ip->country();      // 'United States'
$ip->is('US');       // true
$ip->isIn(['US', 'CA', 'MX']); // true

// Conditional logic
GeoIp::of($ip)
    ->whenCountry('US', fn ($ip) => handleUs($ip))
    ->whenIn(['MX', 'CO', 'AR'], fn ($ip) => handleLatam($ip))
    ->toArray();

// From the current request
GeoIp::fromRequest()->countryCode();
```

## Drivers

| Driver | Env Value | Requirements |
|--------|-----------|-------------|
| DB-IP Lite | `dbip` (default) | `php artisan geo-ip:install` |
| MaxMind GeoLite2 | `maxmind` | License key + `php artisan geo-ip:install --maxmind` |
| ip-api.com | `ip-api` | None (45 req/min limit) |
| ipinfo.io | `ipinfo` | `IPINFO_TOKEN` |
| Null | `null` | None (for testing) |

Set the driver in `.env`:

```env
GEOIP_DRIVER=dbip
```

## Testing

```bash
composer test
```

Set the driver to `null` in your test environment to avoid external calls:

```xml
<env name="GEOIP_DRIVER" value="null"/>
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ArielMejiaDev](https://github.com/ArielMejiaDev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
