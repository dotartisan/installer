<?php

namespace Dotartisan\Installer;

class Installer
{
    public function config($key, $default)
    {
        return config($key, $default);
    }
}
