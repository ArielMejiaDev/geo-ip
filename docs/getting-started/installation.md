# Installation

## Requirements

- PHP 8.2+
- Laravel 12 or 13

## Install via Composer

```bash
composer require arielmejiadev/geo-ip
```

The package auto-registers its service provider and facade via Laravel's package discovery.

## Setup

The install command publishes the config file and downloads the free DB-IP Lite database — no signup or license key required:

```bash
php artisan geo-ip:install
```

This publishes `config/geo-ip.php` and downloads the database to `storage/app/geoip/dbip-city-lite.mmdb`.

That's it. Zero configuration needed.

## Quick Test

Verify the installation:

```bash
php artisan geo-ip:lookup 8.8.8.8
```

You should see a table with geolocation data for Google's public DNS server.
