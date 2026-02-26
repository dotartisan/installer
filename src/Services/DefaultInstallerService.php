<?php

namespace Dotartisan\Installer\Services;

use Dotartisan\Installer\Contracts\InstallerServiceContract;

class DefaultInstallerService implements InstallerServiceContract
{
    public function product(): string
    {
        return 'unknown-item';
    }
   
    public function version(): string
    {
        return '0.0.0';
    }

    public function beforeRunUpdate(): void {}
    public function afterRunUpdate(bool $success, array $context = []): void {}

    public function beforeOptimize(): void {}
    public function afterOptimize(): void {}

    public function beforeMigrate(): void {}
    public function afterMigrate(): void {}

    public function beforeRebuildTheme(): void {}
    public function afterRebuildTheme(): void {}

    public function beforeUpdateAppVersion(): void {}
    public function afterUpdateAppVersion(): void {}

    public function beforeDisableUpdateAlert(): void {}
    public function afterDisableUpdateAlert(): void {}
}
