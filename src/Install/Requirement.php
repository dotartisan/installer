<?php

namespace Dotartisan\Installer\Install;

class Requirement
{
    public function extensions()
    {
        return [
            'PHP >= 8.3' => version_compare(phpversion(), '8.3'),
            'cURL PHP Extension' => extension_loaded('curl'),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'Fileinfo PHP Extension' => extension_loaded('fileinfo'),
            'JSON PHP Extension' => extension_loaded('json'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'PDO PHP Extension' => extension_loaded('pdo'),
            'Intl PHP Extension' => extension_loaded('intl'),
            'OpenSSL PHP Extension' => extension_loaded('openssl'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
            'XML PHP Extension' => extension_loaded('xml'),
            'EXIF PHP Extension' => extension_loaded('exif'),
            'GD PHP Extension' => extension_loaded('gd'),
        ];
    }

    public function directories()
    {
        return [
            'storage' => is_writable(storage_path()),
            'bootstrap/cache' => is_writable(app()->bootstrapPath('cache')),
        ];
    }

    public function satisfied()
    {
        return collect($this->extensions())
            ->merge($this->directories())
            ->every(
                function ($item) {
                    return $item;
                }
            );
    }
}
