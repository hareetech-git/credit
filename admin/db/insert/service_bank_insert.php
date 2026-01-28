<?php
include '../config.php';

mysqli_query($conn,"
INSERT INTO service_banks (service_id, bank_key, bank_value)
VALUES (
".(int)$_POST['service_id'].",
'".$_POST['bank_key']."',
'".$_POST['bank_value']."'
)");

header("Location: ../../service_add.php?service_id=".$_POST['service_id']."&tab=banks");
exit;
