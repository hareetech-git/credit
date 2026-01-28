<?php
session_start();
include '../config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../../category.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CHECK: Any subcategory exists under this category?
|--------------------------------------------------------------------------
*/
$check = mysqli_query(
    $conn,
    "SELECT id FROM services_subcategories WHERE category_id = $id LIMIT 1"
);

if (mysqli_num_rows($check) > 0) {
    echo "<script>
        alert('First delete all related subcategories');
        window.location.href='../../category.php';
    </script>";
    exit;
}

/*
|--------------------------------------------------------------------------
| DELETE CATEGORY
|--------------------------------------------------------------------------
*/
mysqli_query($conn, "DELETE FROM service_categories WHERE id = $id");

// Redirect back
header("Location: ../../category.php");
exit;
