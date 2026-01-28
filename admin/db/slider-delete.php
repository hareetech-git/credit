<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = (int)($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success'=>false]);
    exit;
}

/* FETCH IMAGE */
$res = mysqli_query($conn, "SELECT img FROM slider_images WHERE id=$id");
$row = mysqli_fetch_assoc($res);

/* DELETE FILE */
if ($row && file_exists('../'.$row['img'])) {
    unlink('../'.$row['img']);
}

/* DELETE DB */
mysqli_query($conn, "DELETE FROM slider_images WHERE id=$id");

echo json_encode(['success'=>true]);
