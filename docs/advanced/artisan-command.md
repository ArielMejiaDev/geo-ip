# Artisan Command

GeoIp ships with a diagnostic command to test your driver configuration directly from the terminal.

## Usage

```bash
php artisan geo-ip:lookup {ip?}
```

The `ip` argument is optional and defaults to `8.8.8.8`.

## Examples

```bash
# Look up Google's DNS
php artisan geo-ip:lookup

# Look up a specific IP
php artisan geo-ip:lookup 1.1.1.1
```

## Output

The command displays a table with all geolocation fields:

```
INFO  Looking up [8.8.8.8] using the [dbip] driver.

+-------------+---------------------+
| Field       | Value               |
+-------------+---------------------+
| ip          | 8.8.8.8             |
| country_code| US                  |
| country     | United States       |
| region      | California          |
| city        | Mountain View       |
| latitude    | 37.386              |
| longitude   | -122.084            |
| timezone    | America/Los_Angeles |
| isp         | Google LLC          |
| postal_code | 94035               |
+-------------+---------------------+
```

## Troubleshooting

If the command fails:

- **"Lookup failed"** — Check your driver configuration and API keys.
- **All values show "-"** — The driver returned an unresolved result. The IP may be private (e.g., `127.0.0.1`, `192.168.x.x`).
- **MaxMind errors** — Verify the `.mmdb` file exists at the configured path and `geoip2/geoip2` is installed.
