<?php

namespace Dotartisan\Installer\Contracts;

interface InstallServiceContract
{
    /**
     * REQUIREMENTS STEP
     */
    public function minPhpVersion(): string;

    /** @return array<string, bool|callable> */
    public function extraExtensions(): array;

    /** @return array<string, bool|callable> */
    public function extraDirectories(): array;

    public function beforeRequirementsCheck(): void;
    public function afterRequirementsCheck(bool $satisfied, array $extensions, array $directories): void;

    /**
     * PURCHASE STEP
     */
    public function beforePurchaseRedirect(): void;
    public function afterPurchaseStored(string $code): void;

    /**
     * DATABASE STEP
     * Return modified input if needed.
     */
    public function beforeDatabaseSetup(array $db): array;
    public function afterDatabaseEnvWritten(array $db): void;

    public function beforeMigrateAndSeed(): void;
    public function afterMigrateAndSeed(): void;

    /**
     * Extra commands after migrate/seed.
     * @return array<int, array{command:string, params?:array}>
     */
    public function extraDatabaseCommands(): array;

    /**
     * ADMIN STEP
     * Return modified input if needed.
     */
    public function beforeAdminSetup(array $admin): array;

    /**
     * Item-specific admin creation (this is the main variation point).
     */
    public function createAdmin(array $admin): void;

    public function afterAdminSetup(array $admin): void;

    /**
     * APP/ENV STEP
     * Return modified input if needed.
     */
    public function beforeAppSetup(array $website): array;

    /**
     * Item-specific env keys to write.
     * @return array<string, scalar|null>
     */
    public function envKeys(array $website): array;

    public function afterEnvWritten(array $website): void;

    /**
     * GENERAL
     */
    public function beforeOptimize(): void;
    public function afterOptimize(): void;
}
