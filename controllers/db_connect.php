<?php
$server = "localhost";
$username = "root";
$password = "";
$db = "evote";

$conn = mysqli_connect($server, $username, $password, $db);
if(!$conn){
    echo ("Connection Error".mysqli_connect_error($conn));
}
?>