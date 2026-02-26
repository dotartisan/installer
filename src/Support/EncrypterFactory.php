<?php

namespace Dotartisan\Installer\Support;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class EncrypterFactory
{
    /**
     * Create a new Encrypter instance.
     *
     * @return \Illuminate\Encryption\Encrypter
     */
    public static function make(): Encrypter
    {
        $key = Config::get('installer.encrypt_key');

        if (empty($key)) {
            throw new RuntimeException('Encryption key [installer.encrypt_key] is not set.');
        }

        // Support base64 keys like Laravel APP_KEY
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $cipher = 'aes-256-cbc';

        // AES-256-CBC requires 32 byte key
        if (mb_strlen($key, '8bit') !== 32) {
            throw new RuntimeException('Encryption key must be 32 bytes for AES-256-CBC.');
        }

        return new Encrypter($key, $cipher);
    }
}
