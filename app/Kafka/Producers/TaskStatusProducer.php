<?php

declare(strict_types=1);

namespace App\Kafka\Producers;

use App\DTO\TaskStatusUpdatedData;
use Exception;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class TaskStatusProducer
{
    /**
     * @throws Exception
     */
    public function publish(TaskStatusUpdatedData $data): void
    {
        Kafka::publish(config('kafka.brokers'))
            ->onTopic('tasks.status.updated')
            ->withMessage(new Message(body: $data->toArray()))
            ->send();
    }
}
