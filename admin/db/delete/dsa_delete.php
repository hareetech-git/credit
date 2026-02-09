<?php
include '../config.php';

if (isset($_GET['id'])) {
    $dsa_id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM dsa_user_permissions WHERE dsa_id = $dsa_id");
    mysqli_query($conn, "DELETE FROM dsa_profiles WHERE dsa_id = $dsa_id");

    if (mysqli_query($conn, "DELETE FROM dsa WHERE id = $dsa_id")) {
        header('Location: ../../dsa_list.php?msg=DSA deleted successfully');
        exit;
    }
}

header('Location: ../../dsa_list.php?error=Unable to delete DSA');
exit;
?>
