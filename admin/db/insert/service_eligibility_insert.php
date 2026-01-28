<?php
include '../config.php';

$service_id     = (int) $_POST['service_id'];
$criteria_key   = trim($_POST['criteria_key']);
$criteria_value = $_POST['criteria_value'];

if ($service_id <= 0 || $criteria_key === '') {
    header("Location: ../../service_add.php");
    exit;
}

mysqli_query(
    $conn,
    "INSERT INTO service_eligibility_criteria
     (service_id, criteria_key, criteria_value)
     VALUES
     ($service_id, '$criteria_key', '$criteria_value')"
);

header("Location: ../../service_add.php?service_id=$service_id&tab=eligibility");
exit;
