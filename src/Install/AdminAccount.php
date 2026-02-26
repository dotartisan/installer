<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;

class AdminAccount
{
    public function __construct(protected InstallServiceContract $service) {}

    public function setup(array $admin): void
    {
        $admin = $this->service->beforeAdminSetup($admin);

        // Item decides how to create admin user (model, roles, etc.)
        $this->service->createAdmin($admin);

        $this->service->afterAdminSetup($admin);
    }
}
