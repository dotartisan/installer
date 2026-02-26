<?php

namespace Dotartisan\Installer\Classes;

use Dotartisan\Installer\Classes\UpdatesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Setting;

class ArtisanApi extends UpdatesManager
{
    public function register(Request $request)
    {
        $request->validate(['code' => 'required|uuid'], ['code.*' => 'Please enter valid purchase code.']);
        $data = $this->verifyData;
        $data['code'] = $request->input('code');
        $data['source'] = config('installer.source');

        try {
            $response = Http::post($this->register_endpoint, $data);
            $jsonData = $response->json();

            if (isset($jsonData['status']) && $jsonData['status'] === true) {
                $code = $request->input('code');

                try {
                    $content = installerEncrypter()->encrypt($code);
                } catch (\Exception $e) {
                    throw new \Exception("Couldn't register product, please contact support.");
                }

                if (config('installer.installed')) {
                    Setting::set('purchase_code', $code);
                    Setting::save();
                }

                Session::put('purchase_code', $code);
                Storage::disk('local')->put(".{$this->item->product()}", $content);
            }

            return response()->json($jsonData, 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

    public function verify()
    {
        $data = $this->getLicenseData();
        $code = $data['code'] ?? null;

        if (config('installer.installed') && setting('purchase_code', null) === $code) {
            return $code;
        } else if (!config('installer.installed')) {
            return $code;
        }

        return null;
    }

    public function license()
    {
        $data = $this->getLicenseData();
        $license = $data['license'] ?? null;

        return $license;
    }

    protected function getLicenseData()
    {
        $data = null;
        $file = storage_path("app/.{$this->item->product()}");
        if (file_exists($file)) {
            $content = File::get($file);
            try {
                $data = installerEncrypter()->decrypt($content);
            } catch (\Exception $e) {
            }
        }
        return $data;
    }

    public function getToken()
    {
        $code = $this->verify();

        return !empty($code) ? base64_encode($code) : null;
    }

    public function hasRegistered()
    {
        $verify = Validator::make(['code' => $this->verify()], ['code' => 'required|uuid']);

        return $verify->passes();
    }
}
