<?php
include '../config.php';

/* ---------------------------------
| COLLECT DATA
----------------------------------*/
$service_id = (int) $_POST['service_id'];
$title      = trim($_POST['title']);
$keys       = $_POST['keys'] ?? [];
$values     = $_POST['values'] ?? [];

/* ---------------------------------
| BASIC VALIDATION
----------------------------------*/
if ($service_id <= 0 || $title === '') {
    header("Location: ../../service_add.php");
    exit;
}

/* ---------------------------------
| CLEAN EMPTY VALUES
----------------------------------*/
$keys   = array_values(array_filter($keys));
$values = array_values(array_filter($values));

/* ---------------------------------
| CONVERT TO JSON
----------------------------------*/
$keys_json   = json_encode($keys, JSON_UNESCAPED_UNICODE);
$values_json = json_encode($values, JSON_UNESCAPED_UNICODE);

/* ---------------------------------
| INSERT OVERVIEW
----------------------------------*/
mysqli_query(
    $conn,
    "INSERT INTO service_overview
        (service_id, title, `keys`, `values`)
     VALUES
        ($service_id, '$title', '$keys_json', '$values_json')"
);

/* ---------------------------------
| REDIRECT TO NEXT TAB
----------------------------------*/
header("Location: ../../service_add.php?service_id=$service_id&tab=features");
exit;
