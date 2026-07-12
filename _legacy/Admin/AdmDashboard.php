<?php 
include "../includes/database.php";
session_start();
if (isset($_SESSION["username"])) {
    $admin = $_SESSION['username'];
}else{
    echo "<script>
        window.location.replace('index.php')
    </script>";
}
$sql = "SELECT * FROM earnings";
$res = mysqli_query($conn, $sql);
$deps = 0;
foreach ($res as $row){
    $deps = $deps + $row['deposit'];
}
$sql = "SELECT * FROM earnings";
$res = mysqli_query($conn, $sql);
$withdraw = 0;
foreach ($res as $row){
    $withdraw = $withdraw + $row['withdraw'];
}
$sql = "SELECT * FROM earnings";
$res = mysqli_query($conn, $sql);
$bals = 0;
foreach ($res as $row){
    $bals = $bals + $row['balance'];
}
$sql = "SELECT * FROM users";
$res = mysqli_query($conn, $sql);
$users = mysqli_num_rows($res);
$sql = "SELECT * FROM users WHERE status='Inactive'";
$res = mysqli_query($conn, $sql);
$inactive = mysqli_num_rows($res);
$active = $users - $inactive;

function get_users_today($db){
    $timeStamp = time();
    $sql = "SELECT * FROM users";
    $res = mysqli_query($db, $sql);
    $joined = [];

    foreach ($res as $row) {
        $date = $row['date'];
        $date = strtotime($date);
        $diff =  $timeStamp - $date;
        $day = $diff / 86400;
        // echo $day.'<br/>';
        if ($day < 1) {
            $joined[] = $row;
        }
    }
    
    return count($joined);
}

$joinedToday = get_users_today($conn);


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://backend.payhero.co.ke/api/v2/wallets?wallet_type=service_wallet',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic alpwZW1ud3RiWnYzTVY1alYxcUU6S1FOeFpaWGhMUm1HVmo5ck9nU3BBRWVXOENSUDk0NVF4VWR2cUtBSA=='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
 $result = json_decode($response);
 //var_dump($result);
 $wallet = $result -> available_balance;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://backend.payhero.co.ke/api/v2/payment_channels/1585',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic alpwZW1ud3RiWnYzTVY1alYxcUU6S1FOeFpaWGhMUm1HVmo5ck9nU3BBRWVXOENSUDk0NVF4VWR2cUtBSA=='
  ),
));
 
 $response = curl_exec($curl);
 
 curl_close($curl);
