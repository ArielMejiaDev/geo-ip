# Runtime Driver

You can override the configured driver at runtime using the `driver()` method. This returns a new `GeoIp` instance with the specified driver — the default singleton remains unchanged.

## Basic Usage

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

// Use ip-api for this lookup, regardless of the configured default
GeoIp::driver('ip-api')->lookup('8.8.8.8');
```

## With Quick Lookups

All quick lookup methods are available on the returned instance:

```php
GeoIp::driver('maxmind')->country('8.8.8.8');       // 'United States'
GeoIp::driver('ipinfo')->countryCode('8.8.8.8');     // 'US'
GeoIp::driver('ip-api')->timezone('8.8.8.8');        // 'America/Los_Angeles'
GeoIp::driver('ip-api')->coordinates('8.8.8.8');     // ['latitude' => ..., 'longitude' => ...]
```

## With the Fluent API

Chain `driver()` with the fluent API for full expressiveness:

```php
GeoIp::driver('maxmind')->of('8.8.8.8')
    ->whenCountry('US', fn ($ip) => applyDomesticRates())
    ->toArray();

GeoIp::driver('ip-api')->fromRequest()->city();
```

## Supported Drivers

Pass any supported driver name: `dbip`, `maxmind`, `ip-api`, `ipinfo`, or `null`.

```php
// Use the null driver in specific scenarios
GeoIp::driver('null')->lookup('8.8.8.8');
```

An `InvalidArgumentException` is thrown for unsupported driver names.

## When to Use

- **Comparing results** across multiple providers for the same IP
- **Fallback logic** — try a local database first, then hit an API
- **Testing** — switch to the `null` driver without changing config

## Example: Cross-Driver Verification

The following test uses a University of Cincinnati IP (`129.137.1.1`, near Cincinnati Union Terminal) to verify that two independent drivers — a local database and a remote API — resolve the same address consistently:

```php
use ArielMejiaDev\GeoIp\GeoIp;

it('resolves a Cincinnati IP similarly with dbip and ip-api drivers', function () {
    $cincinnatiIp = '129.137.1.1';

    $geoip = app(GeoIp::class);

    $dbip  = $geoip->driver('dbip')->lookup($cincinnatiIp);
    $ipApi = $geoip->driver('ip-api')->lookup($cincinnatiIp);

    // Both should resolve successfully.
    expect($dbip->isResolved())->toBeTrue()
        ->and($ipApi->isResolved())->toBeTrue();

    // Both should agree on country.
    expect($dbip->countryCode)->toBe('US')
        ->and($ipApi->countryCode)->toBe('US');

    // Both should agree on state.
    expect($dbip->region)->toBe('Ohio')
        ->and($ipApi->region)->toBe('Ohio');

    // Both should agree on city.
    expect($dbip->city)->toBe('Cincinnati')
        ->and($ipApi->city)->toBe('Cincinnati');

    // Both should agree on timezone.
    expect($dbip->timezone)->toBe('America/New_York')
        ->and($ipApi->timezone)->toBe('America/New_York');
});
```

This pattern is useful for auditing data quality, validating a new driver before switching, or ensuring consistent results across providers.
