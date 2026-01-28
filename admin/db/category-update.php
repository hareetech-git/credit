<?php
include 'config.php';

$response = [
    'success' => false,
    'message' => 'Something went wrong'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = trim($_POST['name'] ?? '');

if ($id <= 0 || $name === '') {
    $response['message'] = 'Invalid category data';
    echo json_encode($response);
    exit;
}

/* =========================
   CHECK EXISTING CATEGORY
========================= */
$stmt = $conn->prepare("SELECT img FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    $response['message'] = 'Category not found';
    echo json_encode($response);
    exit;
}

/* =========================
   IMAGE UPLOAD (OPTIONAL)
========================= */
$imgPath = $category['img']; // keep old image by default

if (!empty($_FILES['img']['name'])) {

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($_FILES['img']['type'], $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Only JPG, PNG, WEBP images allowed'
        ]);
        exit;
    }

    if ($_FILES['img']['size'] > 2 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Image must be less than 2MB'
        ]);
        exit;
    }

    $uploadDir = '../uploads/categories/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['img']['name']);
    $fullPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['img']['tmp_name'], $fullPath)) {
        echo json_encode([
            'success' => false,
            'message' => 'Image upload failed'
        ]);
        exit;
    }

    // delete old image (optional but recommended)
    if (!empty($category['img']) && file_exists('../' . $category['img'])) {
        unlink('../' . $category['img']);
    }

    $imgPath = 'uploads/categories/' . $fileName;
}

/* =========================
   UPDATE CATEGORY
========================= */
$stmt = $conn->prepare("UPDATE categories SET name = ?, img = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $imgPath, $id);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Category updated successfully';
} else {
    $response['message'] = 'Database update failed';
}

$stmt->close();
$conn->close();

echo json_encode($response);
