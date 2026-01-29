<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "credit";
$port = 3307;
$conn = mysqli_connect($servername, $username, $password, $dbname , $port);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

?>




