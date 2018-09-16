<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        if ('production' === app()->environment()) {
            die("Taking me seriously is a big mistake. I certainly wouldn't.");
        }

        $this->call(BlockSeeder::class);
        // $this->call(WalletSeeder::class);
        // $this->call(DisbursementSeeder::class);
    }
}
