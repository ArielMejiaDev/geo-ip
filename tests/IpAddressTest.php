<?php

use ArielMejiaDev\GeoIp\IpAddress;
use ArielMejiaDev\GeoIp\IpData;

function fakeIp(string $ip = '8.8.8.8', ?string $countryCode = 'US', ?string $country = 'United States'): IpAddress
{
    return new IpAddress($ip, new IpData(
        ip: $ip,
        countryCode: $countryCode,
        country: $country,
        region: 'California',
        city: 'Mountain View',
        latitude: 37.386,
        longitude: -122.084,
        timezone: 'America/Los_Angeles',
        isp: 'Google LLC',
        postalCode: '94035',
    ));
}

// ── Data accessors ───────────────────────────────

it('exposes all data accessors', function () {
    $ip = fakeIp();

    expect($ip->ip())->toBe('8.8.8.8')
        ->and($ip->country())->toBe('United States')
        ->and($ip->countryCode())->toBe('US')
        ->and($ip->region())->toBe('California')
        ->and($ip->city())->toBe('Mountain View')
        ->and($ip->latitude())->toBe(37.386)
        ->and($ip->longitude())->toBe(-122.084)
        ->and($ip->timezone())->toBe('America/Los_Angeles')
        ->and($ip->isp())->toBe('Google LLC')
        ->and($ip->postalCode())->toBe('94035');
});

it('returns coordinates as an associative array', function () {
    expect(fakeIp()->coordinates())->toBe([
        'latitude' => 37.386,
        'longitude' => -122.084,
    ]);
});

// ── Boolean checks ───────────────────────────────

it('checks country with is()', function () {
    $ip = fakeIp();

    expect($ip->is('US'))->toBeTrue()
        ->and($ip->is('us'))->toBeTrue()
        ->and($ip->is('MX'))->toBeFalse();
});

it('checks country with isNot()', function () {
    expect(fakeIp()->isNot('MX'))->toBeTrue()
        ->and(fakeIp()->isNot('US'))->toBeFalse();
});

it('checks country with isIn()', function () {
    expect(fakeIp()->isIn(['US', 'CA', 'MX']))->toBeTrue()
        ->and(fakeIp()->isIn(['DE', 'FR']))->toBeFalse();
});

it('checks country with isNotIn()', function () {
    expect(fakeIp()->isNotIn(['DE', 'FR']))->toBeTrue()
        ->and(fakeIp()->isNotIn(['US', 'CA']))->toBeFalse();
});

it('reports resolved status', function () {
    expect(fakeIp()->isResolved())->toBeTrue();

    $unresolved = new IpAddress('127.0.0.1', new IpData(ip: '127.0.0.1'));
    expect($unresolved->isResolved())->toBeFalse();
});

// ── Domain conditionals ──────────────────────────

it('runs callback with whenCountry()', function () {
    $called = false;

    fakeIp()->whenCountry('US', function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

it('skips callback when whenCountry() does not match', function () {
    $called = false;

    fakeIp()->whenCountry('MX', function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeFalse();
});

it('runs default callback when whenCountry() does not match', function () {
    $result = null;

    fakeIp()->whenCountry(
        'MX',
        function () use (&$result) {
            $result = 'matched';
        },
        function () use (&$result) {
            $result = 'default';
        },
    );

    expect($result)->toBe('default');
});

it('chains whenNotCountry()', function () {
    $called = false;

    fakeIp()->whenNotCountry('MX', function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

it('chains whenIn()', function () {
    $called = false;

    fakeIp()->whenIn(['US', 'CA'], function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

it('chains whenNotIn()', function () {
    $called = false;

    fakeIp()->whenNotIn(['DE', 'FR'], function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

it('chains whenResolved()', function () {
    $called = false;

    fakeIp()->whenResolved(function () use (&$called) {
        $called = true;
    });

    expect($called)->toBeTrue();
});

// ── Pipeline ─────────────────────────────────────

it('pipes to a callback', function () {
    $result = fakeIp()->pipe(fn (IpAddress $ip) => $ip->countryCode());

    expect($result)->toBe('US');
});

// ── Tappable ─────────────────────────────────────

it('taps without modifying the chain', function () {
    $tapped = null;

    $ip = fakeIp()->tap(function (IpAddress $ip) use (&$tapped) {
        $tapped = $ip->country();
    });

    expect($tapped)->toBe('United States')
        ->and($ip)->toBeInstanceOf(IpAddress::class);
});

// ── Serialization ────────────────────────────────

it('converts to array', function () {
    $array = fakeIp()->toArray();

    expect($array)
        ->toHaveKey('ip', '8.8.8.8')
        ->toHaveKey('country_code', 'US');
});

it('converts to json', function () {
    $json = fakeIp()->toJson();

    expect(json_decode($json, true))->toHaveKey('country_code', 'US');
});

it('converts to a human readable string', function () {
    expect(fakeIp()->toString())->toBe('Mountain View, California, United States');
});

it('casts to string via __toString()', function () {
    expect((string) fakeIp())->toBe('Mountain View, California, United States');
});

// ── Macroable ────────────────────────────────────

it('supports macros', function () {
    IpAddress::macro('isNorthAmerican', function () {
        return $this->isIn(['US', 'CA', 'MX']);
    });

    expect(fakeIp()->isNorthAmerican())->toBeTrue();

    // Clean up
    IpAddress::flushMacros();
});
