<?php
require_once __DIR__ . '/../../core/db_master.php';

$conn = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME,
    $DB_PORT
);

if (!$conn) {
    die("Database connection failed");
}
