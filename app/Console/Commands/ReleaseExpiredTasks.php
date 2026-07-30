<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

/**
 * Returns tasks whose 45-minute hold has lapsed.
 *
 * Scheduled every five minutes in bootstrap/app.php. Locally, run the
 * scheduler with `php artisan schedule:work`; in production point cron at
 * `php artisan schedule:run` every minute.
 */
class ReleaseExpiredTasks extends Command
{
    protected $signature = 'tasks:release-expired';

    protected $description = 'Return claimed tasks whose hold has expired to the open queue';

    public function handle(TaskService $tasks): int
    {
        $released = $tasks->releaseExpired();

        $this->components->info(
            $released === 0
                ? 'No holds had expired.'
                : sprintf('Returned %d task%s to the queue.', $released, $released === 1 ? '' : 's')
        );

        return self::SUCCESS;
    }
}
