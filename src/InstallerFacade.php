<?php

namespace Dotartisan\Installer;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dotartisan\Installer\Skeleton\SkeletonClass
 */
class InstallerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'installer';
    }
}
