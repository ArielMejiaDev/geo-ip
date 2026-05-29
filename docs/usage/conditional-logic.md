# Conditional Logic

`IpAddress` provides domain-specific conditional methods that keep your code fluent and expressive. These are built on top of Laravel's `Conditionable` trait.

## whenCountry / whenNotCountry

Execute a callback only when the IP matches (or doesn't match) a specific country:

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::of($ip)
    ->whenCountry('US', function ($ip) {
        // Runs only for US visitors
    })
    ->whenNotCountry('US', function ($ip) {
        // Runs for everyone except US
    });
```

## whenIn / whenNotIn

Execute a callback when the country is in (or not in) a list:

```php
GeoIp::of($ip)
    ->whenIn(['MX', 'CO', 'AR', 'CL', 'PE'], function ($ip) {
        // LATAM visitors
    })
    ->whenNotIn(['CN', 'RU'], function ($ip) {
        // Not from China or Russia
    });
```

## whenResolved

Execute a callback only when the lookup succeeded:

```php
GeoIp::of($ip)->whenResolved(function ($ip) {
    // The lookup found geolocation data
});
```

## Default Callbacks

All conditional methods accept an optional default callback that runs when the condition is false:

```php
GeoIp::of($ip)->whenCountry('US',
    fn ($ip) => showDomesticPricing(),
    fn ($ip) => showInternationalPricing(),
);
```

## Generic when / unless

The `Conditionable` trait adds generic `when()` and `unless()` methods for arbitrary conditions:

```php
GeoIp::of($ip)
    ->when($user->isPremium(), function ($ip) {
        // Premium user logic
    })
    ->unless($isBot, function ($ip) {
        // Not a bot
    });
```

## Chaining Multiple Conditions

All conditional methods return `static`, so you can chain as many as needed:

```php
GeoIp::of($ip)
    ->whenCountry('US', fn ($ip) => setLocale('en_US'))
    ->whenCountry('MX', fn ($ip) => setLocale('es_MX'))
    ->whenCountry('BR', fn ($ip) => setLocale('pt_BR'))
    ->whenNotIn(['US', 'MX', 'BR'], fn ($ip) => setLocale('en'))
    ->tap(fn ($ip) => Log::info("Locale set for {$ip->countryCode()}"))
    ->toArray();
```
