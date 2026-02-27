<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Setting;

class App
{
    public function __construct(protected InstallServiceContract $service) {}

    public function setup(array $website): void
    {
        $website = $this->service->beforeAppSetup($website);

        $this->generateAppKey();
        $this->storageLink();
        $this->writeEnvAndSettings($website);
        $this->optimize();

        $this->service->afterEnvWritten($website);
    }

    private function generateAppKey(): void
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    private function storageLink(): void
    {
        Artisan::call('storage:link');
    }

    private function writeEnvAndSettings(array $website): void
    {
        $env = DotenvEditor::load();
        $env->setKey('APP_URL', url('/'));
        $env->setKey('APP_NAME', $website['app_name']);
        $env->setKey('APP_ENV', 'production');
        $env->setKey('APP_DEBUG', 'false');
        $env->setKey('DEBUGBAR_ENABLED', 'false');
        $env->setKey('MAIL_MAILER', 'smtp');
        $env->setKey('MAIL_FROM_NAME', $website['app_name']);
        $env->setKey('MAIL_FROM_ADDRESS', $website['app_email']);

        foreach ($this->service->envKeys($website) as $k => $v) {
            $env->setKey((string) $k, (string) ($v ?? ''));
        }
        $env->save();


        Setting::set('app_url', url('/'));
        Setting::set('app_name', $website['app_name']);
        Setting::set('meta_title', $website['app_name']);
        Setting::set('website_email', $website['app_email']);
        if (Session::has('purchase_code')) {
            Setting::set('purchase_code', Session::get('purchase_code'));
        }
        $settings = $this->service->settings($website);
        if (!empty($settings)) {
            foreach ($settings as $key => $value) {
                Setting::set($key, $value);
            }
        }
        Setting::save();
    }

    private function optimize(): void
    {
        $this->service->beforeOptimize();

        Cache::flush();
        Artisan::call('optimize');

        $this->service->afterOptimize();
    }
}
