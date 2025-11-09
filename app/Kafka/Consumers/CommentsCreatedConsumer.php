<?php

declare(strict_types=1);

namespace App\Kafka\Consumers;

use App\Kafka\Handlers\TaskCommentCreatedHandler;
use Junges\Kafka\Contracts\MessageConsumer;
use Junges\Kafka\Facades\Kafka;

class CommentsCreatedConsumer
{
    public function __invoke(): MessageConsumer
    {
        return Kafka::consumer(['comments.created'])
            ->withConsumerGroupId('comments-consumers')
            ->withOptions(['client.id' => 'task-tracker-app'])
            ->withHandler(new TaskCommentCreatedHandler)
            ->build();
    }
}
