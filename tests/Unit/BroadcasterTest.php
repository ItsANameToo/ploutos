<?php

namespace Tests\Unit;

use App\Services\Ark\Broadcaster;
use App\Services\Ark\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use ReflectionProperty;
use Tests\TestCase;

class BroadcasterTest extends TestCase
{
    private array $requests = [];

    public function test_it_broadcasts_to_the_configured_host(): void
    {
        config(['delegate.host' => 'https://api.example.com/api']);

        $this->broadcaster([$this->jsonResponse([])])->broadcast(['id' => 'abc']);

        $this->assertSame(['POST https://api.example.com/api/transactions'], $this->sentRequests());
    }

    public function test_it_spreads_to_the_host_and_the_api_of_healthy_peers(): void
    {
        config(['delegate.host' => 'https://api.example.com/api']);

        $peers = $this->jsonResponse(['data' => [
            ['ip' => '1.1.1.1', 'port' => 4001, 'latency' => 10, 'ports' => ['@arkecosystem/core-api' => 4003]],
            ['ip' => '2.2.2.2', 'port' => 4001, 'latency' => 20, 'ports' => ['@arkecosystem/core-api' => -1]],
            ['ip' => '3.3.3.3', 'port' => 4001, 'latency' => 30],
            ['ip' => '4.4.4.4', 'port' => 4001, 'latency' => 900, 'ports' => ['@arkecosystem/core-api' => 4003]],
            ['ip' => '5.5.5.5', 'port' => 4001, 'latency' => 40, 'status' => 500, 'ports' => ['@arkecosystem/core-api' => 4003]],
            ['ip' => '6.6.6.6', 'port' => 4002, 'latency' => 50, 'ports' => ['@arkecosystem/core-webhooks' => 4004, '@arkecosystem/core-api' => 4103]],
        ]]);

        $this->broadcaster([$peers, $this->jsonResponse([]), new Response(422), $this->jsonResponse([])])->spread(['id' => 'abc']);

        $this->assertSame([
            'GET https://api.example.com/api/peers',
            'POST https://api.example.com/api/transactions',
            'POST http://1.1.1.1:4003/api/transactions',
            'POST http://6.6.6.6:4103/api/transactions',
        ], $this->sentRequests());
    }

    private function broadcaster(array $responses): Broadcaster
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->requests));

        $client = new Client();
        (new ReflectionProperty($client, 'client'))->setValue($client, new GuzzleClient([
            'base_uri' => delegate_host(),
            'handler'  => $stack,
        ]));

        return new Broadcaster($client);
    }

    private function jsonResponse(array $body): Response
    {
        return new Response(200, [], json_encode($body));
    }

    private function sentRequests(): array
    {
        return array_map(
            fn (array $entry) => $entry['request']->getMethod().' '.$entry['request']->getUri(),
            $this->requests
        );
    }
}
