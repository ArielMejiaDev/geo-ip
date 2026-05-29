<?php

use ArielMejiaDev\GeoIp\Drivers\NullDriver;

it('returns unresolved IpData', function () {
    $driver = new NullDriver;
    $data = $driver->lookup('8.8.8.8');

    expect($data->ip)->toBe('8.8.8.8')
        ->and($data->isResolved())->toBeFalse()
        ->and($data->countryCode)->toBeNull();
});
