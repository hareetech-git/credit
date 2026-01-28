<?php
include '../config.php';

mysqli_query($conn,"
INSERT INTO service_documents (service_id, doc_name, disclaimer)
VALUES (
".(int)$_POST['service_id'].",
'".$_POST['doc_name']."',
'".$_POST['disclaimer']."'
)");

header("Location: ../../service_add.php?service_id=".$_POST['service_id']."&tab=documents");
exit;
