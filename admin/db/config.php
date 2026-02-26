<?php
require_once realpath(__DIR__ . '/../../core/db_master.php');
require_once realpath(__DIR__ . '/../../core/crypto_helper.php');
require_once realpath(__DIR__ . '/../../includes/format_helpers.php');

if (!function_exists('uc_admin_env_value')) {
    function uc_admin_env_value(string $name, string $default = ''): string
    {
        static $adminEnv = null;

        if ($adminEnv === null) {
            $adminEnv = [];
            $envPath = realpath(__DIR__ . '/../.env');

            if ($envPath && is_readable($envPath)) {
                $lines = @file($envPath, FILE_IGNORE_NEW_LINES);
                if ($lines !== false) {
                    foreach ($lines as $line) {
                        $line = trim((string)$line);
                        if ($line === '' || str_starts_with($line, '#')) {
                            continue;
                        }

                        if (str_starts_with($line, 'export ')) {
                            $line = trim(substr($line, 7));
                        }

                        $eqPos = strpos($line, '=');
                        if ($eqPos === false) {
                            continue;
                        }

                        $key = trim(substr($line, 0, $eqPos));
                        $value = trim(substr($line, $eqPos + 1));
                        if ($key === '') {
                            continue;
                        }

                        if (
                            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                            (str_starts_with($value, "'") && str_ends_with($value, "'"))
                        ) {
                            $value = substr($value, 1, -1);
                        }

                        $adminEnv[$key] = $value;
                    }
                }
            }
        }

        $runtimeValue = getenv($name);
        if ($runtimeValue !== false) {
            return (string)$runtimeValue;
        }

        if (isset($_ENV[$name])) {
            return (string)$_ENV[$name];
        }

        if (isset($adminEnv[$name])) {
            return (string)$adminEnv[$name];
        }

        return $default;
    }
}

if (!function_exists('uc_admin_is_local_host')) {
    function uc_admin_is_local_host(): bool
    {
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));

        return $host === 'localhost'
            || $host === '127.0.0.1'
            || str_starts_with($host, 'localhost:')
            || str_starts_with($host, '127.0.0.1:');
    }
}

if (!function_exists('uc_admin_file_url')) {
    function uc_admin_file_url(string $path): string
    {
        $rawPath = trim($path);
        if ($rawPath === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $rawPath)) {
            return $rawPath;
        }

        $cleanPath = ltrim($rawPath, '/');
        if (str_starts_with($cleanPath, 'admin/')) {
            $cleanPath = substr($cleanPath, 6);
        }

        $relativePath = str_starts_with($cleanPath, 'uploads/')
            ? '../' . $cleanPath
            : $cleanPath;

        if (uc_admin_is_local_host()) {
            return $relativePath;
        }

        $fileBaseUrl = rtrim(uc_admin_env_value('FILE_BASE_URL', ''), '/');
        if ($fileBaseUrl !== '' && str_starts_with($cleanPath, 'uploads/')) {
            return $fileBaseUrl . '/' . $cleanPath;
        }

        return $relativePath;
    }
}

$conn = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    DB_PORT
);

if (!$conn) {
    die('Database connection failed');
}
