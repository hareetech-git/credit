<?php
// ❌ NO session_start() here (same as certificate)
include '../config.php';

// Init
$errors = [];

// Request check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header("Location: ../../testimonials.php");
    exit;
}

// Get & validate ID
$testimonial_id = isset($_POST['testimonial_id']) ? (int)$_POST['testimonial_id'] : 0;
if ($testimonial_id <= 0) {
    $_SESSION['error'] = 'Invalid testimonial ID';
    header("Location: ../../testimonials.php");
    exit;
}

// Get form data
$partner_name     = trim($_POST['partner_name'] ?? '');
$designation      = trim($_POST['designation'] ?? '');
$testimonial_text = trim($_POST['testimonial_text'] ?? '');
$active            = isset($_POST['active']) ? (int)$_POST['active'] : 1;
$existing_image    = trim($_POST['existing_image'] ?? '');

// Validation
if ($partner_name === '') {
    $errors[] = 'Partner name is required';
}

if ($testimonial_text === '') {
    $errors[] = 'Testimonial text is required';
}

// Redirect back if validation fails
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../../testimonial_edit.php?id=$testimonial_id");
    exit;
}

// Default image
$partner_img = $existing_image;

// -------- IMAGE UPLOAD (same pattern as certificate) --------
if (isset($_FILES['partner_img']) && $_FILES['partner_img']['error'] == 0) {

    $target_dir = "../../assets/testimonials/";

    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $errors[] = 'Failed to create upload directory. Please check permissions.';
        }
    }

    if (empty($errors) && !is_writable($target_dir)) {
        $errors[] = 'Upload directory is not writable. Please check permissions.';
    }

    if (empty($errors)) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['partner_img']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed.';
        }

        // Max 5MB (same as certificate)
        if ($_FILES['partner_img']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image size must be less than 5MB.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../testimonial_edit.php?id=$testimonial_id");
        exit;
    }

    $safe_name = preg_replace('/[^a-zA-Z0-9]/', '_', $partner_name);
    $new_name = time() . '_' . $safe_name . '.' . $ext;
    $target_file = $target_dir . $new_name;

    if (!move_uploaded_file($_FILES['partner_img']['tmp_name'], $target_file)) {
        $_SESSION['errors'] = ['Failed to upload image'];
        header("Location: ../../testimonial_edit.php?id=$testimonial_id");
        exit;
    }

    // Delete old image
    if (!empty($existing_image)) {
        $old = "../../" . $existing_image;
        if (file_exists($old)) {
            unlink($old);
        }
    }

    $partner_img = 'assets/testimonials/' . $new_name;
}

// -------- DATABASE UPDATE --------
$query = "UPDATE testimonials 
          SET partner_name = ?, designation = ?, testimonial_text = ?, partner_img = ?, active = ?
          WHERE id = ?";

$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    $_SESSION['error'] = 'Database prepare failed';
    header("Location: ../../testimonial_edit.php?id=$testimonial_id");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssii",
    $partner_name,
    $designation,
    $testimonial_text,
    $partner_img,
    $active,
    $testimonial_id
);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = 'Testimonial updated successfully!';
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../../testimonials.php");
    exit;
} else {
    $_SESSION['error'] = 'Database update failed';
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../../testimonial_edit.php?id=$testimonial_id");
    exit;
}
