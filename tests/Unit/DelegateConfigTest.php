<?php

namespace Tests\Unit;

use Tests\TestCase;

class DelegateConfigTest extends TestCase
{
    private array $keys = [];

    protected function tearDown(): void
    {
        foreach ($this->keys as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }

        parent::tearDown();
    }

    public function test_empty_values_fall_back_to_the_defaults(): void
    {
        $config = $this->loadConfig([
            'ARK_DELEGATE_FEE_TRANSFER'              => '',
            'ARK_DELEGATE_FEE_MULTIPAYMENT'          => '',
            'ARK_DELEGATE_SHARE_PERCENTAGE'          => '',
            'ARK_DELEGATE_THRESHOLD'                 => '',
            'ARK_DELEGATE_MULTIPAYMENT_COUNT'        => '',
            'ARK_DELEGATE_PERSONAL_SHARE_PERCENTAGE' => '',
            'ARK_DELEGATE_NETWORK'                   => '',
            'ARK_DELEGATE_BROADCAST_TYPE'            => '',
        ]);

        $this->assertSame(10000000, $config['fees']['transfer']);
        $this->assertSame(10000000, $config['fees']['multipayment']);
        $this->assertSame(90, $config['sharePercentage']);
        $this->assertSame(0.1, $config['threshold']);
        $this->assertSame(150, $config['multipaymentCount']);
        $this->assertSame(0, $config['personal']['sharePercentage']);
        $this->assertSame('mainnet', $config['network']);
        $this->assertSame('default', $config['broadcastType']);
    }

    public function test_missing_vendor_fields_become_empty_strings(): void
    {
        $config = $this->loadConfig([
            'ARK_DELEGATE_VENDOR_FIELD'          => null,
            'ARK_DELEGATE_PERSONAL_VENDOR_FIELD' => null,
        ]);

        $this->assertSame('', $config['vendorField']);
        $this->assertSame('', $config['personal']['vendorField']);
    }

    public function test_configured_values_are_kept(): void
    {
        $config = $this->loadConfig([
            'ARK_DELEGATE_FEE_MULTIPAYMENT' => '25000000',
            'ARK_DELEGATE_SHARE_PERCENTAGE' => '0',
            'ARK_DELEGATE_FEE_COVER'        => 'true',
        ]);

        $this->assertSame('25000000', $config['fees']['multipayment']);
        $this->assertSame('0', $config['sharePercentage']);
        $this->assertTrue($config['fees']['cover']);
    }

    private function loadConfig(array $values): array
    {
        foreach ($values as $key => $value) {
            $this->keys[] = $key;

            if (null === $value) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);
            } else {
                putenv("{$key}={$value}");
                $_ENV[$key] = $_SERVER[$key] = $value;
            }
        }

        return require base_path('config/delegate.php');
    }
}
