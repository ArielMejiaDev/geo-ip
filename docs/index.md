---
layout: home
hero:
  name: GeoIp
  text: IP Geolocation for Laravel
  tagline: Resolve visitor country, city, and coordinates from their IP address — with a fluent, expressive API and swappable drivers.
  actions:
    - theme: brand
      text: Get Started
      link: /getting-started/installation
    - theme: alt
      text: View on GitHub
      link: https://github.com/arielmejiadev/geo-ip
features:
  - title: Fluent API
    details: Chain methods like whenCountry(), isIn(), and toArray() — inspired by Laravel's Str / Stringable pattern.
  - title: Swappable Drivers
    details: Choose between DB-IP Lite (default, free, no signup), MaxMind GeoLite2, ip-api, or ipinfo. Switch with a single env variable.
  - title: Built-in Caching
    details: Avoid redundant lookups with configurable cache TTL. Works with any Laravel cache driver.
  - title: Macroable & Extensible
    details: Add custom methods at runtime with macros on both GeoIp and IpAddress classes.
---
