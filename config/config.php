<?php

return [
    'installed' => env('APP_INSTALLED', false),
    'source' => env('APP_SOURCE', 'envato'),
    'redirect_after_install' => '/',

    'installer_service' => \Dotartisan\Installer\Services\DefaultInstallerService::class,

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Additional middleware that should be applied to installer routes.
    | These will be merged with the package default middleware.
    |
    */
    'route_middleware' => [
        \Dotartisan\Installer\Http\Middlewares\RedirectIfInstalled::class,
    ],

    /**
     * Do not change or remove this like.
     */
    'encrypt_key' => 'base64:NHd6MXFZSjhnREtHNlNqYjN5bFBtclRVQ3dzcDlpNFU=',
];
