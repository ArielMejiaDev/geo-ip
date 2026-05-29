# Changelog

All notable changes to `geo-ip` will be documented in this file.

## v1.0.1 - 2026-05-29

### Bug Fix

- Move `geoip2/geoip2` from `suggest` to `require` in `composer.json` — fixes `Class "GeoIp2\Database\Reader" not found` error when using the default `dbip` driver.

## v1.0.0 - 2026-05-29

### GeoIp v1.0.0

Initial release of the GeoIp package for Laravel.

#### Features

- **Dual-interface API** — Static facade methods for quick lookups + fluent `IpAddress` chain for rich, chainable operations
- **Five swappable drivers** — DB-IP Lite (default), MaxMind GeoLite2, ip-api, ipinfo, and null (testing)
- **Zero-friction default** — DB-IP Lite requires no signup, no API key — just `php artisan geo-ip:install`
- **Fluent conditionals** — `whenCountry()`, `whenIn()`, `whenResolved()` and more
- **Boolean checks** — `is()`, `isNot()`, `isIn()`, `isNotIn()`, `isResolved()`
- **Serialization** — `toArray()`, `toJson()`, `toString()`
- **Macroable** — Extend both `GeoIp` and `IpAddress` with custom methods
- **Caching** — Built-in cache support with configurable TTL
- **Artisan commands** — `geo-ip:install` (setup) and `geo-ip:lookup` (diagnostics)
- **Laravel 12 & 13** support, PHP 8.2+
