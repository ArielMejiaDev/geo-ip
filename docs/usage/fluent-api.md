# Fluent API

The fluent API is built around the `IpAddress` class, which wraps the resolved geolocation data in a chainable interface — inspired by Laravel's `Str::of()` / `Stringable` pattern.

## Creating an IpAddress Instance

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

// From a known IP
$ip = GeoIp::of('8.8.8.8');

// From the current HTTP request
$ip = GeoIp::fromRequest();
```

## Data Accessors

Every accessor returns the raw value and exits the chain:

```php
$ip = GeoIp::of('8.8.8.8');

$ip->ip();           // '8.8.8.8'
$ip->country();      // 'United States'
$ip->countryCode();  // 'US'
$ip->region();       // 'California'
$ip->city();         // 'Mountain View'
$ip->latitude();     // 37.386
$ip->longitude();    // -122.084
$ip->coordinates();  // ['latitude' => 37.386, 'longitude' => -122.084]
$ip->timezone();     // 'America/Los_Angeles'
$ip->isp();          // 'Google LLC'
$ip->postalCode();   // '94035'
```

## Boolean Checks

```php
$ip = GeoIp::of('8.8.8.8');

$ip->is('US');                     // true
$ip->isNot('MX');                  // true
$ip->isIn(['US', 'CA', 'MX']);     // true
$ip->isNotIn(['DE', 'FR']);        // true
$ip->isResolved();                 // true (false when lookup failed)
```

Country codes are case-insensitive — `is('us')` and `is('US')` both work.

## Pipeline

Use `pipe()` to transform the `IpAddress` into any value:

```php
$greeting = GeoIp::of('8.8.8.8')->pipe(
    fn ($ip) => "Hello from {$ip->city()}!"
);
// 'Hello from Mountain View!'
```

## Side Effects with tap()

Use `tap()` for side effects without breaking the chain:

```php
GeoIp::of($ip)
    ->tap(fn ($ip) => Log::info("Visitor from {$ip->country()}"))
    ->toArray();
```

## Debugging

```php
GeoIp::of('8.8.8.8')->dump()->toArray();  // dumps mid-chain, continues
GeoIp::of('8.8.8.8')->dd();               // dumps and dies
```

## Traits

`IpAddress` uses these Laravel traits:

| Trait | Methods |
|-------|---------|
| `Conditionable` | `when()`, `unless()` |
| `Dumpable` | `dump()`, `dd()` |
| `Macroable` | Runtime method registration |
| `Tappable` | `tap()` |
