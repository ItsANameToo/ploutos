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

        $host = $parsed['host'] ?? null;
        $port = $parsed['port'] ?? $this->defaultPort($parsed['scheme'] ?? 'http');

        $connection = $host ? @fsockopen($host, $port, $errCode, $errStr, 1) : false;

        if ($connection) {
            fclose($connection);

            Artisan::call('up');
        } else {
            Artisan::call('down');
        }
    }

    private function defaultPort(string $scheme): int
    {
        return strtolower($scheme) === 'https' ? 443 : 80;
    }
}
