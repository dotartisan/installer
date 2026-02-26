<?php

namespace Dotartisan\Installer\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'dotartisan:install {--force : Overwrite existing files}';
    protected $description = 'Install Dotartisan Installer (publishes config and generates InstallerService stub).';

    public function handle(): int
    {
        $fs = new Filesystem();

        // Publish config
        $this->callSilent('vendor:publish', [
            '--tag' => 'dotartisan-installer-config',
            '--force' => (bool) $this->option('force'),
        ]);

        $stub = __DIR__ . '/../../stubs/UpdateService.stub';
        $targetDir = app_path('Helpers/Classes');
        $target = $targetDir . DIRECTORY_SEPARATOR . 'UpdateService.php';

        if (! $fs->exists($stub)) {
            $this->error("Stub not found: {$stub}");
            return self::FAILURE;
        }

        if ($fs->exists($target) && ! $this->option('force')) {
            $this->warn("File exists: {$target}");
            $this->line('Run with --force to overwrite.');
            return self::SUCCESS;
        }

        $fs->ensureDirectoryExists($targetDir);
        $fs->put($target, $fs->get($stub));

        $this->info('✅ Update Service created.');
        $this->line("Path: {$target}");

        return self::SUCCESS;
    }
}
