<?php

declare(strict_types=1);

namespace App\Events;

use App\DTO\TaskStatusUpdatedData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

class TaskStatusUpdatedBroadcast implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesAndRestoresModelIdentifiers;

    public function __construct(public TaskStatusUpdatedData $data) {}

    public function broadcastAs(): string
    {
        return 'task-status-updated';
    }

    public function broadcastWith(): array
    {
        return $this->data->toArray();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project.'.$this->data->project_id),
        ];
    }
}
