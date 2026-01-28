<?php
if (!isset($conn)) {
    include __DIR__ . '/../db/config.php';
}

$department_id = isset($_GET['department'])
    ? (int) $_GET['department']
    : (isset($_POST['department']) ? (int) $_POST['department'] : 0);

$categories = [];

if ($department_id > 0) {
    $categories = mysqli_query(
        $conn,
        "SELECT id, category_name
         FROM service_categories
         WHERE department = $department_id
         AND active = 1
         ORDER BY category_name ASC"
    );
}
