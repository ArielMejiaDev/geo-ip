# Macros

Both `GeoIp` and `IpAddress` use Laravel's `Macroable` trait, allowing you to add custom methods at runtime.

## Adding Macros to IpAddress

Register macros in a service provider's `boot()` method:

```php
use ArielMejiaDev\GeoIp\IpAddress;

public function boot(): void
{
    IpAddress::macro('isNorthAmerican', function () {
        return $this->isIn(['US', 'CA', 'MX']);
    });

    IpAddress::macro('isEuropean', function () {
        return $this->isIn([
            'DE', 'FR', 'ES', 'IT', 'PT', 'NL', 'BE', 'AT', 'CH',
            'SE', 'NO', 'DK', 'FI', 'PL', 'CZ', 'RO', 'GR', 'HU', 'IE',
        ]);
    });

    IpAddress::macro('isLatam', function () {
        return $this->isIn([
            'MX', 'CO', 'AR', 'CL', 'PE', 'BR', 'VE', 'EC', 'BO',
            'PY', 'UY', 'CR', 'PA', 'GT', 'HN', 'SV', 'NI', 'DO', 'CU',
        ]);
    });
}
```

Usage:

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::of('8.8.8.8')->isNorthAmerican();  // true
GeoIp::of('8.8.8.8')->isEuropean();       // false
GeoIp::of('8.8.8.8')->isLatam();          // false
```

## Adding Macros to GeoIp

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::macro('isFromUS', function (string $ip) {
    return $this->countryCode($ip) === 'US';
});

GeoIp::isFromUS('8.8.8.8'); // true
```

## Conditional Macros

Combine macros with domain conditionals for expressive chains:

```php
IpAddress::macro('whenNorthAmerican', function (callable $callback, ?callable $default = null) {
    return $this->when($this->isNorthAmerican(), $callback, $default);
});

GeoIp::of($ip)
    ->whenNorthAmerican(fn ($ip) => applyNaftaRules())
    ->toArray();
```
