<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['certificate_id']) || empty($_POST['name'])) {
    $_SESSION['errors'] = ['All fields are required'];
    header("Location: ../../certificates.php");
    exit;
}

$certificate_id = (int)$_POST['certificate_id'];
$name = trim($_POST['name']);
$existing_image = isset($_POST['existing_image']) ? trim($_POST['existing_image']) : '';
$certificate_img = $existing_image;

// Handle file upload if new image is provided
if (isset($_FILES['certificate_img']) && $_FILES['certificate_img']['error'] == 0) {
    $target_dir = "../../assets/certificates/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['certificate_img']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($file_extension, $allowed_extensions)) {
        // Delete old image if exists
        if (!empty($existing_image) && file_exists("../../" . $existing_image)) {
            unlink("../../" . $existing_image);
        }
        
        // Upload new image
        $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['certificate_img']['tmp_name'], $target_file)) {
            $certificate_img = 'assets/certificates/' . $new_filename;
        }
    } else {
        $_SESSION['errors'] = ['Only JPG, JPEG, PNG, GIF & WEBP files are allowed'];
        header("Location: ../../certificate_edit.php?id=$certificate_id");
        exit;
    }
}

// Update database
$query = "UPDATE certificates SET name = ?, certificate_img = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $name, $certificate_img, $certificate_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Certificate updated successfully!";
    header("Location: ../../certificates.php");
    exit;
} else {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($conn)];
    header("Location: ../../certificate_edit.php?id=$certificate_id");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>  