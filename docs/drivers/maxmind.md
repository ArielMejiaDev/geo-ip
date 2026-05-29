# MaxMind Driver

Uses a local [MaxMind GeoLite2](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) database file. No external API calls — all lookups are local and fast.

## Setup

### 1. Get a MaxMind license key

Sign up for a free account at [maxmind.com/en/geolite2/signup](https://www.maxmind.com/en/geolite2/signup) and generate a license key.

### 2. Download the database

```bash
php artisan geo-ip:install --maxmind --license=your-license-key
```

Or add the key to `.env` first:

```env
MAXMIND_LICENSE_KEY=your-license-key
```

```bash
php artisan geo-ip:install --maxmind
```

### 3. Set the driver

```env
GEOIP_DRIVER=maxmind
```

## Configuration

Optionally customize the database path:

```env
MAXMIND_DB_PATH=storage/app/geoip/GeoLite2-City.mmdb
```

Default path: `storage/app/geoip/GeoLite2-City.mmdb`.

## Returned Fields

The MaxMind driver populates all `IpData` fields except `isp`:

- `countryCode`, `country`
- `region`, `city`, `postalCode`
- `latitude`, `longitude`
- `timezone`

## Keeping the Database Updated

Re-run the install command:

```bash
php artisan geo-ip:install --maxmind
```

## When to Choose MaxMind

- You need **higher confidence** city-level accuracy (MaxMind is more conservative but more precise)
- Your organization already uses MaxMind products
- You want access to MaxMind's paid GeoIP2 databases for even more data
