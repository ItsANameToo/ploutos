<?php

namespace App\Services\Ark;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;
use Illuminate\Support\Collection;

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
        return $this->get('wallets/'.$address)['data'];
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
        return $this->get('transactions/'.$id)['data'];
    }

    /**
     * Get voters for the configured delegate.
     *
     * @return array
     */
    public function voters(): array
    {
        return $this->get('delegates/'.config('delegate.username').'/voters')['data'];
    }

    /**
     * Get information about the configured delegate.
     *
     * @return array
     */
    public function delegate(): array
    {
        return $this->get('delegates/'.config('delegate.username'))['data'];
    }

    /**
     * Get a list of well performing peers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function peers(): Collection
    {
        $peers = $this->get('peers')['data'];
        $peers = collect($peers)->sortByDesc('height')->sortBy('latency');

        return $peers->filter(function ($peer) {
            return array_get($peer, 'latency', 1000) <= 300;
        })->reject(function ($peer) {
            return 200 !== array_get($peer, 'status', 500);
        });
    }

    /**
     * [broadcast description].
     *
     * @param string $uri
     * @param array  $transaction
     */
    public function broadcast(string $uri, array $transaction): array
    {
        return $this->post('transactions', [
            'transactions' => [$transaction],
        ]);
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
        $response = $this->client->post($path, compact('json'));

        return json_decode($response->getBody(), true);
    }
}
