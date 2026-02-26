<?php

namespace Dotartisan\Installer\Services\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;

abstract class AbstractInstallService implements InstallServiceContract
{
    public function minPhpVersion(): string
    {
        return '8.0';
    }

    public function extraExtensions(): array
    {
        return [];
    }
    public function extraDirectories(): array
    {
        return [];
    }

    public function beforeRequirementsCheck(): void {}
    public function afterRequirementsCheck(bool $satisfied, array $extensions, array $directories): void {}

    public function beforePurchaseRedirect(): void {}
    public function afterPurchaseStored(string $code): void {}

    public function beforeDatabaseSetup(array $db): array
    {
        return $db;
    }
    public function afterDatabaseEnvWritten(array $db): void {}

    public function beforeMigrateAndSeed(): void {}
    public function afterMigrateAndSeed(): void {}

    public function extraDatabaseCommands(): array
    {
        return [];
    }

    public function beforeAdminSetup(array $admin): array
    {
        return $admin;
    }
    public function afterAdminSetup(array $admin): void {}

    public function beforeAppSetup(array $website): array
    {
        return $website;
    }
    public function envKeys(array $website): array
    {
        return [];
    }
    public function afterEnvWritten(array $website): void {}

    public function beforeFinish(): void {}
    public function afterFinish(): void {}

    public function beforeOptimize(): void {}
    public function afterOptimize(): void {}
}
