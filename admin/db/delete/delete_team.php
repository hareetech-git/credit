<?php
include '../../db/config.php';

$id = (int)$_GET['id'];

mysqli_query($conn, "DELETE FROM team_members WHERE id = $id");

header("Location: ../../team_members.php?msg=Deleted Successfully");
exit;
