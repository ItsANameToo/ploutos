<?php

namespace App\Services\Ark;

use Exception;

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
        $this->client->broadcast(config('delegate.host'), $transfer);
    }

    /**
     * Broadcast the given transfer to many peers.
     *
     * @param array $transfer
     */
    public function spread(array $transfer): void
    {
        $this->client->peers()->take(5)->each(function ($peer) use ($transfer) {
            try {
                $this->client->broadcast(
                    sprintf('http://%s:%s', $peer['ip'], $peer['port']),
                    $transfer
                );
            } catch (Exception $e) {
                // an error usually means that the transaction is already confirmed
            }
        });
    }
}
