<?php
include 'config.php';

$response = ['success' => false, 'message' => 'Error'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$name = trim($_POST['name'] ?? '');

if ($name === '') {
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit;
}

$imgPath = null;

// Image upload (optional)
if (!empty($_FILES['img']['name'])) {

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($_FILES['img']['type'], $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }

    if ($_FILES['img']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Image must be under 2MB']);
        exit;
    }

    $dir = '../uploads/categories/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fileName = time() . '_' . $_FILES['img']['name'];
    move_uploaded_file($_FILES['img']['tmp_name'], $dir . $fileName);

    $imgPath = 'uploads/categories/' . $fileName;
}

// Insert into DB (safe)
$stmt = $conn->prepare("INSERT INTO categories (name, img) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $imgPath);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Category added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
