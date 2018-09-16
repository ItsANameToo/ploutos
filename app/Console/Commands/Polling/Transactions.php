<?php

namespace App\Console\Commands\Polling;

use App\Models\Wallet;
use App\Services\Ark\Client;
use Illuminate\Console\Command;

class Transactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:poll:transactions {pages=5}';

    public function __construct(Client $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        for ($i = 0; $i < $this->argument('pages'); $i++) {
            $transactions = $this->getTransactions($i);

            foreach ($transactions as $transaction) {
                if (empty($transaction['vendorField'])) {
                    continue;
                }

                // NOTE: if you want to index historical data you should adjust
                // the next few lines to avoid indexing transactions that have
                // not been send to your voters.

                // if ('Delegate payout' === $transaction['vendorField']) {
                //     continue;
                // }

                // $containsOldPurpose = str_contains($transaction['vendorField'], 'payout to height');
                // $containsNewPurpose = $transaction['vendorField'] === config('delegate.vendorField');

                // if (! $containsOldPurpose && ! $containsNewPurpose) {
                //     continue;
                // }

                $this->line('Polling Transaction: <info>'.$transaction['id'].'</info>');

                $struct = [
                    'transaction_id' => $transaction['id'],
                    'amount'         => $transaction['amount'],
                    'purpose'        => $transaction['vendorField'],
                    'signed_at'      => humanize_epoch($transaction['timestamp']),
                    'transaction'    => transform_transfer($transaction),
                ];

                try {
                    $wallet = Wallet::findByAddress($transaction['recipientId']);
                } catch (\Exception $e) {
                    $response = $this->client->wallet($transaction['recipientId']);

                    $wallet = Wallet::firstOrCreate([
                        'address'    => $response['address'],
                        'public_key' => $response['publicKey'],
                    ]);
                }

                $wallet->disbursements()->firstOrCreate(
                    ['transaction_id' => $struct['transaction_id']],
                    $struct
                );
            }
        }
    }

    private function getTransactions(int $page): array
    {
        return $this->client->get('api/transactions', [
            'senderId' => config('delegate.address'),
            'offset'   => 50 * $page,
            'limit'    => 50,
            'orderBy'  => 'timestamp:desc',
        ])['transactions'];
    }
}
