# Testing

GeoIp is designed for easy testing. Use the `null` driver to prevent any external API calls in your test suite.

## Null Driver

Set the driver to `null` in your test environment:

**Option 1: phpunit.xml**

```xml
<env name="GEOIP_DRIVER" value="null"/>
```

**Option 2: TestCase setup**

```php
protected function getEnvironmentSetUp($app)
{
    $app['config']->set('geo-ip.driver', 'null');
}
```

The `null` driver returns an `IpData` with only the `ip` field populated. All other fields are `null`, and `isResolved()` returns `false`.

## Switching Drivers in phpunit.xml

You can override any driver from your `phpunit.xml` using environment variables:

```xml
<!-- Use the null driver (no external calls) -->
<env name="GEOIP_DRIVER" value="null"/>

<!-- Use ip-api for integration tests -->
<env name="GEOIP_DRIVER" value="ip-api"/>

<!-- Use ipinfo with a test token -->
<env name="GEOIP_DRIVER" value="ipinfo"/>
<env name="IPINFO_TOKEN" value="test-token-here"/>

<!-- Use DB-IP Lite with a test database -->
<env name="GEOIP_DRIVER" value="dbip"/>
<env name="DBIP_DB_PATH" value="tests/fixtures/dbip-city-lite.mmdb"/>

<!-- Use MaxMind with a test database -->
<env name="GEOIP_DRIVER" value="maxmind"/>
<env name="MAXMIND_DB_PATH" value="tests/fixtures/GeoLite2-City-Test.mmdb"/>

<!-- Disable caching during tests -->
<env name="GEOIP_CACHE_ENABLED" value="false"/>
```

A typical `phpunit.xml` setup for testing:

```xml
<phpunit>
    <php>
        <env name="GEOIP_DRIVER" value="null"/>
        <env name="GEOIP_CACHE_ENABLED" value="false"/>
    </php>
</phpunit>
```

## Faking Specific Data

When your tests need specific geolocation data, bind a fake driver:

```php
use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\GeoIp;
use ArielMejiaDev\GeoIp\IpData;

it('shows domestic pricing for US visitors', function () {
    $fakeDriver = new class implements Driver {
        public function lookup(string $ip): IpData
        {
            return new IpData(
                ip: $ip,
                countryCode: 'US',
                country: 'United States',
                city: 'New York',
            );
        }
    };

    $this->app->singleton(GeoIp::class, fn () => new GeoIp(driver: $fakeDriver));

    $response = $this->get('/pricing');

    $response->assertSee('Domestic pricing');
});
```

## Testing with IpAddress Directly

For unit tests that don't need the full service container, create `IpAddress` instances directly:

```php
use ArielMejiaDev\GeoIp\IpAddress;
use ArielMejiaDev\GeoIp\IpData;

$ip = new IpAddress('8.8.8.8', new IpData(
    ip: '8.8.8.8',
    countryCode: 'US',
    country: 'United States',
));

expect($ip->is('US'))->toBeTrue();
expect($ip->country())->toBe('United States');
```

## Testing Conditionals

```php
it('applies LATAM pricing for Mexican visitors', function () {
    $ip = new IpAddress('1.2.3.4', new IpData(
        ip: '1.2.3.4',
        countryCode: 'MX',
        country: 'Mexico',
    ));

    $applied = false;

    $ip->whenIn(['MX', 'CO', 'AR'], function () use (&$applied) {
        $applied = true;
    });

    expect($applied)->toBeTrue();
});
```

## Testing Macros

```php
it('checks custom macro', function () {
    IpAddress::macro('isNorthAmerican', function () {
        return $this->isIn(['US', 'CA', 'MX']);
    });

    $ip = new IpAddress('8.8.8.8', new IpData(ip: '8.8.8.8', countryCode: 'US'));

    expect($ip->isNorthAmerican())->toBeTrue();

    IpAddress::flushMacros();
});
```
