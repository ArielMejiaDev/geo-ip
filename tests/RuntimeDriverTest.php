<?php

use ArielMejiaDev\GeoIp\GeoIp;
use ArielMejiaDev\GeoIp\IpData;

// Cincinnati Union Terminal area — University of Cincinnati IP range.
$cincinnatiIp = '129.137.1.1';

it('switches driver at runtime via driver() method', function () {
    $geoip = app(GeoIp::class);
    $swapped = $geoip->driver('null');

    expect($swapped)->toBeInstanceOf(GeoIp::class)
        ->and($swapped)->not->toBe($geoip);

    $data = $swapped->lookup('8.8.8.8');

    expect($data)->toBeInstanceOf(IpData::class)
        ->and($data->isResolved())->toBeFalse();
});

it('resolves a Cincinnati IP similarly with dbip and ip-api drivers', function () use ($cincinnatiIp) {
    $dbipPath = config('geo-ip.drivers.dbip.database_path')
        ?? storage_path('app/geoip/dbip-city-lite.mmdb');

    if (! file_exists($dbipPath)) {
        $this->markTestSkipped('DB-IP database not installed. Run: php artisan geo-ip:install');
    }

    $geoip = app(GeoIp::class);

    $dbip = $geoip->driver('dbip')->lookup($cincinnatiIp);
    $ipApi = $geoip->driver('ip-api')->lookup($cincinnatiIp);

    // Both should resolve successfully.
    expect($dbip->isResolved())->toBeTrue('DB-IP failed to resolve')
        ->and($ipApi->isResolved())->toBeTrue('ip-api failed to resolve');

    // Both should agree on country.
    expect($dbip->countryCode)->toBe('US')
        ->and($ipApi->countryCode)->toBe('US');

    // Both should agree on state.
    expect($dbip->region)->toBe('Ohio')
        ->and($ipApi->region)->toBe('Ohio');

    // Both should agree on city.
    expect($dbip->city)->toBe('Cincinnati')
        ->and($ipApi->city)->toBe('Cincinnati');

    // Both should agree on timezone.
    expect($dbip->timezone)->toBe('America/New_York')
        ->and($ipApi->timezone)->toBe('America/New_York');
})->group('integration');
