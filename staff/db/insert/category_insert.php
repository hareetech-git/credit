<?php
session_start();
include '../config.php';

// Basic validation
if (
    empty($_POST['department']) ||
    empty($_POST['category_name'])
) {
    header("Location: ../../category_add.php");
    exit;
}

$department    = (int) $_POST['department'];
$category_name = trim($_POST['category_name']);
$sequence      = isset($_POST['sequence']) ? (int) $_POST['sequence'] : 1;
$active        = isset($_POST['active']) ? (int) $_POST['active'] : 1;

// Insert category
mysqli_query($conn, "
    INSERT INTO service_categories
    (department, category_name, sequence, active)
    VALUES
    ($department, '$category_name', $sequence, $active)
");

// Redirect back to list
header("Location: ../../category.php");
exit;
