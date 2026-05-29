# DB-IP Lite Driver

The default driver. Uses the free [DB-IP Lite](https://db-ip.com/db/download/ip-to-city-lite) local database. No external API calls, no signup, no license key.

## Setup

```bash
php artisan geo-ip:install
```

That's it. The command downloads the DB-IP Lite City database to `storage/app/geoip/dbip-city-lite.mmdb`.

## Coverage

- **8 million+ records** covering IPv4 and IPv6
- **Country level:** 99%+ accuracy
- **City level:** 75–85% accuracy (same range as all free GeoIP databases)
- **Updated monthly** by DB-IP

## Configuration

DB-IP is the default driver — no `GEOIP_DRIVER` change needed. Optionally customize the database path:

```env
DBIP_DB_PATH=storage/app/geoip/dbip-city-lite.mmdb
```

## Missing Database Error

If the `.mmdb` file is not found, the package throws a clear error:

```
DB-IP database not found at [storage/app/geoip/dbip-city-lite.mmdb].
Run [php artisan geo-ip:install] to download it.
```

## Returned Fields

The DB-IP Lite driver populates these `IpData` fields:

- `countryCode`, `country`
- `region`, `city`, `postalCode`
- `latitude`, `longitude`
- `timezone`

## Keeping the Database Updated

Re-run the install command to download the latest monthly release:

```bash
php artisan geo-ip:install
```

## Licensing

DB-IP Lite is licensed under [Creative Commons Attribution 4.0](https://creativecommons.org/licenses/by/4.0/). You must give attribution to DB-IP.com when using this database.

## When to Choose DB-IP Lite

- **Zero friction** — No signup, no API keys, just install and go
- **Production ready** — No rate limits, no network latency
- **Privacy** — IP data never leaves your server
- **Offline** — No internet access needed after download
