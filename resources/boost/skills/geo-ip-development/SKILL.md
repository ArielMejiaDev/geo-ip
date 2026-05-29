---
name: geo-ip-development
description: Resolve visitor country and geolocation from IP addresses using the GeoIp package. Use when implementing IP geolocation lookups, country detection, regional content, location-based routing, conditional logic by country, or driver configuration for DB-IP, MaxMind, ip-api, and ipinfo.
metadata:
  author: ArielMejiaDev
  version: "1.0"
---

# GeoIp Development

## When to use this skill

Use this skill when:

- Looking up geolocation data (country, city, region, timezone, coordinates) from an IP address.
- Implementing country-based conditional logic or regional content.
- Configuring or switching between geolocation drivers (DB-IP, MaxMind, ip-api, ipinfo).
- Writing tests that involve IP geolocation.
- Extending the package with custom drivers or macros.

## Core Concepts

### Two API Styles

The package provides two complementary interfaces, following the same pattern as Laravel's `Str` / `Stringable`:

**1. One-shot Facade calls** — Quick, static-style lookups:

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::country('8.8.8.8');       // 'United States'
GeoIp::countryCode('8.8.8.8');   // 'US'
GeoIp::city('8.8.8.8');          // 'Mountain View'
GeoIp::region('8.8.8.8');        // 'California'
GeoIp::timezone('8.8.8.8');      // 'America/Los_Angeles'
GeoIp::coordinates('8.8.8.8');   // ['latitude' => 37.386, 'longitude' => -122.084]
GeoIp::lookup('8.8.8.8');        // IpData value object
```

**2. Fluent `IpAddress` chain** — Rich, chainable interface via `of()`:

```php
$ip = GeoIp::of('8.8.8.8');

// Data accessors
$ip->country();      // 'United States'
$ip->countryCode();  // 'US'
$ip->city();         // 'Mountain View'
$ip->region();       // 'California'
$ip->latitude();     // 37.386
$ip->longitude();    // -122.084
$ip->coordinates();  // ['latitude' => 37.386, 'longitude' => -122.084]
$ip->timezone();     // 'America/Los_Angeles'
$ip->isp();          // 'Google LLC'
$ip->postalCode();   // '94035'

// From the current HTTP request
$ip = GeoIp::fromRequest();
```

### Boolean Checks

```php
$ip = GeoIp::of('8.8.8.8');

$ip->is('US');                    // true
$ip->isNot('MX');                 // true
$ip->isIn(['US', 'CA', 'MX']);    // true
$ip->isNotIn(['DE', 'FR']);       // true
$ip->isResolved();                // true (false when lookup failed)
```

### Conditional Chaining

Domain-specific conditionals keep your code fluent and expressive:

```php
GeoIp::of($ip)
    ->whenCountry('US', function ($ip) {
        // Runs only for US visitors
    })
    ->whenNotCountry('US', function ($ip) {
        // Runs for non-US visitors
    })
    ->whenIn(['MX', 'CO', 'AR'], function ($ip) {
        // Runs for LATAM visitors
    })
    ->whenNotIn(['CN', 'RU'], function ($ip) {
        // Runs when NOT in the list
    })
    ->whenResolved(function ($ip) {
        // Runs when the lookup succeeded
    });
```

You can also provide a default callback:

```php
GeoIp::of($ip)->whenCountry('US',
    fn ($ip) => showDomesticPricing(),
    fn ($ip) => showInternationalPricing(),
);
```

Generic `when()` and `unless()` from Laravel's `Conditionable` trait are also available:

```php
GeoIp::of($ip)
    ->when($user->isPremium(), fn ($ip) => $ip->tap(fn () => logPremiumVisitor()))
    ->unless($isBot, fn ($ip) => $ip->tap(fn () => trackVisitor()));
```

### Serialization

```php
$ip = GeoIp::of('8.8.8.8');

$ip->toArray();   // ['ip' => '8.8.8.8', 'country_code' => 'US', ...]
$ip->toJson();    // JSON string
$ip->toString();  // 'Mountain View, California, United States'
(string) $ip;     // same as toString()
```

### Pipeline & Side Effects

```php
// tap() — side effects without breaking the chain
GeoIp::of($ip)
    ->tap(fn ($ip) => Log::info("Visitor from {$ip->country()}"))
    ->toArray();

