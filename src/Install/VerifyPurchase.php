<?php

namespace Dotartisan\Installer\Install;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class VerifyPurchase
{
    protected $provider = 'https://verify.bcstatic.com/api-provider';
    protected $product;
    protected $key_path;

    public function __construct()
    {
        if (!isset($this->provider)) {
            abort(401, 'Something went wrong, please contact support.');
        }

        $this->key_path = storage_path('app/.' . $this->product);
    }

    public function satisfied()
    {
        return app(ArtisanApi::class)->hasRegistered();
    }

    public function authorize()
    {
        $authorized = Request::input('authorized');
        $message = Request::input('message');
        $authorized_key = Request::input('authorized_key', null);

        if ($authorized === 'success' && $authorized_key) {
            return $this->generate_key($authorized_key, $message);
        } else {
            return redirect('/install/verify')->withErrors($message);
        }
    }

    public function login()
    {
        $redirect = $this->provider . '?item=' . $this->product . '&return_uri=' . urlencode(URL::route('verify.return')) . '&source=' . config('artisan.source');

        return Redirect::away($redirect);
    }

    protected function generate_key($code, $message)
    {
        if (!$this->satisfied()) {
            $filename = '.' . $this->product;
            Session::put('purchase_code', $code);
            Storage::disk('local')->put($filename, artisanCrypt()->encrypt($code));
        }

        return redirect('/install/verify')->withSuccess($message);
    }
}
