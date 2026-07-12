<?php 
    session_start();
    include 'includes/database.php';
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
    }else{
        echo "<script>
            window.location.replace('login')
        </script>";
    }
    $sql = "SELECT * FROM users WHERE email='".$email."'";
    $res = mysqli_query($conn, $sql);
    $res = $res -> fetch_array();
    $email = $res['email'];
    $phone = $res['phone'];
    $userID = $res['ID'];
    $upline = $res['refer'];
    $sql2 = "SELECT * FROM earnings WHERE email='".$email."'";
    $res2 = mysqli_query($conn, $sql2);
    $res2 = $res2 -> fetch_array();
    $balance = $res2['balance'];
    $roll = $res2['roll'];
    $deposits = $res2['deposit'];
    $withdraw = $res2['withdraw'];
    $total = $res2['totals'];
    $referral = $res2['referral'];
    $error = '';
    $msg = '';
    $sql3 = "SELECT * FROM users WHERE refer='".$userID."'";
    $res3 = mysqli_query($conn, $sql3);
    $active = array();
    $downline = mysqli_num_rows($res3);
    foreach($res3 as $row){
        if ($row['status'] != "Inactive") {
            array_push($active, $row);
        }
    }
    $product = 0;
    $numActive = count($active);
    $sql = "SELECT * FROM orders WHERE email='".$email."'";
    $res = $conn -> query($sql);
    foreach ($res as $row) {
        $product += $row['earnings'];
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Account</title>
    <style>
               body{
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: lightgrey;
  max-width: 600px;
        }
    </style>
</head>
<body>
    <div style="background-color: green; width: 100vw;height: 7vh;display: flex;align-items: center;justify-content: center;margin-bottom: 20px;" class="w3-container w3-card  w3-text-white">
        <div class="w3-panel">
            <p class="w3-xlarge">User Account</p>
        </div>
    </div>
    <div style="background-color: green;display: flex;align-items: center;justify-content: center;width: 100%;" class="w3-container w3-border-bottom w3-round-large">
        <div class="w3-panel w3-third">
            <P class="w3-text-yellow w3-center">Balance</P>
            <p class="w3-text-white w3-center">Kes <?=number_format($balance, 2)?></p>
        </div>
        <div class="w3-panel w3-border-left w3-border-yellow w3-third">
            <P class="w3-text-yellow w3-center">Deposits</P>
            <p class="w3-text-white w3-center">Kes <?=number_format($deposits, 2)?></p>
        </div>
        <div class="w3-panel w3-border-left w3-border-yellow w3-third">
            <P class="w3-text-yellow w3-center">Withdrawals</P>
            <p class="w3-text-white w3-center">Kes <?=number_format($withdraw, 2)?></p>
        </div>
    </div>
    <div style="width: 100%;margin-bottom: 100px;margin-top: 50x;" class="w3-container">
        <a href="deposit" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center; height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-money-bill"></i></p>
                <p class="w3-padding w3-text-green"> Deposit</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="transaction" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-receipt"></i></p>
                <p class="w3-padding w3-text-green"> Transactions</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="withdraw" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-money-bill-transfer"></i></p>
                <p class="w3-padding w3-text-green"> Withdraw</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="user" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-user-tie"></i></p>
                <p class="w3-padding w3-text-green"> User details</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="team" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-share-nodes"></i></p>
                <p class="w3-padding w3-text-green"> Team</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="https://chat.whatsapp.com/J5tjMtv7yJ5GHQw8djOEuq" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-brands fa-whatsapp"></i></p>
                <p class="w3-padding w3-text-green"> WhatsApp Group</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="https://wa.me/254797440372?" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-green w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-solid fa-circle-info"></i></p>
                <p class="w3-padding w3-text-green"> Support</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right w3-text-green"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
        <a href="https://apk.e-droid.net/apk/app3878872-0cgrip.apk?v=2" id="edit" style="text-decoration: none; display: flex;flex-direction: row;justify-content: space-between; margin: 0;align-items: center;height: 9vh;" class="w3-border-bottom w3-border-blue w3-text-white w3-padding">
            <span style="display: flex;justify-content: center;align-items: center;" class="w3-left">
                <p class="w3-large w3-text-blue"><i class="fa-brands fa-telegram"></i></p>
                <p class="w3-padding"> Download App</p>
            </span>
            <span style="float: right;" class="w3-right">
                <p class="w3-right"><i class="fa-solid fa-angle-right"></i></p>
            </span> 
        </a>
    </div>
    <div style="background-color: green;display: flex;align-items: center;" class="w3-container w3-bottom w3-card w3-bottombar">
        <a href="home" style="display: flex;flex-direction: column;justify-content: center;align-items: center;text-decoration: none;" class="w3-block w3-bar-item w3-padding">
            <img src="images/home.png" width="20%" alt="">
            <p style="margin: 0;" class="w3-text-white">Home</p>
        </a>
        <a href="task" style="display: flex;flex-direction: column;justify-content: center;align-items: center;text-decoration: none;" class="w3-block w3-bar-item w3-padding">
            <img src="images/order.png" width="20%" alt="">
            <p style="margin: 0;" class="w3-text-white">Order</p>
        </a>
        <a href="account" style="display: flex;flex-direction: column;justify-content: center;align-items: center;text-decoration: none;" class="w3-block w3-bar-item w3-padding">
            <img src="images/mine.png" width="20%" alt="">
            <p style="margin: 0;" class="w3-text-white">Mine</p>
        </a>
    </div>
</body>
</html>