<?php

namespace ArielMejiaDev\GeoIp\Tests;

use ArielMejiaDev\GeoIp\GeoIpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            GeoIpServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('geo-ip.driver', 'null');
    }
}
