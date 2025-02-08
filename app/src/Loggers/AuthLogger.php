<?php

namespace App\Loggers;

class AuthLogger extends AbstractLogger
{
    public function __construct(string $path)
    {
        parent::__construct('user_logger', $path);
    }

    public function logInfo(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function logWarning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
