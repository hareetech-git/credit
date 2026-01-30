<?php
include '../includes/connection.php';
header('Content-Type: application/json');

$service_id = (int)$_GET['service_id'];
$query = "SELECT doc_name, disclaimer FROM service_documents WHERE service_id = $service_id";
$res = mysqli_query($conn, $query);

$docs = [];
while($row = mysqli_fetch_assoc($res)) {
    $docs[] = $row;
}
echo json_encode($docs);