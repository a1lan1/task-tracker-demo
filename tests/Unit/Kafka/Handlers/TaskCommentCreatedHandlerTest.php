<?php

declare(strict_types=1);

use App\DTO\UserData;
use App\Events\TaskCommentCreatedBroadcast;
use App\Kafka\Handlers\TaskCommentCreatedHandler;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Event;
use Junges\Kafka\Message\ConsumedMessage;

beforeEach(function (): void {
    Event::fake();
});

it('broadcasts TaskCommentCreatedBroadcast with correct DTO from ConsumedMessage payload', function (): void {
    $payload = [
        'id' => 10,
        'task_id' => 77,
        'project_id' => 5,
        'user_id' => 3,
        'user' => [
            'id' => 3,
            'name' => 'Jane',
            'avatar' => 'https://example.com/a.jpg',
        ],
        'body' => 'Hello world',
        'created_at' => '2025-01-02T03:04:05+00:00',
    ];

    $message = $this->createMock(ConsumedMessage::class);
    $message->method('getBody')->willReturn($payload);

    (new TaskCommentCreatedHandler)->__invoke($message);

    Event::assertDispatched(TaskCommentCreatedBroadcast::class, function (TaskCommentCreatedBroadcast $event) use ($payload): bool {
        $data = $event->data;

        return $data->id === $payload['id']
            && $data->task_id === $payload['task_id']
            && $data->project_id === $payload['project_id']
            && $data->user_id === $payload['user_id']
            && $data->user instanceof UserData
            && $data->user->id === $payload['user']['id']
            && $data->user->name === $payload['user']['name']
            && $data->user->avatar === $payload['user']['avatar']
            && $data->body === $payload['body']
            && $data->created_at->equalTo(CarbonImmutable::parse($payload['created_at']));
    });
});
