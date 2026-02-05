<?php
include '../config.php';

$id = (int) $_GET['id'];

// Check if service exists
$check = mysqli_query($conn,
"SELECT id FROM services WHERE sub_category_id = $id LIMIT 1"
);

if (mysqli_num_rows($check) > 0) {
    echo "<script>
        alert('First delete all related services');
        window.location.href='../../subcategory.php';
    </script>";
    exit;
}

// Delete subcategory
mysqli_query($conn, "DELETE FROM services_subcategories WHERE id = $id");

header("Location: ../../subcategory.php");
exit;
