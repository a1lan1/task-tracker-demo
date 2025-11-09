<?php

declare(strict_types=1);

namespace App\Kafka\Producers;

use App\DTO\TaskCommentEventData;
use Exception;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class CommentCreatedProducer
{
    /**
     * @throws Exception
     */
    public function publish(TaskCommentEventData $data): void
    {
        Kafka::publish(config('kafka.brokers'))
            ->onTopic('comments.created')
            ->withMessage(new Message(body: $data->toArray()))
            ->send();
    }
}
