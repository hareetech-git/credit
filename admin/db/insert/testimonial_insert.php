<?php
session_start();
include '../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['partner_name']) || empty($_POST['testimonial_text'])) {
    $_SESSION['errors'] = ['All required fields must be filled'];
    header("Location: ../../testimonial_add.php");
    exit;
}

$partner_name = trim($_POST['partner_name']);
$designation = trim($_POST['designation'] ?? '');
$testimonial_text = trim($_POST['testimonial_text']);
$active = isset($_POST['active']) ? (int)$_POST['active'] : 1;
$partner_img = '';

// Handle file upload
if (isset($_FILES['partner_img']) && $_FILES['partner_img']['error'] == 0) {
    $target_dir = "../../assets/testimonials/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['partner_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG, GIF & WEBP files are allowed'];
        header("Location: ../../testimonial_add.php");
        exit;
    }
    
    // Generate unique filename
    $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $partner_name) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['partner_img']['tmp_name'], $target_file)) {
        $partner_img = 'assets/testimonials/' . $new_filename;
    } else {
        $_SESSION['errors'] = ['Failed to upload image. Check folder permissions.'];
        header("Location: ../../testimonial_add.php");
        exit;
    }
}

// Insert into database
$query = "INSERT INTO testimonials (partner_name, designation, testimonial_text, partner_img, active) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssi", $partner_name, $designation, $testimonial_text, $partner_img, $active);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Testimonial added successfully!";
    header("Location: ../../testimonials.php");
    exit;
} else {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($conn)];
    header("Location: ../../testimonial_add.php");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>