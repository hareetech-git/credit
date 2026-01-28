<?php
include 'config.php';

$response = ['success' => false, 'message' => 'Invalid request'];

$data = json_decode(file_get_contents("php://input"), true);
$image_id = (int)($data['id'] ?? 0);

if ($image_id <= 0) {
    echo json_encode($response);
    exit;
}

/* FETCH IMAGE PATH */
$result = mysqli_query(
    $conn,
    "SELECT img FROM services_imgs WHERE id = $image_id"
);

$image = mysqli_fetch_assoc($result);

if (!$image) {
    echo json_encode(['success' => false, 'message' => 'Image not found']);
    exit;
}

/* DELETE FILE */
$filePath = '../' . $image['img'];
if (file_exists($filePath)) {
    unlink($filePath);
}

/* DELETE DB ROW */
mysqli_query($conn, "DELETE FROM services_imgs WHERE id = $image_id");

echo json_encode(['success' => true]);
