<?php
include '../config.php';

function clean(mysqli $conn, $value): string
{
    return mysqli_real_escape_string($conn, trim((string) $value));
}

function resolveOldImagePath(string $storedPath): string
{
    $storedPath = trim(str_replace('\\', '/', $storedPath));
    if ($storedPath === '') {
        return '';
    }

    $rootDir = realpath(__DIR__ . '/../../../');
    $adminDir = realpath(__DIR__ . '/../../');

    if (strpos($storedPath, 'admin/') === 0 && $rootDir) {
        return $rootDir . '/' . $storedPath;
    }

    if (strpos($storedPath, 'assets/') === 0 && $adminDir) {
        return $adminDir . '/' . $storedPath;
    }

    if ($adminDir) {
        return $adminDir . '/assets/team/' . basename($storedPath);
    }

    return '';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../team_members.php?msg=Invalid request');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ../../team_members.php?msg=Invalid member ID');
    exit;
}

$name = clean($conn, $_POST['name'] ?? '');
$designation = clean($conn, $_POST['designation'] ?? '');
$shortDescription = clean($conn, $_POST['short_description'] ?? '');
$linkedin = clean($conn, $_POST['linkedin_link'] ?? '');
$twitter = clean($conn, $_POST['twitter_link'] ?? '');
$email = clean($conn, $_POST['email_link'] ?? '');
$existingImage = clean($conn, $_POST['existing_image'] ?? '');
$imagePath = $existingImage;

if ($name === '' || $designation === '') {
    header('Location: ../../team_members.php?msg=Name and designation are required');
    exit;
}

if (!empty($_FILES['image']['name']) && (int) ($_FILES['image']['error'] ?? 1) === UPLOAD_ERR_OK) {
    $uploadDir = '../../assets/team/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        header('Location: ../../team_members.php?msg=Only JPG, JPEG, PNG and WEBP allowed');
        exit;
    }

    $fileName = 'team_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        header('Location: ../../team_members.php?msg=Image upload failed');
        exit;
    }

    $oldImageFsPath = resolveOldImagePath($existingImage);
    if ($oldImageFsPath !== '' && file_exists($oldImageFsPath)) {
        @unlink($oldImageFsPath);
    }

    $imagePath = 'admin/assets/team/' . $fileName;
}

$sql = "UPDATE team_members
        SET name = ?,
            designation = ?,
            short_description = ?,
            image = ?,
            linkedin_link = ?,
            twitter_link = ?,
            email_link = ?
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    header('Location: ../../team_members.php?msg=Database error');
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    'sssssssi',
    $name,
    $designation,
    $shortDescription,
    $imagePath,
    $linkedin,
    $twitter,
    $email,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../../team_members.php?msg=Team Member Updated');
    exit;
}

mysqli_stmt_close($stmt);
header('Location: ../../team_members.php?msg=Update failed');
exit;

