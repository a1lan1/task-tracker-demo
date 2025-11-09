<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskStatusUpdated;
use App\Kafka\Producers\TaskStatusProducer;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class SendTaskStatusToKafkaListener implements ShouldQueue
{
    public function __construct(private TaskStatusProducer $producer) {}

    /**
     * @throws Exception
     */
    public function handle(TaskStatusUpdated $event): void
    {
        $this->producer->publish($event->data);
    }
}
