<?php
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "bazarin";
$dbName = "agridigital";
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if ($conn) {
    # code...
}else{
    die("Sorry not connected");
}