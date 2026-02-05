<?php
session_start();
include '../config.php';

$category_id       = (int) $_POST['category_id'];
$sub_category_name = trim($_POST['sub_category_name']);
$sequence          = (int) $_POST['sequence'];
$status            = $_POST['status'];

mysqli_query($conn,
"INSERT INTO services_subcategories
(category_id, sub_category_name, sequence, status)
VALUES ($category_id, '$sub_category_name', $sequence, '$status')"
);

header("Location: ../../subcategory.php");
exit;
