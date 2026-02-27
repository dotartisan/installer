<?php

namespace Dotartisan\Installer\Classes;

use Dotartisan\Installer\Contracts\UpdateServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Setting;
use Theme;
use ZipArchive;

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

/**
 * UpdatesManager (package-generic)
 *
 * - Endpoints are fixed inside this class (not overridable by the app).
 * - Item-specific values (product/version) + hooks live in InstallerService (App\Helpers\Classes\InstallerService).
 * - runUpdate() keeps your original behavior/order, but wraps each step with before/after hooks.
 */
class UpdatesManager
{
    protected string $register_endpoint = 'https://verify.bcstatic.com/register-item';
    protected string $check_updates     = 'https://verify.bcstatic.com/check';
    protected string $update_links      = 'https://verify.bcstatic.com/get-links';
    protected string $patch_links       = 'https://verify.bcstatic.com/check-patches';

    /**
     * Will be filled from API response (download URL), not from the item class.
     */
    protected ?string $download_url = null;

    /**
     * @var array
     */
    protected array $verifyData = [];

    /**
     * Item service (App-specific)
     */
    protected UpdateServiceContract $item;

    /**
     * @param UpdateServiceContract $item
     */
    public function __construct(UpdateServiceContract $item)
    {
        $this->item = $item;

        $this->verifyData = config('installer.installed') ? [
            'code'       => setting('purchase_code'),
            'version'    => setting('version', $this->getAppVersion()),
            'item'       => $this->item->product(),
            'source'     => config('installer.source'),
            'return_uri' => url('/'),
        ] : [
            'code'       => 'asdf',
            'version'    => $this->getAppVersion(),
            'item'       => $this->item->product(),
            'source'     => config('installer.source'),
            'return_uri' => url('/'),
        ];
    }

    /**
     * Run full update routine (original order preserved)
     */
    public function runUpdate()
    {
        $success = session('success');
        $error   = session('error');

        $ok = true;

        // Run-level hook
        $this->item->beforeRunUpdate();

        try {
            $this->optimizeApp();
            $this->runMigration();
            $this->rebuildTheme();
            $this->UpdateAppVersion();
            $this->optimizeApp();
            $this->disableUpdateAlert();
        } catch (\Throwable $e) {
            // Preserve your session behavior style
            $ok = false;
            Session::flash('error', $e->getMessage());
        } finally {
            // Preserve original flash values
            Session::flash('success', $success);
            Session::flash('error', $error);

            $this->item->afterRunUpdate($ok, [
                'success' => $success,
                'error'   => $error,
            ]);
        }

        return compact('success', 'error');
    }

