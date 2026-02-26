<?php

use Dotartisan\Installer\Support\EncrypterFactory;

if (! function_exists('installerEncrypter')) {
    /**
     * Get Installer Encrypter instance.
     *
     * @return \Illuminate\Encryption\Encrypter
     */
    function installerEncrypter()
    {
        return EncrypterFactory::make();
    }
}
