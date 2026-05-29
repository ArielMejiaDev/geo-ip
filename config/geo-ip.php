<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "dbip", "maxmind", "ip-api", "ipinfo", "null"
    |
    */

    'driver' => env('GEOIP_DRIVER', 'dbip'),

    /*
    |--------------------------------------------------------------------------
    | Driver Configuration
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Resolved lookups are cached to avoid redundant API calls or disk reads.
    | Set "enabled" to false or "ttl" to null to disable caching entirely.
    |
    */

    'cache' => [
        'enabled' => env('GEOIP_CACHE_ENABLED', true),
        'ttl' => env('GEOIP_CACHE_TTL', 3600),
    ],

];
