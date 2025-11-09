<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Kafka\Consumers\TaskStatusConsumer;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Exceptions\ConsumerException;

class KafkaTasksConsumer extends Command
{
    protected $signature = 'kafka:tasks-consumer';

    protected $description = 'Consume tasks.status.updated topic and broadcast updates via Soketi';

    /**
     * @throws ConsumerException
     * @throws Exception
     */
    public function handle(TaskStatusConsumer $consumer): int
    {
        $this->info('Starting Kafka consumer for tasks.status.updated...');

        $consumer()->consume();

        return self::SUCCESS;
    }
}
