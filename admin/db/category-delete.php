<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = (int)$data['id'];

$success = mysqli_query($conn, "DELETE FROM categories WHERE id=$id");

echo json_encode(['success' => $success]);
