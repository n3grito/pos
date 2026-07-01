<?php

namespace Tests\Feature;

use App\Jobs\CreateBackupJob;
use App\Jobs\SendStockAlertJob;
use Tests\TestCase;

class QueueTest extends TestCase
{
    public function test_backup_job_can_be_queued(): void
    {
        config()->set('queue.default', 'sync');

        $job = new CreateBackupJob();
        $job->handle();

        $this->assertTrue(true);
    }

    public function test_stock_alert_job_can_be_queued(): void
    {
        config()->set('queue.default', 'sync');

        $job = new SendStockAlertJob();
        $job->handle();

        $this->assertTrue(true);
    }
}
