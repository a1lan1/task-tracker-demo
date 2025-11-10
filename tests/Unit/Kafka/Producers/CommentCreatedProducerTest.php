<?php

declare(strict_types=1);

use App\DTO\TaskCommentEventData;
use App\DTO\UserData;
use App\Kafka\Producers\CommentCreatedProducer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Config;
use Junges\Kafka\Contracts\MessageProducer;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

it('publishes comment created event to the correct topic with DTO payload', function (): void {
    $brokers = 'kafka:9092';
    Config::set('kafka.brokers', $brokers);

    $producer = \Mockery::mock(MessageProducer::class);
    $producer->shouldReceive('onTopic')->once()->with('comments.created')->andReturnSelf();
    $producer->shouldReceive('withMessage')->once()->with(\Mockery::type(Message::class))->andReturnSelf();
    $producer->shouldReceive('send')->once()->andReturnTrue();

    Kafka::shouldReceive('publish')->once()->with($brokers)->andReturn($producer);

    $dto = new TaskCommentEventData(
        id: 1,
        task_id: 2,
        project_id: 3,
        user_id: 4,
        user: new UserData(id: 4, name: 'John', avatar: 'a'),
        body: 'Hi',
        created_at: CarbonImmutable::parse('2025-01-01T00:00:00Z'),
    );

    (new CommentCreatedProducer)->publish($dto);
});
