<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PingNode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:ping';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $parsed = parse_url(delegate_host());
        $port = $parsed['port'] ?? ('https' === strtolower($parsed['scheme']) ? 443 : 80);

        if ($connection = @fsockopen($parsed['host'], $port, $errCode, $errStr, 1)) {
            fclose($connection);

            Artisan::call('up');
        } else {
            Artisan::call('down');
        }
    }
}
