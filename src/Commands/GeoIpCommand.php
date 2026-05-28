<?php

namespace ArielMejiaDev\GeoIp\Commands;

use Illuminate\Console\Command;

class GeoIpCommand extends Command
{
    public $signature = 'geo-ip';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
