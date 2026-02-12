<?php
require_once __DIR__ . '/app_env.php';

return [
    'host' => uc_env('SMTP_HOST', 'smtp.hostinger.com'),
    'port' => (int)uc_env('SMTP_PORT', '587'),
    'username' => uc_env('SMTP_USERNAME', 'sanayam@hareetech.com'),
    'password' => uc_env('SMTP_PASSWORD', 'Hareetech@San25'),
    'encryption' => uc_env('SMTP_ENCRYPTION', 'tls'),
    'from_address' => uc_env('SMTP_FROM_ADDRESS', 'sanayam@hareetech.com'),
    'from_name' => uc_env('SMTP_FROM_NAME', 'Udhaar Capital'),
];
