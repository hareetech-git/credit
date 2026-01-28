<?php
include '../config.php';

/* ---------------------------------
| COLLECT DATA
----------------------------------*/
$category_id     = (int) $_POST['category_id'];
$sub_category_id = (int) $_POST['sub_category_id'];
$title           = trim($_POST['title']);
$short_desc      = $_POST['short_description'] ?? '';
$long_desc       = $_POST['long_description'] ?? '';

/* ---------------------------------
| BASIC VALIDATION
----------------------------------*/
if ($category_id <= 0 || $sub_category_id <= 0 || $title === '') {
    header("Location: ../../service_add.php");
    exit;
}

/* ---------------------------------
| INSERT SERVICE
----------------------------------*/
mysqli_query(
    $conn,
    "INSERT INTO services
        (category_id, sub_category_id, title, short_description, long_description)
     VALUES
        ($category_id, $sub_category_id, '$title', '$short_desc', '$long_desc')"
);

/* ---------------------------------
| REDIRECT TO NEXT TAB
----------------------------------*/
$service_id = mysqli_insert_id($conn);

header("Location: ../../service_add.php?service_id=$service_id&tab=overview");
exit;
