<?php

declare(strict_types=1);

namespace App\Events;

use App\DTO\TaskCommentEventData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

class TaskCommentCreated
{
    use Dispatchable;
    use SerializesAndRestoresModelIdentifiers;

    public function __construct(public TaskCommentEventData $data) {}
}
