<?php

if (!function_exists('uc_parse_env_file')) {
    function uc_parse_env_file(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

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

            $name = trim(substr($line, 0, $eqPos));
            $value = trim(substr($line, $eqPos + 1));
            if ($name === '') {
                continue;
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

if (!function_exists('uc_load_app_env')) {
    function uc_load_app_env(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $root = realpath(__DIR__ . '/..');
        if (!$root) {
            return;
        }

        uc_parse_env_file($root . DIRECTORY_SEPARATOR . '.env');

        $env = getenv('APP_ENV');
        if ($env === false || trim((string)$env) === '') {
            $env = isset($_ENV['APP_ENV']) ? (string)$_ENV['APP_ENV'] : '';
        }
        $env = strtolower(trim((string)$env));
        if ($env === '') {
            $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
            $env = ($host === 'localhost' || $host === '127.0.0.1' || str_starts_with($host, 'localhost:') || str_starts_with($host, '127.0.0.1:'))
                ? 'local'
                : 'production';
        }

        uc_parse_env_file($root . DIRECTORY_SEPARATOR . '.env.' . $env);
    }
}

if (!function_exists('uc_env')) {
    function uc_env(string $name, string $default = ''): string
    {
        uc_load_app_env();
        $value = getenv($name);
        if ($value !== false) {
            return (string)$value;
        }
        if (isset($_ENV[$name])) {
            return (string)$_ENV[$name];
        }
        return $default;
    }
}

if (!function_exists('uc_base_url')) {
    function uc_base_url(string $path = ''): string
    {
        $baseUrl = rtrim(uc_env('BASE_URL', ''), '/');
        if ($baseUrl === '') {
            $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
            if ($host === 'localhost' || $host === '127.0.0.1' || str_starts_with($host, 'localhost:') || str_starts_with($host, '127.0.0.1:')) {
                $baseUrl = 'http://localhost/credit';
            } else {
                $baseUrl = 'https://credit.unicorndevelopment.in';
            }
        }

        if ($path === '') {
            return $baseUrl;
        }
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

