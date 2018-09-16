<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDelegateEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:earnings:delegate:clear';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Cache::forever('delegate.earnings', 0);
    }
}
