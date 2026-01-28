<?php
if (!isset($conn)) {
    include __DIR__ . '/../db/config.php';
}

$departments = mysqli_query(
    $conn,
    "SELECT id, name FROM departments ORDER BY name ASC"
);
