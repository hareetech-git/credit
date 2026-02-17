<?php
session_start();
include '../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['name'])) {
    $_SESSION['errors'] = ['Certificate name is required'];
    header("Location: ../../certificate_add.php");
    exit;
}

$name = trim($_POST['name']);
$certificate_img = '';

// Handle file upload
if (isset($_FILES['certificate_img']) && $_FILES['certificate_img']['error'] == 0) {
    $target_dir = "../../assets/certificates/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['certificate_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG, GIF & WEBP files are allowed'];
        header("Location: ../../certificate_add.php");
        exit;
    }
    
    // Generate unique filename
    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['certificate_img']['tmp_name'], $target_file)) {
        $certificate_img = 'assets/certificates/' . $new_filename;
    } else {
        $_SESSION['errors'] = ['Failed to upload image. Check folder permissions.'];
        header("Location: ../../certificate_add.php");
        exit;
    }
} else {
    $_SESSION['errors'] = ['Certificate image is required'];
    header("Location: ../../certificate_add.php");
    exit;
}

// Insert into database
$query = "INSERT INTO certificates (name, certificate_img) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $name, $certificate_img);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Certificate added successfully!";
    header("Location: ../../certificates.php");
    exit;
} else {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($conn)];
    header("Location: ../../certificate_add.php");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>