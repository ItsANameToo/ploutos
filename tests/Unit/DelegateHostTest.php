<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DelegateHostTest extends TestCase
{
    public static function hosts(): array
    {
        return [
            'ip and port with trailing slash'    => ['http://1.2.3.4:4003/api/', 'http://1.2.3.4:4003/api/'],
            'ip and port without trailing slash' => ['http://1.2.3.4:4003/api', 'http://1.2.3.4:4003/api/'],
            'ip and port without scheme'         => ['1.2.3.4:4003/api', 'http://1.2.3.4:4003/api/'],
            'url with path'                      => ['https://api.example.com/api', 'https://api.example.com/api/'],
            'url without path'                   => ['https://api.example.com', 'https://api.example.com/'],
            'url with surrounding whitespace'    => [' https://api.example.com/api/ ', 'https://api.example.com/api/'],
        ];
    }

    #[DataProvider('hosts')]
    public function test_it_normalizes_the_delegate_host(string $host, string $expected): void
    {
        config(['delegate.host' => $host]);

        $this->assertSame($expected, delegate_host());
    }
}
