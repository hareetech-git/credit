<?php
include 'config.php';

$response = ['success' => false, 'message' => 'Invalid request'];

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$category_id = (int)$_POST['category_id'];
$name = trim($_POST['name']);
$description = trim($_POST['description']); // New
$estimated_time = trim($_POST['estimated_time']); // New
$price = $_POST['price'];
$discount_price = $_POST['discount_price'] !== '' ? $_POST['discount_price'] : null;
$rating = $_POST['rating'] !== '' ? $_POST['rating'] : null;

if (!$category_id || !$name || !$price) {
    echo json_encode($response);
    exit;
}

if ($id) {
    // UPDATE
    $stmt = $conn->prepare(
        "UPDATE services 
         SET category_id=?, name=?, description=?, estimated_time=?, price=?, discount_price=?, rating=? 
         WHERE id=?"
    );
    $stmt->bind_param("isssdddi", $category_id, $name, $description, $estimated_time, $price, $discount_price, $rating, $id);
    $stmt->execute();
    $service_id = $id;
    $message = 'Service updated successfully';
} else {
    // INSERT
    $stmt = $conn->prepare(
        "INSERT INTO services (category_id, name, description, estimated_time, price, discount_price, rating)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("isssddd", $category_id, $name, $description, $estimated_time, $price, $discount_price, $rating);
    $stmt->execute();
    $service_id = $stmt->insert_id;
    $message = 'Service added successfully';
}
$stmt->close();

/* IMAGE LOGIC REMAINS THE SAME AS YOUR ORIGINAL CODE */
if (!empty($_FILES['images']['name'][0])) {
    $dir = '../uploads/services/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!$tmp) continue;
        $fileName = time() . '_' . $i . '_' . basename($_FILES['images']['name'][$i]);
        move_uploaded_file($tmp, $dir . $fileName);
        $path = 'uploads/services/' . $fileName;
        mysqli_query($conn, "INSERT INTO services_imgs (service_id, img) VALUES ($service_id, '$path')");
    }
}

$response['success'] = true;
$response['message'] = $message;
echo json_encode($response);