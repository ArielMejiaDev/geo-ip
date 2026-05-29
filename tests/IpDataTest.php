<?php

use ArielMejiaDev\GeoIp\IpData;

it('stores all geo data', function () {
    $data = new IpData(
        ip: '8.8.8.8',
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

    expect($data->ip)->toBe('8.8.8.8')
        ->and($data->countryCode)->toBe('US')
        ->and($data->country)->toBe('United States')
        ->and($data->region)->toBe('California')
        ->and($data->city)->toBe('Mountain View')
        ->and($data->latitude)->toBe(37.386)
        ->and($data->longitude)->toBe(-122.084)
        ->and($data->timezone)->toBe('America/Los_Angeles')
        ->and($data->isp)->toBe('Google LLC')
        ->and($data->postalCode)->toBe('94035');
});

it('defaults all optional fields to null', function () {
    $data = new IpData(ip: '127.0.0.1');

    expect($data->ip)->toBe('127.0.0.1')
        ->and($data->countryCode)->toBeNull()
        ->and($data->country)->toBeNull()
        ->and($data->city)->toBeNull();
});

it('reports resolved status based on country code', function () {
    expect(new IpData(ip: '8.8.8.8', countryCode: 'US'))
        ->isResolved()->toBeTrue();

    expect(new IpData(ip: '127.0.0.1'))
        ->isResolved()->toBeFalse();
});

it('converts to array with snake_case keys', function () {
    $data = new IpData(ip: '8.8.8.8', countryCode: 'US', country: 'United States');

    $array = $data->toArray();

    expect($array)->toHaveKeys(['ip', 'country_code', 'country', 'region', 'city'])
        ->and($array['ip'])->toBe('8.8.8.8')
        ->and($array['country_code'])->toBe('US');
});

it('serializes to json', function () {
    $data = new IpData(ip: '8.8.8.8', countryCode: 'US');

    $json = json_encode($data);

    expect(json_decode($json, true))->toHaveKey('country_code', 'US');
});
