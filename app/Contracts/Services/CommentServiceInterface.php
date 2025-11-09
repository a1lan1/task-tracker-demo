<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Illuminate\Pagination\LengthAwarePaginator;

interface CommentServiceInterface
{
    public function getTaskComments(int $taskId): LengthAwarePaginator;
}
