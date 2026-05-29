# Configuration

After running `php artisan geo-ip:install`, you'll find `config/geo-ip.php` with these options:

## Driver

Set the active driver via the `GEOIP_DRIVER` environment variable:

```env
GEOIP_DRIVER=dbip
```

Supported values: `dbip`, `maxmind`, `ip-api`, `ipinfo`, `null`.

## Driver-specific Settings

```php
'drivers' => [

    'dbip' => [
        'database_path' => env('DBIP_DB_PATH', storage_path('app/geoip/dbip-city-lite.mmdb')),
    ],

    'maxmind' => [
        'database_path' => env('MAXMIND_DB_PATH', storage_path('app/geoip/GeoLite2-City.mmdb')),
        'license_key' => env('MAXMIND_LICENSE_KEY'),
    ],

    'ip-api' => [
        // Free tier — no API key required.
        // Rate limit: 45 requests per minute.
    ],

    'ipinfo' => [
        'token' => env('IPINFO_TOKEN'),
    ],

],
```

## Cache

Lookups are cached by default to avoid redundant disk reads or API calls:

```php
'cache' => [
    'enabled' => env('GEOIP_CACHE_ENABLED', true),
    'ttl' => env('GEOIP_CACHE_TTL', 3600), // seconds
],
```

Set `GEOIP_CACHE_ENABLED=false` to disable caching entirely.

## Full Configuration File

```php
return [
    'driver' => env('GEOIP_DRIVER', 'dbip'),

    'drivers' => [
        'dbip' => [
            'database_path' => env('DBIP_DB_PATH', storage_path('app/geoip/dbip-city-lite.mmdb')),
        ],
        'maxmind' => [
            'database_path' => env('MAXMIND_DB_PATH', storage_path('app/geoip/GeoLite2-City.mmdb')),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
        ],
        'ip-api' => [],
        'ipinfo' => [
            'token' => env('IPINFO_TOKEN'),
        ],
    ],

    'cache' => [
        'enabled' => env('GEOIP_CACHE_ENABLED', true),
        'ttl' => env('GEOIP_CACHE_TTL', 3600),
    ],
];
```
