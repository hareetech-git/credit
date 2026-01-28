<?php
include '../config.php';

mysqli_query($conn,"
INSERT INTO service_why_choose_us (service_id, title, description)
VALUES (
".(int)$_POST['service_id'].",
'".$_POST['title']."',
'".$_POST['description']."'
)");

header("Location: ../../service_add.php?service_id=".$_POST['service_id']."&tab=why");
exit;
