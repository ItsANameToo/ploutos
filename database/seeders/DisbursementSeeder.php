<?php

namespace Database\Seeders;

use App\Models\Disbursement;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DisbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Wallet::all()->each(function ($wallet) {
            for ($i = 0; $i < 20; $i++) {
                $disbursement = Disbursement::factory()->make([
                    'wallet_id'  => $wallet->id,
                    'signed_at'  => Carbon::now()->subDays($i),
                    'created_at' => Carbon::now()->addMinutes($i),
                ]);

                $wallet->disbursements()->save($disbursement);
            }
        });
    }
}
