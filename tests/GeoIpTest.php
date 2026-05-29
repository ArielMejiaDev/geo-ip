<?php

use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\GeoIp;
use ArielMejiaDev\GeoIp\IpAddress;
use ArielMejiaDev\GeoIp\IpData;

function fakeGeoIp(): GeoIp
{
    $driver = new class implements Driver
    {
        public function lookup(string $ip): IpData
        {
            return new IpData(
                ip: $ip,
                countryCode: 'US',
                country: 'United States',
                region: 'California',
                city: 'Mountain View',
                latitude: 37.386,
                longitude: -122.084,
                timezone: 'America/Los_Angeles',
                isp: 'Google LLC',
                postalCode: '94035',
            );
        }
    };

    return new GeoIp(driver: $driver);
}

// ── Static-style one-shot methods ────────────────

it('resolves country from ip', function () {
    expect(fakeGeoIp()->country('8.8.8.8'))->toBe('United States');
});

it('resolves country code from ip', function () {
    expect(fakeGeoIp()->countryCode('8.8.8.8'))->toBe('US');
});

it('resolves region from ip', function () {
    expect(fakeGeoIp()->region('8.8.8.8'))->toBe('California');
});

it('resolves city from ip', function () {
    expect(fakeGeoIp()->city('8.8.8.8'))->toBe('Mountain View');
});

it('resolves timezone from ip', function () {
    expect(fakeGeoIp()->timezone('8.8.8.8'))->toBe('America/Los_Angeles');
});

it('resolves coordinates from ip', function () {
    expect(fakeGeoIp()->coordinates('8.8.8.8'))->toBe([
        'latitude' => 37.386,
        'longitude' => -122.084,
    ]);
});

// ── Fluent API ───────────────────────────────────

it('creates an IpAddress instance via of()', function () {
    expect(fakeGeoIp()->of('8.8.8.8'))->toBeInstanceOf(IpAddress::class);
});

it('chains fluent methods', function () {
    $result = fakeGeoIp()
        ->of('8.8.8.8')
        ->whenCountry('US', fn (IpAddress $ip) => $ip->tap(fn () => null))
        ->toArray();

    expect($result)->toHaveKey('country_code', 'US');
});

// ── Lookup ───────────────────────────────────────

it('returns IpData from lookup()', function () {
    $data = fakeGeoIp()->lookup('8.8.8.8');

    expect($data)->toBeInstanceOf(IpData::class)
        ->and($data->ip)->toBe('8.8.8.8')
        ->and($data->countryCode)->toBe('US');
});

// ── Macroable ────────────────────────────────────

it('supports macros on GeoIp', function () {
    GeoIp::macro('isFromUS', function (string $ip) {
        return $this->countryCode($ip) === 'US';
    });

    expect(fakeGeoIp()->isFromUS('8.8.8.8'))->toBeTrue();

    GeoIp::flushMacros();
});
