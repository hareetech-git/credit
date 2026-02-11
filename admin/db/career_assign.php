<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../career_applications.php?err=Invalid request");
    exit;
}

$career_id = (int)($_POST['career_id'] ?? 0);
$staff_id = (int)($_POST['staff_id'] ?? 0);
$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;

if ($career_id <= 0) {
    header("Location: ../career_applications.php?err=Invalid application");
    exit;
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS career_applications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(191) NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    resume_original_name VARCHAR(255) NOT NULL,
    status ENUM('new','assigned','closed') NOT NULL DEFAULT 'new',
    assigned_staff_id BIGINT UNSIGNED DEFAULT NULL,
    assigned_by BIGINT UNSIGNED DEFAULT NULL,
    assigned_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_career_status (status),
    KEY idx_career_assigned_staff (assigned_staff_id),
    KEY idx_career_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($staff_id > 0) {
    $checkStaff = mysqli_query($conn, "SELECT id FROM staff WHERE id=$staff_id AND status='active' LIMIT 1");
    if (!$checkStaff || mysqli_num_rows($checkStaff) === 0) {
        header("Location: ../career_applications.php?err=Selected staff is invalid");
        exit;
    }

    $sql = "UPDATE career_applications
            SET assigned_staff_id = $staff_id,
                assigned_by = " . ($admin_id ?: "NULL") . ",
                assigned_at = NOW(),
                status = 'assigned'
            WHERE id = $career_id";
} else {
    $sql = "UPDATE career_applications
            SET assigned_staff_id = NULL,
                assigned_by = NULL,
                assigned_at = NULL,
                status = 'new'
            WHERE id = $career_id";
}

if (mysqli_query($conn, $sql)) {
    header("Location: ../career_applications.php?msg=Assignment updated");
} else {
    header("Location: ../career_applications.php?err=Assignment failed");
}
exit;

