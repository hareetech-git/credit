<?php
include 'config.php';

$data=json_decode(file_get_contents("php://input"),true);
$id=(int)$data['id'];

$imgs=mysqli_query($conn,"SELECT img FROM services_imgs WHERE service_id=$id");
while($i=mysqli_fetch_assoc($imgs)){
    if(file_exists('../'.$i['img'])) unlink('../'.$i['img']);
}

mysqli_query($conn,"DELETE FROM services_imgs WHERE service_id=$id");
mysqli_query($conn,"DELETE FROM services WHERE id=$id");

echo json_encode(['success'=>true]);
