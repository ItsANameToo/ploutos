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
        $parsed = parse_url(config('delegate.host'));

        if ($connection = fsockopen($parsed['host'], $parsed['port'], $errCode, $errStr, 1)) {
            Artisan::call('up');
        } else {
            Artisan::call('down');
        }

        fclose($connection);
    }
}
