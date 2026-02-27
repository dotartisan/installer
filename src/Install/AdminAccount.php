<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;

class AdminAccount
{
    public function __construct(protected InstallServiceContract $service) {}

    public function setup(array $admin): void
    {
        $admin = $this->service->beforeAdminSetup($admin);
        $this->service->createAdmin($admin);
        $this->service->afterAdminSetup($admin);
    }
}
