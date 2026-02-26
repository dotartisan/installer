<?php

namespace Dotartisan\Installer\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'dotartisan:install {--force : Overwrite existing files}';
    protected $description = 'Install Dotartisan Installer (generates Install/Update service classes in the host app).';

    public function handle(): int
    {
        $fs = new Filesystem();

        $targetDir = app_path('Helpers/Classes');
        $fs->ensureDirectoryExists($targetDir);

        $files = [
            [
                'stub'   => __DIR__ . '/../../stubs/InstallerInstallService.stub',
                'target' => $targetDir . DIRECTORY_SEPARATOR . 'InstallerInstallService.php',
                'label'  => 'InstallerInstallService',
            ],
            [
                'stub'   => __DIR__ . '/../../stubs/InstallerUpdateService.stub',
                'target' => $targetDir . DIRECTORY_SEPARATOR . 'InstallerUpdateService.php',
                'label'  => 'InstallerUpdateService',
            ],
        ];

        foreach ($files as $file) {
            if (! $fs->exists($file['stub'])) {
                $this->error("Stub not found: {$file['stub']}");
                return self::FAILURE;
            }

            if ($fs->exists($file['target']) && ! $this->option('force')) {
                $this->warn("{$file['label']} already exists: {$file['target']}");
                $this->line("Use --force to overwrite.");
                continue;
            }

            $fs->put($file['target'], $fs->get($file['stub']));
            $this->info("✅ Generated {$file['label']}: {$file['target']}");
        }

        return self::SUCCESS;
    }
}
