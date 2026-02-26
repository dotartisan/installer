<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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

        // if you want an after hook after everything in App step:
        $this->service->afterEnvWritten($website);
    }

    private function generateAppKey(): void
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    private function storageLink(): void
    {
        // Keep if your items rely on it
        Artisan::call('storage:link');
    }

    private function writeEnvAndSettings(array $website): void
    {
        $env = DotenvEditor::load();
        foreach ($this->service->envKeys($website) as $k => $v) {
            $env->setKey((string) $k, (string) ($v ?? ''));
        }

        $env->save();

        $settings = $this->service->settings($website);
        if (!empty($settings)) {
            foreach ($settings as $key => $value) {
                Setting::set($key, $value);
            }
            Setting::save();
        }
    }

    private function optimize(): void
    {
        $this->service->beforeOptimize();

        Cache::flush();
        Artisan::call('optimize');

        $this->service->afterOptimize();
    }
}