// pipe() — transform and exit the chain
$greeting = GeoIp::of($ip)->pipe(fn ($ip) => "Hello from {$ip->city()}!");

// dump() / dd() — debugging
GeoIp::of($ip)->dump()->toArray();  // dumps mid-chain
GeoIp::of($ip)->dd();               // dumps and dies
```

## Driver Configuration

### Setting the driver

In `.env`:

```
GEOIP_DRIVER=dbip
```

Publish the config file:

```bash
php artisan vendor:publish --tag=geo-ip-config
```

### Available drivers

| Driver | Env Value | Requirements | Rate Limit |
|--------|-----------|-------------|------------|
| DB-IP Lite | `dbip` (default) | `php artisan geo-ip:install` (free, no signup) | None (local) |
| MaxMind GeoLite2 | `maxmind` | License key + `php artisan geo-ip:install --maxmind` | None (local) |
| ip-api.com | `ip-api` | None | 45 req/min |
| ipinfo.io | `ipinfo` | `IPINFO_TOKEN` | Depends on plan |
| Null (testing) | `null` | None | N/A |

### ipinfo setup

```
GEOIP_DRIVER=ipinfo
IPINFO_TOKEN=your-token-here
```

### DB-IP Lite setup (default driver)

Download the free DB-IP Lite database (no signup required):

```bash
php artisan geo-ip:install
```

This publishes the config and downloads the database to `storage/app/geoip/dbip-city-lite.mmdb`.

### MaxMind GeoLite2 setup (optional)

```bash
php artisan geo-ip:install --maxmind --license=your-license-key
```

Then set the driver:

```
GEOIP_DRIVER=maxmind
```

### Cache configuration

```
GEOIP_CACHE_ENABLED=true
GEOIP_CACHE_TTL=3600
```

## Creating a Custom Driver

Implement `ArielMejiaDev\GeoIp\Contracts\Driver`:

```php
use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\IpData;

class MyCustomDriver implements Driver
{
    public function lookup(string $ip): IpData
    {
        // Your lookup logic here

        return new IpData(
            ip: $ip,
            countryCode: 'US',
            country: 'United States',
            region: 'California',
            city: 'Los Angeles',
            latitude: 34.0522,
            longitude: -118.2437,
            timezone: 'America/Los_Angeles',
            isp: 'Example ISP',
            postalCode: '90001',
        );
    }
}
```

Bind it in a service provider:

```php
$this->app->singleton(GeoIp::class, fn () => new GeoIp(
    driver: new MyCustomDriver,
    cacheTtl: 3600,
));
```

## Extending with Macros

Both `GeoIp` and `IpAddress` support macros:

```php
use ArielMejiaDev\GeoIp\IpAddress;

// In a service provider boot() method
IpAddress::macro('isEuropean', function () {
    return $this->isIn(['DE', 'FR', 'ES', 'IT', 'PT', 'NL', 'BE', 'AT', 'CH', 'SE', 'NO', 'DK', 'FI', 'PL', 'CZ', 'RO', 'GR', 'HU', 'IE']);
});

// Usage
GeoIp::of('1.2.3.4')->isEuropean();
```

## Testing

Set the driver to `null` so tests never call external APIs:

```php
// phpunit.xml
<env name="GEOIP_DRIVER" value="null"/>
```

Or in your `TestCase`:

```php
protected function getEnvironmentSetUp($app)
{
    $app['config']->set('geo-ip.driver', 'null');
}
```

For tests that need specific geo data, bind a fake driver:

```php
use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\GeoIp;
use ArielMejiaDev\GeoIp\IpData;

$fakeDriver = new class implements Driver {
    public function lookup(string $ip): IpData
    {
        return new IpData(ip: $ip, countryCode: 'US', country: 'United States');
    }
};

$this->app->singleton(GeoIp::class, fn () => new GeoIp(driver: $fakeDriver));
```

## Artisan Command

Test your driver configuration from the terminal:

```bash
php artisan geo-ip:lookup 8.8.8.8
```
