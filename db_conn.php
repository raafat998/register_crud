<?php


$server_name = "localhost";
$user_name = "root";
$password = "";
$db_name = "form_validation";
$port = 3306;

$conn = mysqli_connect($server_name , $user_name , $password , $db_name,$port);

if(!$conn){

    die("Connection failed".mysqli_connect_error());
}
//  echo "Connected Successfully";










?>