    public function getPatch()
    {
        // Optional: patch-level hooks if you have them in your service
        // (Not required by your request, but useful and consistent.)
        if (method_exists($this->item, 'beforePatch')) {
            $this->item->beforePatch();
        }

        try {
            $response = Http::post($this->patch_links, $this->verifyData);
            $jsonData = $response->json();

            if (isset($jsonData['status']) && !$jsonData['status']) {
                $message = $this->getMessage($jsonData);
                Session::flash('error', $message);

                if (method_exists($this->item, 'afterPatch')) {
                    $this->item->afterPatch(false, $message);
                }

                return false;
            }

            $this->download_url = $jsonData['download'] ?? null;

            $ok = $this->downloadPatch($this->download_url);

            if (method_exists($this->item, 'afterPatch')) {
                $this->item->afterPatch($ok, $ok ? 'Patch applied successfully' : 'Patch failed');
            }

            return $ok;
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());

            if (method_exists($this->item, 'afterPatch')) {
                $this->item->afterPatch(false, $e->getMessage());
            }

            return false;
        }
    }

    /**
     * Disable update alert.
     */
    private function disableUpdateAlert()
    {
        $this->item->beforeDisableUpdateAlert();
        Setting::set("update_available", 0);
        Setting::set("update_available_msg", null);

        Setting::save();
        $this->item->afterDisableUpdateAlert();
    }

    /**
     * Get app version (item-specific).
     */
    private function getAppVersion()
    {
        return $this->item->version();
    }

    /**
     * Rebuild themes + theme cache.
     */
    public function rebuildTheme()
    {
        $this->item->beforeRebuildTheme();
        Theme::rebuildCache();
        $this->item->afterRebuildTheme();
    }

    /**
     * Run database migrations and seeds (original logic preserved).
     */
    public function runMigration()
    {
        $this->item->beforeMigrate();
        Artisan::call('migrate', ['--force' => true]);
        $this->item->afterMigrate();
    }

    /**
     * Update app version in env + settings (original logic preserved).
     */
    private function UpdateAppVersion()
    {
        $this->item->beforeUpdateAppVersion();

        $version = $this->getAppVersion();

        $env = DotenvEditor::load();
        $env->setKey('APP_VERSION', $version);
        $env->save();

        Setting::set('version', $version);
        Setting::save();

        $this->item->afterUpdateAppVersion();
    }

    /**
     * Clear cache and optimize app (original logic preserved).
     */
    private function optimizeApp()
    {
        $this->item->beforeOptimize();
        try {
            Cache::flush();
            Artisan::call('optimize');
        } catch (\Exception $e) {
            // keep silent (your original behavior)
        }
        $this->item->afterOptimize();
    }

    /**
     * Optimize clear app (original logic preserved).
     */
    private function optimizeClearApp()
    {
        try {
            Artisan::call('optimize:clear');
        } catch (\Exception $e) {
            // keep silent (your original behavior)
        }
    }

    /**
     * Check for updates (product now comes from item service).
     */
    public function checkUpdates()
    {
        $response = Http::post($this->check_updates, [
            'version'    => setting('version'),
            'item'       => $this->item->product(),
            'return_uri' => url('/'),
        ]);

        $jsonData = $response->json();

        // updates are found now asking user to download/verify
        if (isset($jsonData['status']) && $jsonData['status'] === true) {
            Session::flash('update_button', $jsonData['message']);
            Setting::set("update_available", 1);
            Setting::set("update_available_msg", $jsonData['message']);
            Setting::save();
        } else {
            $this->disableUpdateAlert();
        }

        $this->checkPatches();

        return true;
    }

    /**
     * Check for patches (original logic preserved).
     */
    public function checkPatches()
    {
        try {
            $response = Http::post($this->patch_links, $this->verifyData);
            $jsonData = $response->json();
            $jsonData = isset($jsonData['status']) ? [] : $jsonData;

            Cache::forever('patches-available', $jsonData);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());

            return false;
        }
    }

    /**
     * Validate and perform update (product/version are item-specific).
     */
    public function validateAndPerformUpdate()
    {
        // Optional: update-level hooks
        if (method_exists($this->item, 'beforeUpdate')) {
            $this->item->beforeUpdate();
        }

        try {
            $response = Http::post($this->update_links, $this->verifyData);
            $jsonData = $response->json();

            if (isset($jsonData['status']) && !$jsonData['status']) {
                $message = $this->getMessage($jsonData);
                Session::flash('error', $message);

                if (method_exists($this->item, 'afterUpdate')) {
                    $this->item->afterUpdate(false, $message);
                }

                return false;
            }

            $this->download_url = $jsonData['download'] ?? null;

            if (($jsonData['has_requirements'] ?? false) == true) {
                $ok = $this->checkRequirements($jsonData['requirements']);
            } else {
                $ok = $this->downloadUpdates($this->download_url);
            }

            if (method_exists($this->item, 'afterUpdate')) {
                $this->item->afterUpdate((bool) $ok, $ok ? 'Update applied successfully' : 'Update failed');
            }

            return $ok;
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());

            if (method_exists($this->item, 'afterUpdate')) {
                $this->item->afterUpdate(false, $e->getMessage());
            }

            return false;
        }
    }

    public function downloadUpdates($url)
    {
        $this->optimizeClearApp();

        $path = $this->downloadUpdateZip($url);

        if (!file_exists($path)) {
            Session::flash('error', "Failed to download update, please try again later.");
            return false;
        }

        $zip = new ZipArchive;

        if (!$zip) {
            Session::flash('error', "PHP Zip extension not loaded.");
            return false;
        }

        $zip->open($path);
        $zip->extractTo(base_path());
        $zip->close();

        @unlink($path);

        if (file_exists(resource_path('cleanup.php'))) {
            include_once(resource_path('cleanup.php'));
        }

        // Your original runUpdate call stays
        $this->runUpdate();

        Session::flash('success', 'Application updated successfully');

        return true;
    }

    public function downloadPatch($url)
    {
        $path = $this->downloadUpdateZip($url);

        if (!file_exists($path)) {
            Session::flash('error', "Failed to download patch, please try again later.");
            return false;
        }

        $zip = new ZipArchive;

        if (!$zip) {
            Session::flash('error', "PHP Zip extension not loaded.");
            return false;
        }

        if ($zip->open($path) !== TRUE) {
            Session::flash('error', "An error occurred creating your ZIP file.");
            return false;
        }

        $zip->extractTo(base_path());
        $zip->close();

        @unlink($path);

        // Your original runUpdate call stays
        $this->runUpdate();

        Session::flash('success', 'Patch applied successfully');

        return true;
    }

    protected function getMessage($response)
    {
        if (isset($response['errors']) && is_array($response['errors']) && count($response['errors']) !== 0) {
            $error = array_pop($response['errors']);
            if (is_array($error)) {
                $error = array_pop($error);
            }
            return $error;
        }

        return $response['message'] ?? 'System could not perform update.';
    }

    protected function downloadUpdateZip($url)
    {
        set_time_limit(0);

        try {
            $path = base_path('tmp.zip');

            // Save downloaded data here
            $fp = fopen($path, 'w+');

            // Replace spaces
            $ch = curl_init(str_replace(" ", "%20", $url));

            curl_setopt($ch, CURLOPT_TIMEOUT, 600);

            // send post data
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->verifyData));

            // write curl response to file
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            curl_exec($ch);

            curl_close($ch);
            fclose($fp);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return false;
        }

        return $path;
    }

    public function checkRequirements($requirements_link)
    {
        $response = Http::post($requirements_link, $this->verifyData);

        $jsonData = $response->json();

        if ($jsonData['status'] == true) {
            $requiremens  = $jsonData['message']['requirements'];
            $requirements =  $this->loadSystemRequirments($requiremens);

            if ($requirements['status'] === true) {
                return $this->downloadUpdates($this->download_url);
            } else {
                Session::flash('error', $requirements['message']);
            }
        }

        return false;
    }

    private function loadSystemRequirments($requiremens)
    {
        $validationStatus = true;
        $validationErrors = [];

        if (isset($requiremens['php']['version']) && !version_compare(PHP_VERSION, $requiremens['php']['version'], $requiremens['php']['operator'])) {
            $validationStatus = false;
            $validationErrors[] = "PHP Version must be {$requiremens['php']['operator']} {$requiremens['php']['version']}";
        }

        // Check extensions requirements
        if (is_array($requiremens['extensions'])) {
            foreach ($requiremens['extensions'] as $key => $value) {
                if (extension_loaded($key) != $value) {
                    $validationStatus = false;
                    $validationErrors[] = "{$key}: {$value}";
                }
            }
        }

        // Check defined requirements
        if (is_array($requiremens['defined'])) {
            foreach ($requiremens['defined'] as $key => $value) {
                if (defined($key) != $value) {
                    $validationStatus = false;
                    $validationErrors[] = $key;
                }
            }
        }

        // Check Apache requirements
        if (is_array($requiremens['apache']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
            foreach ($requiremens['apache'] as $key => $value) {
                if (!in_array('mod_rewrite', apache_get_modules()) && $value == true) {
                    $validationStatus = false;
                    $validationErrors[] = $key;
                }
            }
        }

        return [
            'message' => __("New update requirements are not met :errors", ['errors' => implode(', ', $validationErrors)]),
            'status'  => $validationStatus
        ];
    }
}
