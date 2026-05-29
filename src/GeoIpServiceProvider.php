<?php

namespace ArielMejiaDev\GeoIp;

use ArielMejiaDev\GeoIp\Commands\GeoIpCommand;
use ArielMejiaDev\GeoIp\Commands\GeoIpInstallCommand;
use ArielMejiaDev\GeoIp\Contracts\Driver;
use ArielMejiaDev\GeoIp\Drivers\DbIpDriver;
use ArielMejiaDev\GeoIp\Drivers\IpApiDriver;
use ArielMejiaDev\GeoIp\Drivers\IpInfoDriver;
use ArielMejiaDev\GeoIp\Drivers\MaxMindDriver;
use ArielMejiaDev\GeoIp\Drivers\NullDriver;
use InvalidArgumentException;
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

            return new GeoIp(
                driver: $this->createDriver($config),
                cacheTtl: ($config['cache']['enabled'] ?? true)
                    ? ($config['cache']['ttl'] ?? 3600)
                    : null,
            );
        });

        $this->app->alias(GeoIp::class, 'geo-ip');
    }

    protected function createDriver(array $config): Driver
    {
        $driver = $config['driver'] ?? 'dbip';

        return match ($driver) {
            'dbip' => new DbIpDriver(
                databasePath: $config['drivers']['dbip']['database_path']
                    ?? storage_path('app/geoip/dbip-city-lite.mmdb'),
            ),
            'maxmind' => new MaxMindDriver(
                databasePath: $config['drivers']['maxmind']['database_path']
                    ?? storage_path('app/geoip/GeoLite2-City.mmdb'),
            ),
            'ip-api' => new IpApiDriver,
            'ipinfo' => new IpInfoDriver(
                token: $config['drivers']['ipinfo']['token'] ?? '',
            ),
            'null' => new NullDriver,
            default => throw new InvalidArgumentException("Unsupported GeoIp driver [{$driver}]."),
        };
    }
}
