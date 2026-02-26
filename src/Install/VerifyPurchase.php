<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Classes\ArtisanApi;
use Dotartisan\Installer\Contracts\InstallServiceContract;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class VerifyPurchase
{
    /**
     * Fixed provider endpoint (package-controlled)
     */
    protected string $provider = 'https://verify.bcstatic.com/api-provider';

    protected string $product;
    protected string $key_path;

    public function __construct(protected InstallServiceContract $service)
    {
        $this->product  = $this->service->product();
        $this->key_path = storage_path('app/.' . $this->product);
    }

    public function satisfied(): bool
    {
        return app(ArtisanApi::class)->hasRegistered();
    }

    public function login()
    {
        $this->service->beforePurchaseRedirect();

        $redirect = $this->provider
            . '?item=' . $this->product
            . '&return_uri=' . urlencode(URL::route('verify.return'))
            . '&source=' . config('installer.source');

        return Redirect::away($redirect);
    }

    public function authorize()
    {
        $authorized     = Request::input('authorized');
        $message        = Request::input('message');
        $authorized_key = Request::input('authorized_key');

        if ($authorized === 'success' && $authorized_key) {
            return $this->generateKey($authorized_key, $message);
        }

        return redirect('/install/verify')->withErrors($message);
    }

    protected function generateKey(string $authorized_key, ?string $message = null)
    {
        if (!$this->satisfied()) {
            $filename = '.' . $this->product;
            try {
                $data = installerEncrypter()->decrypt($authorized_key);
                $code = $data['code'] ?? null;
                $license = $data['license'] ?? null;
            } catch (\Exception $e) {
            }

            Session::put('purchase_code', $code);

            // uses your helper -> installer_encrypter() or artisanCrypt() depending on your naming
            Storage::disk('local')->put($filename, $authorized_key);

            $this->service->afterPurchaseStored($code);
        }

        return redirect('/install/verify')->withSuccess($message);
    }
}
