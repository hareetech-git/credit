<?php
require_once '../includes/connection.php';
require_once '../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../career.php?err=Invalid request');
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

$email = trim((string)($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../career.php?err=Please enter a valid email address');
    exit;
}

if (empty($_FILES['resume_pdf']['name']) || !is_uploaded_file($_FILES['resume_pdf']['tmp_name'])) {
    header('Location: ../career.php?err=Please upload your resume in PDF format');
    exit;
}

$resume = $_FILES['resume_pdf'];
$originalName = (string)($resume['name'] ?? '');
$tmpPath = (string)($resume['tmp_name'] ?? '');
$fileSize = (int)($resume['size'] ?? 0);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if ($ext !== 'pdf') {
    header('Location: ../career.php?err=Only PDF resumes are allowed');
    exit;
}

if ($fileSize <= 0 || $fileSize > (5 * 1024 * 1024)) {
    header('Location: ../career.php?err=Resume must be less than 5 MB');
    exit;
}

$fileHead = @file_get_contents($tmpPath, false, null, 0, 4);
if ($fileHead !== '%PDF') {
    header('Location: ../career.php?err=Uploaded file is not a valid PDF');
    exit;
}

$uploadDir = realpath(__DIR__ . '/../uploads');
if ($uploadDir === false) {
    $uploadDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        header('Location: ../career.php?err=Upload directory is not available');
        exit;
    }
}

$careerDir = $uploadDir . DIRECTORY_SEPARATOR . 'careers';
if (!is_dir($careerDir) && !mkdir($careerDir, 0755, true)) {
    header('Location: ../career.php?err=Career upload directory is not available');
    exit;
}

$safeBase = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
$safeBase = trim((string)$safeBase, '_');
if ($safeBase === '') {
    $safeBase = 'resume';
}

$storedName = 'career_' . time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeBase . '.pdf';
$storedPathAbs = $careerDir . DIRECTORY_SEPARATOR . $storedName;
$storedPathRel = 'uploads/careers/' . $storedName;

if (!move_uploaded_file($tmpPath, $storedPathAbs)) {
    header('Location: ../career.php?err=Unable to upload resume');
    exit;
}

$emailEsc = mysqli_real_escape_string($conn, $email);
$pathEsc = mysqli_real_escape_string($conn, $storedPathRel);
$origEsc = mysqli_real_escape_string($conn, $originalName);

$insertSql = "INSERT INTO career_applications (email, resume_path, resume_original_name)
              VALUES ('$emailEsc', '$pathEsc', '$origEsc')";

if (!mysqli_query($conn, $insertSql)) {
    if (file_exists($storedPathAbs)) {
        @unlink($storedPathAbs);
    }
    header('Location: ../career.php?err=Unable to submit application right now');
    exit;
}

$careerId = (int)mysqli_insert_id($conn);

$hrEmail = '';
$hrRes = mysqli_query($conn, "SELECT hr_email FROM web_settings LIMIT 1");
if ($hrRes && mysqli_num_rows($hrRes) > 0) {
    $hrRow = mysqli_fetch_assoc($hrRes);
    $hrEmail = trim((string)($hrRow['hr_email'] ?? ''));
}

$mailSent = false;
if ($hrEmail !== '' && filter_var($hrEmail, FILTER_VALIDATE_EMAIL)) {
    $subject = 'New Career Application #' . $careerId;
    $body = '<h3>New Career Application Received</h3>'
        . '<p><strong>Application ID:</strong> ' . $careerId . '</p>'
        . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
        . '<p><strong>Resume:</strong> ' . htmlspecialchars($originalName) . '</p>';

    $mailSent = sendEnquiryEmailWithAttachments(
        $hrEmail,
        'HR Team',
        $subject,
        $body,
        '',
        [
            [
                'path' => $storedPathAbs,
                'name' => $originalName,
            ]
        ]
    );
}

if ($mailSent) {
    header('Location: ../career.php?msg=Application submitted successfully');
} else {
    header('Location: ../career.php?msg=Application saved. HR notification will be reviewed by admin.');
}
exit;