//   echo $response;
  $result2 = json_decode($response, true);
 //  var_dump($result);
  $pwallet = $result2['balance_plain']['balance'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" 
     type="image/png" 
     href="home/[removal.ai]_d2c7b213-49fc-4a0e-b504-4f2e27babfcb-vex_logo.svg">
    <link rel="shortcut icon" type="logo" href="home/logij.png" />
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
   <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
   <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <title>Dashboard</title>
</head>
<body class="w3-sans-serif w3-light-gray">
    <div style="padding: 0;" class="w3-container">
        <div id="sidebar" class="w3-cell w3-padding w3-block w3-quarter w3-blue" style="width: fit-content; display: flex; flex-direction: column; align-items: start; overflow-x: hidden;">
            <div style="margin: 0; width: 100%;" class="w3-row w3-panel w3-margin-bottom w3-border-bottom w3-border-black w3-border-10">
            <p class="w3-cell w3-xxlarge">Admin</p>
            <p id="close" style="margin: 0;" class="w3-button w3-xxlarge w3-display-topright" onclick="document.getElementById('sideBar').display='none'">&times;</p>
            </div>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none w3-round-small" href="AdmDashboard.php"> <span><i class="fa-solid fa-wallet"></i></span> Dashboard</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="deposits.php"> <span><i class="fa fa-youtube-square" aria-hidden="true"></i></span> Deposits</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="withdrawals.php"> <span><i class="fa-solid fa-money-bill-trend-up"></i></span> Withdrawals</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="users.php"><span><i class="fa fa-share-square" aria-hidden="true"></i></span> Users</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="service.php"> <span><i class="fa-solid fa-square-poll-vertical"></i></span> Wallets</a>
            </div>
        <div style="padding: 0; margin: 0;" class="w3-light-gray w3-cell">
            <div style="height: auto; width: 100%; margin: 0;" class="w3-container w3-bar w3-row w3-card w3-white">
                    <div id="pussy" style="height: 100%; display: flex; align-self: center; flex-direction: row; align-items: center; justify-content: center;" class="w3-left w3-margin-top">
                        <button id="btn-hide" class="w3-button w3-left"><i class="fa-solid fa-bars"></i></button>
                        <p id="txt-hide" style="font-weight:bold;" class="w3-large w3-bar-item w3-text-green">Bazarin Technologies</p>
                    </div>
                    
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="w3-right w3-margin">
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="w3-border-right w3-border-blue-gray">
                            <p class="w3-text-grey w3-hover-text-amber w3-margin w3-medium"><i class="fa-solid fa-bell"></i></p>
                            <p class="w3-text-grey w3-hover-text-amber w3-margin w3-medium"><i class="fa-solid fa-envelope"></i></p>
                        </div>
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="">
                            <p style="margin: 0;" class="w3-small w3-margin">account</p>
                            <a style="text-decoration: none;" href="mains/account.php" class="w3-hover-text-amber w3-large"><i class="fa-regular fa-user"></i></a>
                        </div>
                    </div>
                </div>
            <div style="margin: 0;"  class="w3-container w3-padding-24 ">
                <h1 style="font-weight: bold;" class="w3-left-align w3-xlarge">Admin Dashboard</h1>
            <div class="w3-row w3-centered w3-center">
                <button style="margin: 5px; max-width: fit-content;" id="dep" class="w3-btn w3-round w3-third  w3-blue">Deposits</button>
                <button style="margin: 5px; max-width: fit-content;"  id="with" class="w3-btn w3-round w3-third  w3-teal">Withdrawals</button>
                <button style="margin: 5px; max-width: fit-content;"  id="team" class="w3-btn w3-round w3-third  w3-light-green">Users</button>
                <button style="margin: 5px; max-width: fit-content;"   id="inv" class="w3-btn w3-round w3-third  w3-yellow">Services</button>
            </div>
            <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-purple w3-card w3-margin w3-white">
                    <div class="w3-block">
                        <p class="w3-text-green">Payment Balance</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"> Kes <?=$pwallet?> </p>
                    </div>
                    <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa fa-money" aria-hidden="true"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-blue w3-card w3-margin w3-white">
                    <div class="w3-block">
                        <p class="w3-text-green">Service Wallet</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"> Kes<?=$wallet?></p>
                        </div>
                        <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-money-check-dollar"></i></span>
                    
                    
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-blue w3-card w3-margin w3-white">
                    <div class="w3-block">
                        <p class="w3-text-green">Total Withdrawals</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"> Kes <?=$withdraw?></p>
                        </div>
                        <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-money-check-dollar"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-yellow w3-card w3-margin w3-white">
                    <div class="w3-block"><p class="w3-text-green">Total account Balances</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"> Kes <?=$bals?></p>
                        </div>
                        <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-money-bill-trend-up"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-amber w3-card w3-margin w3-white">
                    <div class="w3-block"><p class="w3-text-green">Total deposits</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"> Kes <?=$deps?> </p>
                    </div>
                    <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-money-bill-1-wave"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-purple w3-card w3-margin w3-white">
                    <div class="w3-block"><p class="w3-text-green">Total active users</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"><?=$active?></p>
                    </div>
                    <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-users"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-green w3-card w3-margin w3-white">
                    <div class="in-cont"><p class="w3-text-green">Total Users</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"><?=$users?></p>
                    </div>
                    <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-user"></i></span>
                </div>
                <div class="w3-cell w3-display-container w3-mobile w3-third w3-padding w3-leftbar w3-round-medium w3-border-green w3-card w3-margin w3-white">
                    <div class="in-cont"><p class="w3-text-green">Total Joined Today</p>
                        <p style="font-weight: bold;" class="w3-text-gray w3-large"><?=$joinedToday?></p>
                    </div>
                    <span class="w3-right w3-margin w3-text-grey w3-display-right w3-xxlarge"><i class="fa-solid fa-user"></i></span>
                </div>
            
            </div>
        
            </div>
        </div>
    </div>               
  <script>
     const btnInv = document.getElementById('inv')
     const btnDep = document.getElementById('dep')
     const btnWith = document.getElementById('with')
     const btnTeam = document.getElementById('team')
     btnInv.addEventListener('click', () => {
        location.replace('dep.php')
     })
     btnDep.addEventListener('click', () => {
        location.replace('deposits.php')
     })
     btnWith.addEventListener('click', () => {
        location.replace('withdrawals.php')
     })
     btnTeam.addEventListener('click', () => {
        location.replace('users.php')
     })
     
     document.getElementById('btn-hide').style.display = 'none'
     const closeBtn = document.getElementById('close')
     closeBtn.style.display = 'none'
     let width = document.documentElement.clientWidth
     if (width < 650) {
        document.getElementById('sidebar').style.display = 'none'
        document.getElementById('btn-hide').style.display = 'block'
        document.getElementById('txt-hide').style.display = 'none'
        document.querySelector('#pussy').classList.add('w3-blue')
        closeBtn.style.display = 'block'
        
     }
     const btnHide = document.querySelector('#btn-hide')
     btnHide.addEventListener('click', function () {
        document.querySelector('#sidebar').style.display = 'block'
        document.querySelector('#sidebar').style.maxWidth = '55vw'
        document.querySelector('#sidebar').classList.add('w3-sidebar')
       
     })
   
     closeBtn.addEventListener('click', function () {
        document.getElementById('sidebar').style.display = 'none'
     })
  </script>
</body>
</html>