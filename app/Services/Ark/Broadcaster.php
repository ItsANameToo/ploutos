<?php

namespace App\Services\Ark;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Broadcaster
{
    /**
     * The Ark client.
     *
     * @var \App\Services\Ark\Client
     */
    private $client;

    /**
     * Create a new Broadcaster instance.
     *
     * @param \App\Services\Ark\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Broadcast the given transfer to the main peer.
     *
     * @param array $transfer
     */
    public function broadcast(array $transfer): void
    {
        $this->client->broadcast(delegate_host(), $transfer);
    }

    /**
     * Broadcast the given transfer to the main peer and many other peers.
     *
     * @param array $transfer
     */
    public function spread(array $transfer): void
    {
        $peers = $this->client->peers()
            ->map(fn (array $peer) => $this->apiUrl($peer))
            ->filter()
            ->take(5);

        $peers->prepend(delegate_host())->each(function (string $uri) use ($transfer) {
            try {
                $this->client->broadcast($uri, $transfer);
            } catch (Exception $e) {
                // an error usually means that the transaction is already confirmed
            }
        });
    }

    /**
     * Get the API URL of the given peer, or null if it has no public API.
     *
     * @param array $peer
     *
     * @return string|null
     */
    private function apiUrl(array $peer): ?string
    {
        $port = collect(Arr::get($peer, 'ports', []))
            ->first(fn ($port, $plugin) => Str::endsWith($plugin, '/core-api') && $port > 0);

        return $port ? sprintf('http://%s:%s/api/', $peer['ip'], $port) : null;
    }
}
