<?php
session_start();
include '../../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

function clean($conn, $value) {
    return mysqli_real_escape_string($conn, trim((string) $value));
}

function slugify($text) {
    $text = strtolower(trim((string) $text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim((string) $text, '-');
}

function uniqueSlug($conn, $baseSlug) {
    $slug = $baseSlug !== '' ? $baseSlug : 'blog';
    $safe = mysqli_real_escape_string($conn, $slug);
    $res = mysqli_query($conn, "SELECT id FROM blogs WHERE slug = '$safe' LIMIT 1");
    if ($res && mysqli_num_rows($res) === 0) {
        return $slug;
    }

    $n = 2;
    while (true) {
        $try = $slug . '-' . $n;
        $trySafe = mysqli_real_escape_string($conn, $try);
        $exists = mysqli_query($conn, "SELECT id FROM blogs WHERE slug = '$trySafe' LIMIT 1");
        if ($exists && mysqli_num_rows($exists) === 0) {
            return $try;
        }
        $n++;
    }
}

function normalizeImageExt($filename) {
    $ext = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
    return $ext === 'acif' ? 'avif' : $ext;
}

$title = trim((string) ($_POST['title'] ?? ''));
$slugRaw = trim((string) ($_POST['slug'] ?? ''));
$shortDescription = trim((string) ($_POST['short_description'] ?? ''));
$content = trim((string) ($_POST['content'] ?? ''));
$status = ((int) ($_POST['status'] ?? 1) === 1) ? 1 : 0;
$adminId = (int) $_SESSION['admin_id'];

if ($title === '' || trim(strip_tags($content)) === '') {
    header("Location: ../../blog_add.php?err=Title and content are required");
    exit;
}

$baseSlug = slugify($slugRaw !== '' ? $slugRaw : $title);
$finalSlug = uniqueSlug($conn, $baseSlug);

if ($shortDescription === '') {
    $plain = trim(strip_tags($content));
    $shortDescription = strlen($plain) > 180 ? substr($plain, 0, 180) . '...' : $plain;
}

$imagePath = '';
if (!empty($_FILES['featured_image']['name'])) {
    $ext = normalizeImageExt($_FILES['featured_image']['name']);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'jfif'];
    if (!in_array($ext, $allowed, true)) {
        header("Location: ../../blog_add.php?err=Invalid image format");
        exit;
    }

    $uploadDir = "../../../uploads/blog/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = "blog_" . time() . "_" . rand(100, 999) . "." . $ext;
    $target = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
        $imagePath = "uploads/blog/" . $fileName;
    }
}

$titleSafe = clean($conn, $title);
$slugSafe = clean($conn, $finalSlug);
$shortSafe = clean($conn, $shortDescription);
$contentSafe = clean($conn, $content);
$imageSafe = clean($conn, $imagePath);

$sql = "INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by)
        VALUES ('$titleSafe', '$slugSafe', '$shortSafe', '$contentSafe', '$imageSafe', $status, $adminId)";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../blogs.php?msg=Blog added successfully");
    exit;
}

header("Location: ../../blog_add.php?err=Failed to add blog");
exit;
?>
