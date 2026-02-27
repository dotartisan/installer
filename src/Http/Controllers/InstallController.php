<?php

namespace Dotartisan\Installer\Http\Controllers;

use Dotartisan\Installer\Classes\ArtisanApi;
use Dotartisan\Installer\Http\Requests\InstallRequest;
use Dotartisan\Installer\Install\AdminAccount;
use Dotartisan\Installer\Install\App;
use Dotartisan\Installer\Install\Database;
use Dotartisan\Installer\Install\Requirement;
use Dotartisan\Installer\Install\VerifyPurchase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class InstallController extends Controller
{
    public function preInstallation(Requirement $requirement)
    {
        $route = Route::current()->getName();

        return view('installer::pre_installation', compact('requirement', 'route'));
    }

    public function verifyPurchase(Requirement $requirement, VerifyPurchase $verifyPurchase)
    {
        $route = Route::current()->getName();

        if (!$requirement->satisfied()) {
            return redirect('/install/pre-installation');
        }

        return view('installer::purchase', compact('requirement', 'verifyPurchase', 'route'));
    }

    public function registerPurchase(Request $request, Requirement $requirement, ArtisanApi $artisan)
    {
        try {
            if (!$requirement->satisfied()) {
                return redirect('/install/pre-installation');
            }


            $response = $artisan->register($request);
            $data = $response->getData();

            return back()->withInput()->withSuccess($data->message);
        } catch (Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function redirectPurchase(VerifyPurchase $verifyPurchase)
    {
        return $verifyPurchase->login();
    }

    public function returnPurchase(VerifyPurchase $verifyPurchase)
    {
        return $verifyPurchase->authorize();
    }

    public function getConfiguration(Requirement $requirement, VerifyPurchase $verifyPurchase)
    {
        $route = Route::current()->getName();

        if (!$requirement->satisfied()) {
            return redirect('/install/pre-installation');
        }

        if (!$verifyPurchase->satisfied()) {
            return redirect('/install/verify');
        }

        return view('installer::configuration', compact('requirement', 'verifyPurchase', 'route'));
    }

    public function postConfiguration(
        InstallRequest $request,
        Database $database,
        AdminAccount $admin,
        App $app
    ) {
        @set_time_limit(0);

        try {
            $database->setup($request->db);
            $admin->setup($request->admin);
            $app->setup($request->website);
            $this->optimizeApp();
        } catch (Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect('/install/complete');
    }

    public function complete(Requirement $requirement, VerifyPurchase $verifyPurchase)
    {
        $route = Route::current()->getName();

        if (config('installer.installed')) {
            return redirect(config('installer.redirect_after_install', '/'));
        }

        if (!$requirement->satisfied()) {
            return redirect('/install/pre-installation');
        }

        if (!$verifyPurchase->satisfied()) {
            return redirect('/install/verify');
        }

        DotenvEditor::setKey('APP_INSTALLED', 'true')->save();

        $this->optimizeApp();

        return view('installer::complete', compact('route'));
    }

    private function optimizeApp()
    {
        Cache::flush();
        Artisan::call('optimize');
    }
}
