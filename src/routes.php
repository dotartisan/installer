<?php

use Dotartisan\Installer\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'install', 'middleware' => config('installer.route_middleware', [])],  function () {
    Route::get('/pre-installation', [InstallController::class, 'preInstallation'])->name('preinstall');
    Route::get('/verify', [InstallController::class, 'verifyPurchase'])->name('verifypurchase');
    Route::post('/verify/register', [InstallController::class, 'registerPurchase'])->name('verify.register');
    Route::get('/verify/redirect', [InstallController::class, 'redirectPurchase'])->name('verify.redirect');
    Route::get('/verify/return', [InstallController::class, 'returnPurchase'])->name('verify.return');
    Route::get('/verify/cancel', [InstallController::class, 'cancelPurchase'])->name('verify.cancel');
    Route::get('/configuration', [InstallController::class, 'getConfiguration'])->name('installconfig.get');
    Route::post('/configuration', [InstallController::class, 'postConfiguration'])->name('installconfig.post');
    Route::get('/complete', [InstallController::class, 'complete'])->name('installcomplete');
});
