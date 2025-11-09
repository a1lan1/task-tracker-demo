<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCommentCreated;
use App\Kafka\Producers\CommentCreatedProducer;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class SendTaskCommentToKafkaListener implements ShouldQueue
{
    public function __construct(private CommentCreatedProducer $producer) {}

    /**
     * @throws Exception
     */
    public function handle(TaskCommentCreated $event): void
    {
        $this->producer->publish($event->data);
    }
}
