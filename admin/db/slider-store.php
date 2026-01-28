<?php
include 'config.php';

$response = ['success'=>false,'message'=>'Error'];

$title = trim($_POST['title']);
$sub_title = trim($_POST['sub_title']);

if ($title === '' || empty($_FILES['img']['name'])) {
    echo json_encode($response);
    exit;
}

$dir = '../uploads/sliders/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$fileName = time().'_'.$_FILES['img']['name'];
move_uploaded_file($_FILES['img']['tmp_name'], $dir.$fileName);

$path = 'uploads/sliders/'.$fileName;

$stmt = $conn->prepare(
    "INSERT INTO slider_images (title, sub_title, img) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $title, $sub_title, $path);
$stmt->execute();

$response['success'] = true;
echo json_encode($response);
