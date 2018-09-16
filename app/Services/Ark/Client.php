<?php

namespace App\Services\Ark;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class Client
{
    /**
     * The Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Create a new Client instance.
     */
    public function __construct()
    {
        $this->client = GuzzleFactory::make([
            'base_uri' => config('delegate.host'),
        ]);
    }

    /**
     * Get the wallet for the given id.
     *
     * @param string $address
     *
     * @return array
     */
    public function wallet(string $address): array
    {
        return $this->get('api/accounts', compact('address'))['account'];
    }

    /**
     * Get the transaction for the given id.
     *
     * @param string $id
     *
     * @return array
     */
    public function transaction(string $id): array
    {
        return $this->get('api/transactions/get', compact('id'));
    }

    /**
     * Get voters for the configured delegate.
     *
     * @return array
     */
    public function voters(): array
    {
        return $this->get('api/delegates/voters', [
            'publicKey' => config('delegate.publicKey'),
        ])['accounts'];
    }

    /**
     * Get information about the configured delegate.
     *
     * @return array
     */
    public function delegate(): array
    {
        return $this->get('api/delegates/get', [
            'username' => config('delegate.username'),
        ])['delegate'];
    }

    /**
     * Get a list of well performing peers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function peers(): Collection
    {
        $peers = $this->get('api/peers')['peers'];
        $peers = collect($peers)->sortByDesc('height')->sortBy('delay');

        return $peers->filter(function ($peer) {
            return in_array(array_get($peer, 'version', '0.0.0'), [
                '1.0.3', '1.1.0',
            ], true);
        })->filter(function ($peer) {
            return array_get($peer, 'delay', 1000) < 500;
        })->reject(function ($peer) {
            return 'OK' !== array_get($peer, 'status', 'UNKNOWN');
        });
    }

    /**
     * [broadcast description].
     *
     * @param string $uri
     * @param array  $transaction
     */
    public function broadcast(string $uri, array $transaction): void
    {
        $response = $this->post("{$uri}/peer/transactions", [
            'transactions' => [$transaction],
        ]);

        if (empty($response['transactionIds'])) {
            Log::emergency($response['message'], [
                'transaction' => json_encode($transaction),
            ]);
        } else {
            Log::info('Broadcasted '.$response['transactionIds'][0]);
        }
    }

    /**
     * Send a HTTP GET request.
     *
     * @param string $path
     * @param array  $query
     *
     * @return array
     */
    public function get(string $path, array $query = []): array
    {
        $response = $this->client->get($path, compact('query'));

        return json_decode($response->getBody(), true);
    }

    /**
     * Send a HTTP POST request.
     *
     * @param string $path
     * @param array  $json
     *
     * @return array
     */
    public function post(string $path, array $json): array
    {
        $response = $this->client->post($path, compact('json') + [
            'headers' => [
                'nethash' => '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988',
                'version' => '1.1.0',
                'port'    => 1,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
