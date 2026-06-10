# CLAUDE.md

Project guidelines for AI assistants working on the **GeoIp** Laravel package.

## Project overview

A Laravel package that resolves visitor geolocation from IP addresses using swappable drivers (DB-IP Lite, MaxMind, ip-api, ipinfo, null). It exposes a dual API: static facade methods for quick lookups and a fluent `IpAddress` chain for rich, conditional operations.

## Tech stack

- **PHP 8.2+** with strict typed, readonly properties
- **Laravel 12 / 13** (Illuminate contracts, facades, traits)
- **Spatie Laravel Package Tools** for service provider scaffolding
- **Pest 4** for testing (not PHPUnit directly)
- **PHPStan level 5** via Larastan for static analysis
- **Laravel Pint** for code formatting
- **VitePress** for documentation site (`docs/`)

## Architecture

```
src/
  Contracts/Driver.php      # Single-method interface: lookup(string $ip): IpData
  Drivers/                  # One class per driver, each implements Driver
  GeoIp.php                 # Main service class (singleton, Macroable)
  GeoIpServiceProvider.php  # Registers singleton + aliases, no driver logic
  IpData.php                # Immutable value object (readonly props, Arrayable, JsonSerializable)
  IpAddress.php             # Fluent wrapper (Conditionable, Dumpable, Macroable, Tappable)
  Facades/GeoIp.php         # Facade with @method docblocks for IDE support
  Commands/                 # Artisan commands (install, lookup)
```

### Key patterns

- **Driver resolution** lives in `GeoIp::createDriver()` (static factory via match expression).
- **Runtime driver switching** via `GeoIp::driver('name')` returns a new instance — it never mutates the singleton.
- The service provider passes the full config array to `GeoIp` so `driver()` can resolve any driver on the fly.
- Drivers return `new IpData(ip: $ip)` on failure (unresolved) — never throw from `lookup()`.
- Cache layer is in `GeoIp::resolve()`, controlled by `$cacheTtl` (null = disabled).

## Code conventions

- **Named arguments** for constructors: `new IpData(ip: $ip, countryCode: 'US', ...)`.
- **`match` expressions** over switch/if chains.
- **No inheritance** on the main classes — use composition, traits, and the Driver interface.
- **snake_case** for config keys and array output; **camelCase** for PHP properties and methods.
- Keep Facade `@method` docblocks in sync when adding public methods to `GeoIp`.
- PHPStan must pass at level 5: `composer analyse`.
- Format with Pint before committing: `composer format`.

## Testing

- **Framework**: Pest 4 (functional style with `it()` / `expect()`).
- **Base TestCase** uses Orchestra Testbench with `geo-ip.driver` set to `null`.
- **Unit tests** use anonymous `Driver` implementations (see `fakeGeoIp()` in `GeoIpTest.php`).
- **Integration tests** (group: `integration`) skip gracefully when prerequisites are missing (e.g., DB-IP database file or network).
- Run tests: `composer test` or `./vendor/bin/pest`.
- Always flush macros after macro tests: `GeoIp::flushMacros()`.

## Documentation (VitePress)

- Source: `docs/` with Markdown files.
- Config: `docs/.vitepress/config.ts` (sidebar, nav).
- **Build after changes**: `cd docs && npm run build`.
- Code examples should use the `GeoIp` facade import and be copy-pasteable.
- When adding a new page, also add it to the sidebar in `config.ts`.

## Commands

| Task | Command |
|------|---------|
| Run tests | `composer test` |
| Static analysis | `composer analyse` |
| Format code | `composer format` |
| Build docs | `cd docs && npm run build` |
| Dev docs server | `cd docs && npm run dev` |

## Release workflow

1. Update `CHANGELOG.md` with the new version entry.
2. Commit, push to `main`.
3. Create a GitHub release with `gh release create vX.Y.Z`.
4. Packagist picks up the new tag automatically.
