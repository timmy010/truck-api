<?php

namespace App\Loggers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class AbstractLogger
{
    protected Logger $logger;

    public function __construct(string $name, string $path)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler($path));
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    abstract public function logInfo(string $message, array $context = []);
    abstract public function logWarning(string $message, array $context = []);
    abstract public function logError(string $message, array $context = []);
}
