<?php
if (
    $_SERVER['HTTP_HOST'] === 'localhost' ||
    $_SERVER['HTTP_HOST'] === '127.0.0.1'
) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'credit');
    define('DB_PORT', 3306);
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u443392627_credit');
    define('DB_PASS', 'j?oku6wH0@');
    define('DB_NAME', 'u443392627_credit');
    define('DB_PORT', 3306);
} 
