<?php

namespace ArielMejiaDev\GeoIp;

use ArielMejiaDev\GeoIp\Commands\GeoIpCommand;
use ArielMejiaDev\GeoIp\Commands\GeoIpInstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GeoIpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('geo-ip')
            ->hasConfigFile()
            ->hasCommands([
                GeoIpCommand::class,
                GeoIpInstallCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GeoIp::class, function ($app) {
            $config = $app['config']['geo-ip'];
            $driverName = $config['driver'] ?? 'dbip';

            return new GeoIp(
                driver: GeoIp::createDriver($driverName, $config),
                cacheTtl: ($config['cache']['enabled'] ?? true)
                    ? ($config['cache']['ttl'] ?? 3600)
                    : null,
                config: $config,
            );
        });

        $this->app->alias(GeoIp::class, 'geo-ip');
    }
}
