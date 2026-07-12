<?php 
include 'includes/database.php';
$stkCallback = file_get_contents('php://input');
$logFile = "mpesastkresponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallback);
fclose($log);
echo $stkCallback;
$data = json_decode($stkCallback, false);

$email = isset($data->TransactionReference) ? mysqli_real_escape_string($conn, $data->TransactionReference) : '';
$amount = isset($data->TransactionAmount) ? mysqli_real_escape_string($conn, $data->TransactionAmount) : '';
$phone = isset($data->Msisdn) ? mysqli_real_escape_string($conn, $data->Msisdn) : '';
$transactionID = isset($data->TransactionReceipt) ? mysqli_real_escape_string($conn, $data->TransactionReceipt) : '';
$status = isset($data->ResponseCode) ? mysqli_real_escape_string($conn, $data->ResponseCode) : '';

$sql = "SELECT * FROM users WHERE email='".$email."'";
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
//To check if the status is success, to perform database actions
 if($status == 0){
// SQL query for insertion
$status = "Success";
    $sql = "INSERT INTO `transaction` (`email`, `phone`, `amount`, `type`, `status`) 
            VALUES ('".$email."', '".$phone."', '".$amount."', 'Deposit', '".$status."')";
    $res = mysqli_query($conn, $sql);
    $sql2 = "SELECT * FROM earnings WHERE email = '".$email."'";
    $res2 = mysqli_query($conn, $sql2);
    $res2 = $res2 -> fetch_array();
    $balance = $res2['balance'];
    $deposit = $res2['deposit'];
    $balance = $balance + $amount;
    $deposit = $deposit + $amount;
    $refT = $amount * 0.08;
    $refBalance = $refBalance + $refT;
    $refEarn = $refEarn + $refT;
    //updates both users and sponsor
    $sql = "UPDATE earnings SET deposit=$deposit, balance=$balance WHERE email='".$email."'";
    $res = mysqli_query($conn, $sql);
    // $sql = "UPDATE users SET status='Active' WHERE email='".$uname."'";
    // $res = mysqli_query($conn, $sql);
    $sql2 = "UPDATE earnings SET referral=$refEarn, balance=$refBalance WHERE email='".$refUname."'";
    $res2 = mysqli_query($conn, $sql2);
    // Close the connection
    mysqli_close($conn);
 }
