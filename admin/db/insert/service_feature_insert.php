<?php
include '../config.php';

$service_id  = (int) $_POST['service_id'];
$title       = trim($_POST['title']);
$description = $_POST['description'];

if ($service_id <= 0 || $title === '') {
    header("Location: ../../service_add.php");
    exit;
}

mysqli_query(
    $conn,
    "INSERT INTO service_features
     (service_id, title, description)
     VALUES
     ($service_id, '$title', '$description')"
);

header("Location: ../../service_add.php?service_id=$service_id&tab=features");
exit;
