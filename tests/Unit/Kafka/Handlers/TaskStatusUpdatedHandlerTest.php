<?php

declare(strict_types=1);

use App\DTO\TaskStatusUpdatedData;
use App\Enums\TaskStatus;
use App\Events\TaskStatusUpdatedBroadcast;
use App\Kafka\Handlers\TaskStatusUpdatedHandler;
use Illuminate\Support\Facades\Event;
use Junges\Kafka\Message\ConsumedMessage;

beforeEach(function (): void {
    Event::fake();
});

it('broadcasts TaskStatusUpdatedBroadcast with correct DTO from ConsumedMessage payload', function (): void {
    $payload = [
        'task_id' => 44,
        'project_id' => 7,
        'old_status' => TaskStatus::InProgress->value,
        'new_status' => TaskStatus::Done->value,
    ];

    $message = $this->createMock(ConsumedMessage::class);
    $message->method('getBody')->willReturn($payload);

    (new TaskStatusUpdatedHandler)->__invoke($message);

    Event::assertDispatched(TaskStatusUpdatedBroadcast::class, function (TaskStatusUpdatedBroadcast $event) use ($payload): bool {
        $data = $event->data;

        return $data instanceof TaskStatusUpdatedData
            && $data->task_id === $payload['task_id']
            && $data->project_id === $payload['project_id']
            && $data->old_status === TaskStatus::from($payload['old_status'])
            && $data->new_status === TaskStatus::from($payload['new_status']);
    });
});
