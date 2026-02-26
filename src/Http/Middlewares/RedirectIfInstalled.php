<?php

namespace Dotartisan\Installer\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('installer.installed')) {
            return redirect(config('installer.redirect_after_install', '/'));
        }

        return $next($request);
    }
}
