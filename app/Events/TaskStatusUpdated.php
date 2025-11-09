<?php

declare(strict_types=1);

namespace App\Events;

use App\DTO\TaskStatusUpdatedData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

class TaskStatusUpdated
{
    use Dispatchable;
    use SerializesAndRestoresModelIdentifiers;

    public function __construct(public TaskStatusUpdatedData $data) {}
}
