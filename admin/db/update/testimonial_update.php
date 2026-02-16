<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['testimonial_id']) || empty($_POST['partner_name']) || empty($_POST['testimonial_text'])) {
    $_SESSION['errors'] = ['All required fields must be filled'];
    header("Location: ../../testimonials.php");
    exit;
}

$testimonial_id = (int)$_POST['testimonial_id'];
$partner_name = trim($_POST['partner_name']);
$designation = trim($_POST['designation'] ?? '');
$testimonial_text = trim($_POST['testimonial_text']);
$active = isset($_POST['active']) ? (int)$_POST['active'] : 1;
$existing_image = isset($_POST['existing_image']) ? trim($_POST['existing_image']) : '';
$partner_img = $existing_image;

// Handle file upload if new image is provided
if (isset($_FILES['partner_img']) && $_FILES['partner_img']['error'] == 0) {
    $target_dir = "../../assets/testimonials/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['partner_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($file_extension, $allowed_extensions)) {
        // Delete old image if exists
        if (!empty($existing_image) && file_exists("../../" . $existing_image)) {
            unlink("../../" . $existing_image);
        }
        
        // Upload new image
        $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $partner_name) . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['partner_img']['tmp_name'], $target_file)) {
            $partner_img = 'assets/testimonials/' . $new_filename;
        }
    } else {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG, GIF & WEBP files are allowed'];
        header("Location: ../../testimonial_edit.php?id=$testimonial_id");
        exit;
    }
}

// Update database
$query = "UPDATE testimonials SET partner_name = ?, designation = ?, testimonial_text = ?, partner_img = ?, active = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssssii", $partner_name, $designation, $testimonial_text, $partner_img, $active, $testimonial_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Testimonial updated successfully!";
    header("Location: ../../testimonials.php");
    exit;
} else {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($conn)];
    header("Location: ../../testimonial_edit.php?id=$testimonial_id");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>