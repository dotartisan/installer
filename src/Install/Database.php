<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class Database
{
    public function __construct(protected InstallServiceContract $service) {}

    public function setup(array $db): void
    {
        $db = $this->service->beforeDatabaseSetup($db);

        $this->checkConnection($db);
        $this->writeEnv($db);

        $this->service->afterDatabaseEnvWritten($db);

        $this->migrateAndSeed();

        foreach ($this->service->extraDatabaseCommands() as $cmd) {
            $command = $cmd['command'] ?? null;
            $params  = $cmd['params'] ?? [];

            if ($command) {
                Artisan::call($command, $params);
            }
        }
    }

    private function checkConnection(array $db): void
    {
        $this->applyRuntimeConfig($db);

        DB::connection('mysql')->reconnect();
        DB::connection('mysql')->getPdo();
    }

    private function applyRuntimeConfig(array $db): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $db['host'] ?? '127.0.0.1',
            'database.connections.mysql.port' => $db['port'] ?? '3306',
            'database.connections.mysql.database' => $db['database'] ?? '',
            'database.connections.mysql.username' => $db['username'] ?? '',
            'database.connections.mysql.password' => $db['password'] ?? '',
        ]);
    }

    private function writeEnv(array $db): void
    {
        $env = DotenvEditor::load();

        $env->setKey('DB_HOST', (string) ($db['host'] ?? '127.0.0.1'));
        $env->setKey('DB_PORT', (string) ($db['port'] ?? '3306'));
        $env->setKey('DB_DATABASE', (string) ($db['database'] ?? ''));
        $env->setKey('DB_USERNAME', (string) ($db['username'] ?? ''));
        $env->setKey('DB_PASSWORD', (string) ($db['password'] ?? ''));

        $env->save();
    }

    private function migrateAndSeed(): void
    {
        $this->service->beforeMigrateAndSeed();

        Artisan::call('migrate', ['--seed' => true, '--force' => true]);

        $this->service->afterMigrateAndSeed();
    }
}
