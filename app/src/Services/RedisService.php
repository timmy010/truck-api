<?php

namespace App\Services;

use Predis\Client;

class RedisService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
        ]);
    }

    public function setValue($key, $value)
    {
        $this->client->set($key, $value);
    }

    public function getValue($key)
    {
        return $this->client->get($key);
    }
}
