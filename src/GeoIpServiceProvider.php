<?php

namespace ArielMejiaDev\GeoIp;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ArielMejiaDev\GeoIp\Commands\GeoIpCommand;

class GeoIpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('geo-ip')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_geo_ip_table')
            ->hasCommand(GeoIpCommand::class);
    }
}
