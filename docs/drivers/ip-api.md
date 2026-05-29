# ip-api Driver

Uses the free [ip-api.com](http://ip-api.com) API — no API key required.

## Setup

```env
GEOIP_DRIVER=ip-api
```

## Rate Limits

The free tier allows **45 requests per minute**. If you exceed this limit, the API returns an error and GeoIp returns an unresolved `IpData` object.

For higher throughput, consider:
- Enabling [caching](/advanced/caching) (enabled by default)
- Switching to [DB-IP Lite](/drivers/dbip) or [MaxMind](/drivers/maxmind) for unlimited local lookups
- Using ip-api's paid pro tier (use a custom driver)

## Returned Fields

The ip-api driver populates all `IpData` fields:

- `countryCode`, `country`
- `region`, `city`, `postalCode`
- `latitude`, `longitude`
- `timezone`
- `isp`

## Limitations

- IPv4 only in the free tier
- HTTP only (no HTTPS in free tier)
- Not suitable for high-traffic production without caching
