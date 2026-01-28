<?php
include '../config.php';

mysqli_query($conn,"
INSERT INTO service_fees_charges (service_id, fee_key, fee_value)
VALUES (
".(int)$_POST['service_id'].",
'".$_POST['fee_key']."',
'".$_POST['fee_value']."'
)");

header("Location: ../../service_add.php?service_id=".$_POST['service_id']."&tab=fees");
exit;
