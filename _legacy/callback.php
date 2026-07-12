<?php
include 'includes/database.php';

// Get the JSON data from the request
$response = file_get_contents('php://input');
$data = json_decode($response, true);

// Log incoming request to a file
file_put_contents("messages.log", print_r($data, true));

// Log incoming request with a timestamp
$logFile = fopen("messages.log", "a");
fwrite($logFile, "\r\n\r\n\r\n\r\n***** PROD " . date('d M Y : H:s:i') . " *****\r\n\r\n");
fwrite($logFile, print_r($data, true));
fclose($logFile);

// Validate and sanitize inputs
$time = time();
$uname = $data['response']['ExternalReference'];
$MPESA_Reference = $data['response']['MpesaReceiptNumber'];
$status = $data['response']['Status'];
$phone = $data['response']['Phone'];
$amount = $data['response']['Amount'];

//user details
$sql = "SELECT * FROM users WHERE email='".$uname."'";
$res = mysqli_query($conn, $sql);
$res = $res -> fetch_array();
$sponsor = $res['refer'];
//sponsor details
$sql = "SELECT * FROM users WHERE ID='".$sponsor."'";
$res = mysqli_query($conn, $sql);
$res = $res -> fetch_array();
$refUname = $res['email'];
$sql3 = "SELECT * FROM earnings WHERE email='".$refUname."'";
$res3 = mysqli_query($conn, $sql3);
$res3 = $res3 -> fetch_array();
$refBalance = $res3['balance'];
$refEarn = $res3['referral'];
//Convert amount to usd
$amount = $amount;
//To check if the status is success, to perform database actions
 if($status == "Success"){
// SQL query for insertion
$sql = "INSERT INTO `transaction` (`email`, `phone`, `amount`, `type`, `status`) 
        VALUES ('".$uname."', '".$phone."', '".$amount."', 'Deposit', '".$status."')";
$res = mysqli_query($conn, $sql);
$sql2 = "SELECT * FROM earnings WHERE email = '".$uname."'";
$res2 = mysqli_query($conn, $sql2);
$res2 = $res2 -> fetch_array();
$balance = $res2['balance'];
$deposit = $res2['deposit'];
$balance = $balance + $amount;
$deposit = $deposit + $amount;
$refT = $amount * 0.1;
$refBalance = $refBalance + $refT;
$refEarn = $refEarn + $refT;
//updates both users and sponsor
$sql = "UPDATE earnings SET deposit=$deposit, balance=$balance WHERE email='".$uname."'";
$res = mysqli_query($conn, $sql);
$sql = "UPDATE users SET status='Active' WHERE email='".$uname."'";
$res = mysqli_query($conn, $sql);
$sql2 = "UPDATE earnings SET referral=$refEarn, balance=$refBalance WHERE email='".$refUname."'";
$res2 = mysqli_query($conn, $sql2);
// Close the connection
mysqli_close($conn);
 }
