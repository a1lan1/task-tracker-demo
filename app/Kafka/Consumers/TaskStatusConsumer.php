<?php

declare(strict_types=1);

namespace App\Kafka\Consumers;

use App\Kafka\Handlers\TaskStatusUpdatedHandler;
use Junges\Kafka\Contracts\MessageConsumer;
use Junges\Kafka\Facades\Kafka;

class TaskStatusConsumer
{
    public function __invoke(): MessageConsumer
    {
        return Kafka::consumer(['tasks.status.updated'])
            ->withConsumerGroupId('task-status-consumers')
            ->withOptions(['client.id' => 'task-tracker-app'])
            ->withHandler(new TaskStatusUpdatedHandler)
            ->build();
    }
}
