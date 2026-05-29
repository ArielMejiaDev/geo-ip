# Quick Lookups

The `GeoIp` facade provides one-shot static methods for the most common lookups. Each method takes an IP address string and returns the resolved value directly.

## Available Methods

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::country('8.8.8.8');       // 'United States'
GeoIp::countryCode('8.8.8.8');   // 'US'
GeoIp::region('8.8.8.8');        // 'California'
GeoIp::city('8.8.8.8');          // 'Mountain View'
GeoIp::timezone('8.8.8.8');      // 'America/Los_Angeles'
GeoIp::coordinates('8.8.8.8');   // ['latitude' => 37.386, 'longitude' => -122.084]
```

## Raw Lookup

To get the full `IpData` value object with all fields:

```php
$data = GeoIp::lookup('8.8.8.8');

$data->ip;          // '8.8.8.8'
$data->countryCode; // 'US'
$data->country;     // 'United States'
$data->region;      // 'California'
$data->city;        // 'Mountain View'
$data->latitude;    // 37.386
$data->longitude;   // -122.084
$data->timezone;    // 'America/Los_Angeles'
$data->isp;         // 'Google LLC'
$data->postalCode;  // '94035'
```

`IpData` is an immutable value object with `readonly` properties. It implements `Arrayable` and `JsonSerializable`:

```php
$data->toArray();       // associative array with snake_case keys
json_encode($data);     // JSON string
$data->isResolved();    // true if the lookup found a country
```

## When to Use Quick Lookups vs Fluent API

Use quick lookups when you need a single piece of data:

```php
$country = GeoIp::countryCode($request->ip());
```

Use the [fluent API](/usage/fluent-api) when you need multiple fields or conditional logic:

```php
GeoIp::of($request->ip())
    ->whenCountry('US', fn ($ip) => applyDomesticRates())
    ->toArray();
```
