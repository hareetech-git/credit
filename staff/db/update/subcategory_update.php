<?php
session_start();
include '../config.php';

$id                = (int) $_POST['id'];
$category_id       = (int) $_POST['category_id'];
$sub_category_name = trim($_POST['sub_category_name']);
$sequence          = (int) $_POST['sequence'];
$status            = $_POST['status'];

mysqli_query($conn,
"UPDATE services_subcategories
SET
    category_id = $category_id,
    sub_category_name = '$sub_category_name',
    sequence = $sequence,
    status = '$status',
    updated_at = NOW()
WHERE id = $id"
);

header("Location: ../../subcategory.php");
exit;
