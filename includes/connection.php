<?php
require_once realpath(__DIR__ . '/../core/db_master.php');
require_once realpath(__DIR__ . '/../core/crypto_helper.php');

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
