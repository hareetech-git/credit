<?php
// Make sure connection exists
if (!isset($conn)) {
    include 'config.php';
}

$webSettings = [
    'site_phone'   => '',
    'site_email'   => '',
    'site_address' => '',
    'hr_email' => ''
];

$result = mysqli_query($conn, "SELECT * FROM web_settings LIMIT 1");

if ($result && mysqli_num_rows($result) > 0) {
    $webSettings = mysqli_fetch_assoc($result);
}
?>
