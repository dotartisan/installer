<?php

namespace Dotartisan\Installer\Install;

use Dotartisan\Installer\Contracts\InstallServiceContract;

class Requirement
{
    public function __construct(protected InstallServiceContract $service) {}

    public function extensions(): array
    {
        $minPhp = $this->service->minPhpVersion();

        $base = [
            "PHP >= {$minPhp}" => version_compare(PHP_VERSION, $minPhp, '>='),
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

        return $base + $this->normalizeChecks($this->service->extraExtensions());
    }

    public function directories(): array
    {
        $base = [
            'storage' => is_writable(storage_path()),
            'bootstrap/cache' => is_writable(app()->bootstrapPath('cache')),
        ];

        return $base + $this->normalizeChecks($this->service->extraDirectories());
    }

    public function satisfied(): bool
    {
        $this->service->beforeRequirementsCheck();

        $extensions = $this->extensions();
        $directories = $this->directories();

        $ok = collect($extensions)->merge($directories)->every(fn($v) => (bool) $v);

        $this->service->afterRequirementsCheck($ok, $extensions, $directories);

        return $ok;
    }

    private function normalizeChecks(array $checks): array
    {
        $out = [];
        foreach ($checks as $label => $value) {
            $out[$label] = is_callable($value) ? (bool) $value() : (bool) $value;
        }
        return $out;
    }
}
