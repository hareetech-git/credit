<?php
session_start();
include '../../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../index.php?err=Login required");
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../../blogs.php?err=Invalid blog ID");
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

function uniqueSlugForUpdate($conn, $slugBase, $id) {
    $slug = $slugBase !== '' ? $slugBase : 'blog';
    $safe = mysqli_real_escape_string($conn, $slug);
    $res = mysqli_query($conn, "SELECT id FROM blogs WHERE slug = '$safe' AND id != $id LIMIT 1");
    if ($res && mysqli_num_rows($res) === 0) {
        return $slug;
    }

    $n = 2;
    while (true) {
        $try = $slug . '-' . $n;
        $trySafe = mysqli_real_escape_string($conn, $try);
        $exists = mysqli_query($conn, "SELECT id FROM blogs WHERE slug = '$trySafe' AND id != $id LIMIT 1");
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
$existingImage = trim((string) ($_POST['existing_image'] ?? ''));

if ($title === '' || trim(strip_tags($content)) === '') {
    header("Location: ../../blog_edit.php?id=$id&err=Title and content are required");
    exit;
}

$baseSlug = slugify($slugRaw !== '' ? $slugRaw : $title);
$finalSlug = uniqueSlugForUpdate($conn, $baseSlug, $id);

if ($shortDescription === '') {
    $plain = trim(strip_tags($content));
    $shortDescription = strlen($plain) > 180 ? substr($plain, 0, 180) . '...' : $plain;
}

$imagePath = $existingImage;
if (!empty($_FILES['featured_image']['name'])) {
    $ext = normalizeImageExt($_FILES['featured_image']['name']);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'jfif'];
    if (!in_array($ext, $allowed, true)) {
        header("Location: ../../blog_edit.php?id=$id&err=Invalid image format");
        exit;
    }

    $uploadDir = "../../../uploads/blog/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = "blog_" . time() . "_" . rand(100, 999) . "." . $ext;
    $target = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
        if ($existingImage !== '' && file_exists("../../../" . $existingImage)) {
            @unlink("../../../" . $existingImage);
        }
        $imagePath = "uploads/blog/" . $fileName;
    }
}

$titleSafe = clean($conn, $title);
$slugSafe = clean($conn, $finalSlug);
$shortSafe = clean($conn, $shortDescription);
$contentSafe = clean($conn, $content);
$imageSafe = clean($conn, $imagePath);

$sql = "UPDATE blogs
        SET title = '$titleSafe',
            slug = '$slugSafe',
            short_description = '$shortSafe',
            content = '$contentSafe',
            featured_image = '$imageSafe',
            status = $status,
            updated_at = NOW()
        WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../blogs.php?msg=Blog updated successfully");
    exit;
}

header("Location: ../../blog_edit.php?id=$id&err=Failed to update blog");
exit;
?>
