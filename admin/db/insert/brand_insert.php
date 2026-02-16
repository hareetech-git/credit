<?php
session_start();
include '../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['brand_name'])) {
    $_SESSION['errors'] = ['Invalid form submission'];
    header("Location: ../../brand_add.php");
    exit;
}

$brand_name = trim($_POST['brand_name']);
$active = isset($_POST['active']) ? (int)$_POST['active'] : 1;
$brand_img = '';

// Handle file upload
if (isset($_FILES['brand_img']) && $_FILES['brand_img']['error'] == 0) {
    // CORRECTED PATH: assets/brands/ instead of uploads/brands/
    $target_dir = "../../assets/brands/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['brand_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG, GIF, WEBP & SVG files are allowed'];
        header("Location: ../../brand_add.php");
        exit;
    }
    
    // Generate unique filename
    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $brand_name) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['brand_img']['tmp_name'], $target_file)) {
        // CORRECTED PATH: assets/brands/ for database storage
        $brand_img = 'assets/brands/' . $new_filename;
    } else {
        $_SESSION['errors'] = ['Failed to upload image. Check folder permissions.'];
        header("Location: ../../brand_add.php");
        exit;
    }
} else {
    $_SESSION['errors'] = ['Brand image is required'];
    header("Location: ../../brand_add.php");
    exit;
}

// Insert into database
$query = "INSERT INTO brands (brand_name, brand_img, active) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $brand_name, $brand_img, $active);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Brand added successfully!";
    header("Location: ../../brands.php");
    exit;
} else {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($conn)];
    header("Location: ../../brand_add.php");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>