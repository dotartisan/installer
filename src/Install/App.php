<?php

namespace Dotartisan\Installer\Install;

use Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class App
{
    public function setup($data)
    {
        $this->generateAppKey();
        $this->setAppSettings($data);
        $this->createStorageFolder();
        $this->setEnvVariables($data);
        $this->optimizeApp();
    }

    private function generateAppKey()
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    private function setEnvVariables($data)
    {
        $env = DotenvEditor::load();

        $facebook_redirect = secure_url(URL::route('social.login.callback', ['provider' => 'facebook'], false));
        $google_redirect = secure_url(URL::route('social.login.callback', ['provider' => 'google'], false));

        $env->setKey('APP_URL', url('/'));
        $env->setKey('APP_NAME', $data['app_name']);
        $env->setKey('APP_ENV', 'production');
        $env->setKey('APP_DEBUG', 'true');
        $env->setKey('DEBUGBAR_ENABLED', 'false');
        $env->setKey('MAIL_MAILER', 'mail');
        $env->setKey('MAIL_FROM_NAME', $data['app_name']);
        $env->setKey('MAIL_FROM_ADDRESS', $data['app_email']);
        $env->setKey('FACEBOOK_URL', $facebook_redirect);
        $env->setKey('GOOGLE_URL', $google_redirect);

        $env->save();
    }

    private function setAppSettings($data)
    {
        Setting::set('app_url', url('/'));
        Setting::set('app_name', $data['app_name']);
        Setting::set('meta_title', $data['app_name']);
        Setting::set('website_email', $data['app_email']);
        if (Session::has('purchase_code')) {
            Setting::set('purchase_code', Session::get('purchase_code'));
        }

        Setting::save();
    }

    private function createStorageFolder()
    {
        Artisan::call('storage:link');
    }

    private function optimizeApp()
    {
        Artisan::call('optimize');
        Cache::flush();
    }
}
