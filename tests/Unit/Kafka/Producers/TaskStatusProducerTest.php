<?php

declare(strict_types=1);

use App\DTO\TaskStatusUpdatedData;
use App\Enums\TaskStatus;
use App\Kafka\Producers\TaskStatusProducer;
use Illuminate\Support\Facades\Config;
use Junges\Kafka\Contracts\MessageProducer;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

it('publishes task status updated event to the correct topic with DTO payload', function (): void {
    $brokers = 'kafka:9092';
    Config::set('kafka.brokers', $brokers);

    $producer = \Mockery::mock(MessageProducer::class);
    $producer->shouldReceive('onTopic')->once()->with('tasks.status.updated')->andReturnSelf();
    $producer->shouldReceive('withMessage')->once()->with(\Mockery::type(Message::class))->andReturnSelf();
    $producer->shouldReceive('send')->once()->andReturnTrue();

    Kafka::shouldReceive('publish')->once()->with($brokers)->andReturn($producer);

    $dto = new TaskStatusUpdatedData(
        task_id: 101,
        project_id: 9,
        old_status: TaskStatus::InProgress,
        new_status: TaskStatus::Done,
    );

    (new TaskStatusProducer)->publish($dto);
});
