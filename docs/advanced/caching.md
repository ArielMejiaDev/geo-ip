# Caching

GeoIp caches resolved lookups to avoid redundant API calls or disk reads. Caching is enabled by default with a 1-hour TTL.

## Configuration

```env
GEOIP_CACHE_ENABLED=true
GEOIP_CACHE_TTL=3600
```

Or in `config/geo-ip.php`:

```php
'cache' => [
    'enabled' => env('GEOIP_CACHE_ENABLED', true),
    'ttl' => env('GEOIP_CACHE_TTL', 3600), // seconds
],
```

## Disabling Cache

```env
GEOIP_CACHE_ENABLED=false
```

When caching is disabled, every call goes directly to the driver.

## Cache Keys

Lookups are cached with the key pattern `geo-ip:{ip}`:

```
geo-ip:8.8.8.8
geo-ip:1.1.1.1
```

## Clearing Cache

Clear a specific IP:

```php
Cache::forget('geo-ip:8.8.8.8');
```

Clear all GeoIp cache (if using a tagged or prefixed cache store, flush accordingly):

```bash
php artisan cache:clear
```

## Cache Store

GeoIp uses Laravel's default cache store. If you need a specific store, you can configure it at the Laravel level with `CACHE_STORE`.

## Recommendations

| Scenario | Recommended TTL |
|----------|----------------|
| API drivers (ip-api, ipinfo) | 3600–86400 (1h–24h) |
| Local databases (dbip, maxmind) | 0 or disabled (already fast) |
| Testing | Disabled |
