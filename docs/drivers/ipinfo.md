# ipinfo Driver

Uses the [ipinfo.io](https://ipinfo.io) API. Requires an API token.

## Setup

1. Get your token from [ipinfo.io](https://ipinfo.io/signup)

2. Add to your `.env`:

```env
GEOIP_DRIVER=ipinfo
IPINFO_TOKEN=your-token-here
```

## Returned Fields

The ipinfo driver populates these `IpData` fields:

- `countryCode` — ISO 3166-1 alpha-2 code
- `country` — Returns the country code (ipinfo doesn't return full names in the basic tier)
- `region`, `city`, `postalCode`
- `latitude`, `longitude` — Parsed from ipinfo's `loc` field
- `timezone`
- `isp` — From ipinfo's `org` field

## Plans

ipinfo offers free and paid tiers:

| Plan | Requests/month | Features |
|------|---------------|----------|
| Free | 50,000 | Basic geolocation |
| Basic | 150,000 | + ASN data |
| Standard | 250,000 | + Company, carrier |
| Business | 500,000 | + Privacy detection |

## When to Choose ipinfo

- You need HTTPS for API calls
- You need higher rate limits than ip-api's free tier
- You want company/ASN/privacy data (via a custom driver extension)
