<?php

use PHPUnit\Framework\TestCase;
use App\Services\RabbitMQService;

class RabbitMQServiceTest extends TestCase
{
    private $rabbitMQService;

    protected function setUp(): void
    {
        $this->rabbitMQService = new RabbitMQService();
    }

    public function testPublishMessage()
    {
        $queue = 'test_queue';
        $message = 'Test message';

        // Публикуем сообщение в очередь
        $this->rabbitMQService->publishMessage($queue, $message);

        // Проверяем, что метод завершился без ошибок
        $this->assertTrue(true, "Message should be published to RabbitMQ without errors.");
    }
}
