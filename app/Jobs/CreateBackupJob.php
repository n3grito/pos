<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateBackupJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        try {
            $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.database')),
                escapeshellarg($path)
            );

            $output = null;
            $resultCode = null;
            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                ActivityLog::create([
                    'action' => 'backup_created',
                    'severity' => 'info',
                    'notable' => false,
                    'description' => 'Backup automático creado: ' . $filename,
                ]);
            } else {
                throw new \RuntimeException('mysqldump failed with code ' . $resultCode);
            }
        } catch (\Throwable $e) {
            ActivityLog::create([
                'action' => 'backup_failed',
                'severity' => 'critical',
                'notable' => true,
                'description' => 'Error en backup automático: ' . $e->getMessage(),
            ]);
        }
    }
}
