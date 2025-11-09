<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Kafka\Consumers\CommentsCreatedConsumer;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Exceptions\ConsumerException;

class KafkaCommentsConsumer extends Command
{
    protected $signature = 'kafka:comments-consumer';

    protected $description = 'Consume comments.created topic and broadcast updates via Soketi';

    /**
     * @throws ConsumerException
     * @throws Exception
     */
    public function handle(CommentsCreatedConsumer $consumer): int
    {
        $this->info('Starting Kafka consumer for comments.created...');

        $consumer()->consume();

        return self::SUCCESS;
    }
}
