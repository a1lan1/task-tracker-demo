<?php

declare(strict_types=1);

namespace App\Kafka\Handlers;

use App\DTO\TaskStatusUpdatedData;
use App\Enums\TaskStatus;
use App\Events\TaskStatusUpdatedBroadcast;
use Junges\Kafka\Message\ConsumedMessage;

class TaskStatusUpdatedHandler
{
    public function __invoke(ConsumedMessage $message): void
    {
        $payload = $message->getBody();

        // dev visibility
        @fwrite(STDOUT, '[tasks.status.updated] '.json_encode($payload).PHP_EOL);

        broadcast(new TaskStatusUpdatedBroadcast(
            new TaskStatusUpdatedData(
                task_id: $payload['task_id'],
                project_id: $payload['project_id'],
                old_status: TaskStatus::from($payload['old_status']),
                new_status: TaskStatus::from($payload['new_status']),
            )
        ));
    }
}
