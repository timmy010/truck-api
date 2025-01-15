<?php

use PHPUnit\Framework\TestCase;
use App\Services\RedisService;

class RedisServiceTest extends TestCase
{
    private $redisService;

    protected function setUp(): void
    {
        $this->redisService = new RedisService();
    }

    public function testSetValueAndGetValue()
    {
        $key = 'test_key';
        $value = 'test_value';

        $this->redisService->setValue($key, $value);
        $retrievedValue = $this->redisService->getValue($key);

        $this->assertEquals($value, $retrievedValue, "The value retrieved from Redis should match the value set.");
    }
}
