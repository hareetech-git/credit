<?php
session_start();
include '../config.php';

if (
    empty($_POST['id']) ||
    empty($_POST['department']) ||
    empty($_POST['category_name'])
) {
    header("Location: ../../category.php");
    exit;
}

$id            = (int) $_POST['id'];
$department    = (int) $_POST['department'];
$category_name = trim($_POST['category_name']);
$sequence      = isset($_POST['sequence']) ? (int) $_POST['sequence'] : 1;
$active        = isset($_POST['active']) ? (int) $_POST['active'] : 1;

// Update category
mysqli_query($conn, "
    UPDATE service_categories
    SET
        department    = $department,
        category_name = '$category_name',
        sequence      = $sequence,
        active        = $active,
        updated_at    = NOW()
    WHERE id = $id
");

// Redirect back to list
header("Location: ../../category.php");
exit;
