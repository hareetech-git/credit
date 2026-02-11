<?php

if (!function_exists('uc_load_env_files')) {
    function uc_load_env_files(): void
    {
        static $loaded = false;
        if ($loaded) {
            return;
        }
        $loaded = true;

        $paths = [
            realpath(__DIR__ . '/../.env'),
            realpath(__DIR__ . '/../admin/.env'),
            realpath(__DIR__ . '/../staff/.env'),
        ];
        $allowedKeys = ['APP_AES_KEY', 'AES_SECRET_KEY', 'ENCRYPTION_KEY'];

        foreach ($paths as $path) {
            if (!$path || !is_readable($path)) {
                continue;
            }

            $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $line = trim($line);
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
                if (!in_array($name, $allowedKeys, true)) {
                    continue;
                }

                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                // Always prefer values from project env files for crypto keys.
                putenv($name . '=' . $value);
                $_ENV[$name] = $value;
            }
        }
    }
}

if (!function_exists('uc_get_env_value')) {
    function uc_get_env_value(string $name, string $default = ''): string
    {
        uc_load_env_files();
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

if (!function_exists('uc_get_crypto_key')) {
    function uc_get_crypto_key(): string
    {
        static $binaryKey = null;
        if ($binaryKey !== null) {
            return $binaryKey;
        }

        $raw = uc_get_env_value('APP_AES_KEY');
        if ($raw === '') {
            $raw = uc_get_env_value('AES_SECRET_KEY');
        }
        if ($raw === '') {
            $raw = uc_get_env_value('ENCRYPTION_KEY');
        }

        $binaryKey = $raw === '' ? '' : hash('sha256', $raw, true);
        return $binaryKey;
    }
}

if (!function_exists('uc_get_crypto_keys_for_decrypt')) {
    function uc_get_crypto_keys_for_decrypt(): array
    {
        $keys = [];
        $primary = uc_get_crypto_key();
        if ($primary !== '') {
            $keys[] = $primary;
        }

        $oldRaw = uc_get_env_value('APP_AES_KEY_OLD');
        if ($oldRaw === '') {
            return $keys;
        }

        $parts = array_filter(array_map('trim', explode(',', $oldRaw)));
        foreach ($parts as $part) {
            $k = hash('sha256', $part, true);
            if (!in_array($k, $keys, true)) {
                $keys[] = $k;
            }
        }

        return $keys;
    }
}

if (!function_exists('uc_encrypt_sensitive')) {
    function uc_encrypt_sensitive(?string $plainText): string
    {
        $plainText = (string)$plainText;
        if ($plainText === '') {
            return '';
        }
        if (str_starts_with($plainText, 'enc:v1:')) {
            return $plainText;
        }

        $key = uc_get_crypto_key();
        if ($key === '') {
            return $plainText;
        }

        $iv = random_bytes(16);
        $cipherRaw = openssl_encrypt($plainText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipherRaw === false) {
            return $plainText;
        }

        $mac = hash_hmac('sha256', $iv . $cipherRaw, $key, true);
        $payload = base64_encode($iv . $mac . $cipherRaw);
        $payload = rtrim(strtr($payload, '+/', '-_'), '=');

        return 'enc:v1:' . $payload;
    }
}

if (!function_exists('uc_decrypt_sensitive')) {
    function uc_decrypt_sensitive(?string $cipherText): string
    {
        $cipherText = (string)$cipherText;
        if ($cipherText === '') {
            return '';
        }
        if (!str_starts_with($cipherText, 'enc:v1:')) {
            return $cipherText;
        }

        $keys = uc_get_crypto_keys_for_decrypt();
        if (empty($keys)) {
            return $cipherText;
        }

        $payload = substr($cipherText, 7);
        $payload = strtr($payload, '-_', '+/');
        $padding = strlen($payload) % 4;
        if ($padding > 0) {
            $payload .= str_repeat('=', 4 - $padding);
        }

        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) <= 48) {
            return $cipherText;
        }

        $iv = substr($raw, 0, 16);
        $mac = substr($raw, 16, 32);
        $cipherRaw = substr($raw, 48);

        foreach ($keys as $key) {
            $calcMac = hash_hmac('sha256', $iv . $cipherRaw, $key, true);
            if (!hash_equals($mac, $calcMac)) {
                continue;
            }

            $plainText = openssl_decrypt($cipherRaw, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            if ($plainText !== false) {
                return $plainText;
            }
        }

        return $cipherText;
    }
}
