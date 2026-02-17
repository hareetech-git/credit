<?php
session_start();
include '../config.php';

// Initialize error array
$errors = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors'] = ['Invalid request method'];
    header("Location: ../../certificates.php");
    exit;
}

// Get and validate certificate ID
$certificate_id = isset($_POST['certificate_id']) ? (int)$_POST['certificate_id'] : 0;
if ($certificate_id <= 0) {
    $errors[] = 'Invalid certificate ID';
    $_SESSION['errors'] = $errors;
    header("Location: ../../certificates.php");
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$existing_image = trim($_POST['existing_image'] ?? '');

// Validate name
if (empty($name)) {
    $errors[] = 'Certificate name is required';
}

// If there are validation errors, redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../../certificate_edit.php?id=$certificate_id");
    exit;
}

// Initialize image path with existing image
$certificate_img = $existing_image;

// Handle file upload if new image is provided
if (isset($_FILES['certificate_img']) && $_FILES['certificate_img']['error'] == 0) {
    $target_dir = "../../assets/certificates/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $errors[] = 'Failed to create upload directory. Please check permissions.';
            $_SESSION['errors'] = $errors;
            header("Location: ../../certificate_edit.php?id=$certificate_id");
            exit;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($target_dir)) {
        $errors[] = 'Upload directory is not writable. Please check permissions.';
        $_SESSION['errors'] = $errors;
        header("Location: ../../certificate_edit.php?id=$certificate_id");
        exit;
    }
    
    $file_extension = strtolower(pathinfo($_FILES['certificate_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $errors[] = 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed. Uploaded file type: ' . $file_extension;
        $_SESSION['errors'] = $errors;
        header("Location: ../../certificate_edit.php?id=$certificate_id");
        exit;
    }
    
    // Check file size (limit to 5MB)
    if ($_FILES['certificate_img']['size'] > 5 * 1024 * 1024) {
        $errors[] = 'File size too large. Maximum allowed size is 5MB.';
        $_SESSION['errors'] = $errors;
        header("Location: ../../certificate_edit.php?id=$certificate_id");
        exit;
    }
    
    // Generate unique filename
    $safe_name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    $new_filename = time() . '_' . $safe_name . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['certificate_img']['tmp_name'], $target_file)) {
        // Delete old image if exists
        if (!empty($existing_image)) {
            $old_file_path = "../../" . $existing_image;
            if (file_exists($old_file_path)) {
                if (!unlink($old_file_path)) {
                    // Log error but don't stop the process
                    error_log("Failed to delete old certificate image: " . $old_file_path);
                }
            }
        }
        $certificate_img = 'assets/certificates/' . $new_filename;
    } else {
        $upload_error = $_FILES['certificate_img']['error'];
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $error_msg = isset($error_messages[$upload_error]) 
            ? $error_messages[$upload_error] 
            : 'Failed to upload image. Error code: ' . $upload_error;
        
        $errors[] = $error_msg;
        $_SESSION['errors'] = $errors;
        header("Location: ../../certificate_edit.php?id=$certificate_id");
        exit;
    }
}

// Update database using prepared statement
$query = "UPDATE certificates SET name = ?, certificate_img = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    $errors[] = "Database error: Failed to prepare statement. " . mysqli_error($conn);
    $_SESSION['errors'] = $errors;
    header("Location: ../../certificate_edit.php?id=$certificate_id");
    exit;
}

mysqli_stmt_bind_param($stmt, "ssi", $name, $certificate_img, $certificate_id);

if (mysqli_stmt_execute($stmt)) {
    // Check if any row was actually updated
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['success_message'] = "Certificate updated successfully!";
    } else {
        $_SESSION['success_message'] = "No changes were made to the certificate.";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../../certificates.php");
    exit;
} else {
    $errors[] = "Database error: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    $_SESSION['errors'] = $errors;
    header("Location: ../../certificate_edit.php?id=$certificate_id");
    exit;
}
?>