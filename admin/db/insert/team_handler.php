<?php
include '../../db/config.php';

function clean($conn, $str) {
    return mysqli_real_escape_string($conn, trim($str));
}

$name        = clean($conn, $_POST['name']);
$designation = clean($conn, $_POST['designation']);
$desc        = clean($conn, $_POST['short_description']);
$linkedin    = clean($conn, $_POST['linkedin_link']);
$twitter     = clean($conn, $_POST['twitter_link']);
$email       = clean($conn, $_POST['email_link']);

$image_path = '';

if (!empty($_FILES['image']['name'])) {

    $upload_dir = "../../assets/team/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (in_array($ext, $allowed)) {
        $file_name = "team_" . time() . "_" . rand(100,999) . "." . $ext;
        $target = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = "admin/assets/team/" . $file_name;
        }
    }
}

$sql = "INSERT INTO team_members 
(name, designation, short_description, image, linkedin_link, twitter_link, email_link)
VALUES 
('$name', '$designation', '$desc', '$image_path', '$linkedin', '$twitter', '$email')";

mysqli_query($conn, $sql);

header("Location: ../../team_members.php?msg=Team Member Added");
exit;
