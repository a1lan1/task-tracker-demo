<?php

declare(strict_types=1);

namespace App\Kafka\Handlers;

use App\DTO\TaskCommentEventData;
use App\DTO\UserData;
use App\Events\TaskCommentCreatedBroadcast;
use Carbon\CarbonImmutable;
use Junges\Kafka\Message\ConsumedMessage;

class TaskCommentCreatedHandler
{
    public function __invoke(ConsumedMessage $message): void
    {
        $payload = $message->getBody();

        // dev visibility
        @fwrite(STDOUT, '[comments.created] '.json_encode($payload).PHP_EOL);

        broadcast(new TaskCommentCreatedBroadcast(
            new TaskCommentEventData(
                id: $payload['id'],
                task_id: $payload['task_id'],
                project_id: $payload['project_id'],
                user_id: $payload['user_id'],
                user: UserData::from($payload['user']),
                body: $payload['body'],
                created_at: CarbonImmutable::parse($payload['created_at']),
            )
        ));
    }
}
