<?php

namespace Dotartisan\Installer\Contracts;

interface InstallerServiceContract
{
    // Item-specific values
    public function product(): string;
    public function version(): string;

    /**
     * Called once at the start/end of runUpdate orchestration.
     */
    public function beforeRunUpdate(): void;
    public function afterRunUpdate(bool $success, array $context = []): void;

    /**
     * Step hooks (called around each UpdatesManager action).
     */
    public function beforeOptimize(): void;
    public function afterOptimize(): void;

    public function beforeMigrate(): void;
    public function afterMigrate(): void;

    public function beforeRebuildTheme(): void;
    public function afterRebuildTheme(): void;

    public function beforeUpdateAppVersion(): void;
    public function afterUpdateAppVersion(): void;

    public function beforeDisableUpdateAlert(): void;
    public function afterDisableUpdateAlert(): void;
}
