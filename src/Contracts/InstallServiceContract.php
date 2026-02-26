<?php

namespace Dotartisan\Installer\Contracts;

interface InstallServiceContract
{
    /**
     * ITEM METADATA (install needs these for purchase provider + display)
     */
    public function product(): string;
    public function version(): string;

    /**
     * STEP 1: REQUIREMENTS
     */
    public function minPhpVersion(): string;

    /** @return array<string, bool|callable> */
    public function extraExtensions(): array;

    /** @return array<string, bool|callable> */
    public function extraDirectories(): array;

    public function beforeRequirementsCheck(): void;
    public function afterRequirementsCheck(bool $satisfied, array $extensions, array $directories): void;

    /**
     * STEP 2: PURCHASE
     */
    public function beforePurchaseRedirect(): void;
    public function afterPurchaseStored(string $code): void;

    /**
     * STEP 3: DATABASE + ADMIN + APP SETUP
     * Allow modifying input arrays before use.
     */
    public function beforeDatabaseSetup(array $db): array;
    public function afterDatabaseEnvWritten(array $db): void;

    public function beforeMigrateAndSeed(): void;
    public function afterMigrateAndSeed(): void;

    /** @return array<int, array{command:string, params?:array}> */
    public function extraDatabaseCommands(): array;

    public function beforeAdminSetup(array $admin): array;
    public function createAdmin(array $admin): void;
    public function afterAdminSetup(array $admin): void;

    public function beforeAppSetup(array $website): array;

    /** @return array<string, scalar|null> */
    public function envKeys(array $website): array;

    public function afterEnvWritten(array $website): void;

    /**
     * STEP 4: FINISH
     */
    public function beforeFinish(): void;
    public function afterFinish(): void;

    /**
     * GENERAL
     */
    public function beforeOptimize(): void;
    public function afterOptimize(): void;
}
