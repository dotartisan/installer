<?php

namespace Dotartisan\Installer\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class RedirectToInstallMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('installer.installed') && !$request->is('install/*')) {
            return redirect()->route('preinstall');
        }

        return $next($request);
    }
}
