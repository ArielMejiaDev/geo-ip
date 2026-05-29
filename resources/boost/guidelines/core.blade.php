## GeoIp

This package resolves a visitor's geolocation (country, city, region, coordinates, timezone) from their IP address using swappable drivers: DB-IP Lite (default, free, no signup), MaxMind GeoLite2, ip-api, or ipinfo.

### Architecture

- `GeoIp` — Main service class. Use for one-shot lookups via the Facade.
- `IpAddress` — Fluent wrapper returned by `GeoIp::of()`. Supports chaining, conditionals, and serialization.
- `IpData` — Immutable value object holding geolocation fields.
- `Driver` — Interface (`ArielMejiaDev\GeoIp\Contracts\Driver`) for all lookup backends.

### Quick Lookups (Facade)

@verbatim
<code-snippet name="One-shot lookups via the GeoIp Facade" lang="php">
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::country('8.8.8.8');       // 'United States'
GeoIp::countryCode('8.8.8.8');   // 'US'
GeoIp::city('8.8.8.8');          // 'Mountain View'
GeoIp::timezone('8.8.8.8');      // 'America/Los_Angeles'
GeoIp::coordinates('8.8.8.8');   // ['latitude' => 37.386, 'longitude' => -122.084]
</code-snippet>
@endverbatim

### Fluent API

@verbatim
<code-snippet name="Fluent IP address lookups with chaining" lang="php">
use ArielMejiaDev\GeoIp\Facades\GeoIp;

// From a known IP
GeoIp::of('8.8.8.8')->country();     // 'United States'
GeoIp::of('8.8.8.8')->is('US');      // true
GeoIp::of('8.8.8.8')->isIn(['US', 'CA', 'MX']); // true

// From the current request
GeoIp::fromRequest()->countryCode();

// Conditional logic
GeoIp::of($ip)
    ->whenCountry('US', fn ($ip) => handleUs($ip))
    ->whenIn(['MX', 'CO', 'AR'], fn ($ip) => handleLatam($ip))
    ->toArray();

// Serialization
GeoIp::of('8.8.8.8')->toArray();
GeoIp::of('8.8.8.8')->toJson();
(string) GeoIp::of('8.8.8.8'); // 'Mountain View, California, United States'
</code-snippet>
@endverbatim

### Drivers

Set `GEOIP_DRIVER` in `.env`. Supported: `dbip` (default), `maxmind`, `ip-api`, `ipinfo`, `null`.

- **dbip** — Default. Free DB-IP Lite local database, no signup. Run `php artisan geo-ip:install`.
- **maxmind** — MaxMind GeoLite2 local database. Requires free signup. Run `php artisan geo-ip:install --maxmind`.
- **ip-api** — Free remote API, no key, 45 req/min rate limit.
- **ipinfo** — Remote API. Requires `IPINFO_TOKEN`.
- **null** — Returns empty data; use for testing.

### Setup

Run `php artisan geo-ip:install` — publishes config and downloads the free DB-IP Lite database. No signup or API key needed.

### Configuration

- `geo-ip.driver` — Active driver name.
- `geo-ip.cache.enabled` / `geo-ip.cache.ttl` — Cache lookups (default: 1 hour).

### Testing

Set the driver to `null` in your test environment:

@verbatim
<code-snippet name="Test configuration for GeoIp" lang="php">
// In phpunit.xml or TestCase::getEnvironmentSetUp()
config()->set('geo-ip.driver', 'null');
</code-snippet>
@endverbatim

### Custom Drivers

Implement `ArielMejiaDev\GeoIp\Contracts\Driver` with a `lookup(string $ip): IpData` method.

### Macros

Both `GeoIp` and `IpAddress` are Macroable:

@verbatim
<code-snippet name="Extending GeoIp with macros" lang="php">
use ArielMejiaDev\GeoIp\Facades\GeoIp;
use ArielMejiaDev\GeoIp\IpAddress;

IpAddress::macro('isNorthAmerican', function () {
    return $this->isIn(['US', 'CA', 'MX']);
});

GeoIp::of('8.8.8.8')->isNorthAmerican(); // true
</code-snippet>
@endverbatim
