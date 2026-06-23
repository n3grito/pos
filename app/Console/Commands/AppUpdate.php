<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AppUpdate extends Command
{
    protected $signature = 'app:update';

    protected $description = 'Actualiza la aplicación a la última versión (migraciones, permisos, caché)';

    public function handle()
    {
        $this->info('Iniciando actualización de ' . config('app.name') . '...');
        $this->newLine();

        // 1. Run pending migrations
        $this->info('1. Ejecutando migraciones pendientes...');
        $exitCode = Artisan::call('migrate', ['--force' => true], $this->getOutput());
        if ($exitCode !== 0) {
            $this->error('Error al ejecutar migraciones.');
            return $exitCode;
        }
        $this->info('  Migraciones ejecutadas correctamente.');
        $this->newLine();

        // 2. Seed permissions
        $this->info('2. Sincronizando permisos...');
        $exitCode = Artisan::call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true], $this->getOutput());
        if ($exitCode !== 0) {
            $this->warn('  Permisos ya estaban actualizados o no se requirieron cambios.');
        } else {
            $this->info('  Permisos sincronizados correctamente.');
        }
        $this->newLine();

        // 3. Clear cache
        $this->info('3. Limpiando caché...');
        Artisan::call('view:clear', outputBuffer: $this->getOutput());
        Artisan::call('config:clear', outputBuffer: $this->getOutput());
        Artisan::call('cache:clear', outputBuffer: $this->getOutput());
        $this->info('  Caché limpiada correctamente.');
        $this->newLine();

        $this->info('¡Actualización completada exitosamente!');

        return Command::SUCCESS;
    }
}
