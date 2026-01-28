<?php
if (!isset($conn)) {
    include __DIR__ . '/../db/config.php';
}

$category_id = isset($_GET['category'])
    ? (int) $_GET['category']
    : 0;

$subcategories = [];

if ($category_id > 0) {
    $subcategories = mysqli_query(
        $conn,
        "SELECT id, sub_category_name
         FROM services_subcategories
         WHERE category_id = $category_id
         ORDER BY sub_category_name ASC"
    );
}
