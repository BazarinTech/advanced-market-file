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
    $refferal = $res2['referral'];
    $error = '';
    $msg = ''; 
    $sql3 = "SELECT * FROM users WHERE refer='".$userID."'";
    $res3 = mysqli_query($conn, $sql3);
    $active = array();
    $db = $conn;
    $downline = mysqli_num_rows($res3);
    foreach($res3 as $row){
        if ($row['status'] != "Inactive") {
            array_push($active, $row);
        }
    }
    $numActive = count($active);
    
        if(isset($_POST['sfl1'])){
        $package = "SFL 1";
        $amount = 200;
        $daily = 20;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
        if(isset($_POST['sfl2'])){
        $package = "SFL 2";
        $amount = 500;
        $daily = 40;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    
    if(isset($_POST['ufl1'])){
        $package = "UFL 1";
        $amount = 750;
        $daily = 80;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl2'])){
        $package = "UFL 2";
        $amount = 2500;
        $daily = 180;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl3'])){
        $package = "UFL 3";
        $amount = 5000;
        $daily = 400;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl4'])){
        $package = "UFL 4";
        $amount = 10000;
        $daily = 850;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl5'])){
        $package = "UFL 5";
        $amount = 15000;
        $daily = 1300;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl6'])){
        $package = "UFL 6";
        $amount = 20000;
        $daily = 1750;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl7'])){
        $package = "UFL 7";
        $amount = 24000;
        $daily = 2000;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    if(isset($_POST['ufl8'])){
        $package = "UFL 8";
        $amount = 30000;
        $daily = 2800;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
        if(isset($_POST['ufl9'])){
        $package = "UFL 9";
        $amount = 40000;
        $daily = 3850;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
        if(isset($_POST['ufl10'])){
        $package = "UFL 10";
        $amount = 80000;
        $daily = 7950;
        $days = 20;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
        if(isset($_POST['ufl11'])){
        $package = "UFL 11";
        $amount = 100000;
        $daily = 10000;
        $days = 30;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
        if(isset($_POST['ufl12'])){
        $package = "UFL 12";
        $amount = 150000;
        $daily = 15000;
        $days = 30;
        $total = $days * $daily;
        if($balance >= $amount) {
            $balance = $balance - $amount;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            
            $sql = "UPDATE users SET status='Active' WHERE email='".$email."'";
    $res = $db->query($sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package bought succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';
        }else {
            $error = "Insuficient balance to invest with this package. Kindly top up your account and try again.";
            echo '<script type="text/javascript">
                    alert("Insuficient balance to invest with this package!!")
                   </script>';
            echo '<script type="text/javascript">
                   setTimeout(function () {
                       // Hide the preloader
                       window.location.replace("deposit")
                     }, 1000);
               </script>';
        }
    }
    $dollar = 2695.98
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Home</title>
    <style>
               body{
  display: flex;
  flex-direction: column;
  /* align-items: center; */
  justify-content: center;
  background-color: lightgrey;
  max-width: 600px;
        }
    </style>
</head>
<body>
    <div style="background-color: green; width: 100%;height: 7vh;display: flex;align-items: center;justify-content: center;" class="w3-container w3-card w3-text-white">
        <div style="display: flex;align-items: center;justify-content: center" class="w3-panel">
            <p style='margin: 0' class='w3-large'>M-Vase Farmers</p>
        </div>
    </div>
    <div style="padding: 0;height: 40vh;width: 100%;" class="w3-container">
        <img src="images/banner-ac.jpeg" width="100%" style="height: 100%;" class="w3-image" alt="">
    </div>
    <div style="padding: 0;display: flex;align-items: center;justify-content: center;" class="w3-container">
        <div style="display: flex; flex-direction: row; justify-content: center; align-items: center;height: 6vh;margin: 0; width: 100vw;" class="w3-panel w3-row w3-round-large">
            <div class="w3-col s1">
                <p style="z-index: 100;" class="w3-text-blue w3-center w3-circle"><i class="fa-solid fa-volume-high"></i></p>
            </div>
            <marquee  class="w3-text-green" behavior="" direction="left">Welcome to the future of farming. Invest digitaly earn physically!!</marquee>
        </div>
    </div>
    <div style="display: flex;justify-content: center;align-items: center;flex-direction: row;padding: 5px;width: 100%;height: 35vh;" class="w3-container">
        <div class="w3-half">
            <div style="background-color: green;height: 100%;padding-right: 0;margin: 0;" class="w3-panel w3-round-large w3-card">
                <p class="w3-text-white">Running Balance</p>
                <p class="w3-large w3-text-white">Kes<?=number_format($balance, 2)?></p>
                <p class="w3-small w3-text-white">Withdrawable funds<br>Are displayed here</p>
                <img src="images/ban1.png" width="30%" class="w3-right" alt="">
            </div>
        </div>
      
        <div style="margin-left: 10px;padding-top: 5px;" class="w3-half">
            <div id="deposit" style="background-color: green;height: fit-content;margin-top: 10px;padding-right: 0;text-decoration: none;" class="w3-round-large w3-card w3-panel">
                <p class="w3-text-white">Deposit</p>
                <img src="images/ban2.png" width="30%" style="height: 100%;" class="w3-right" alt="">
            </div>
            <div id="withdraw" style="background-color: green;height: fit-content;margin-top: 10px;padding-right: 0;text-decoration: none;" class="w3-round-large w3-card w3-panel">
                <p class="w3-text-white">Withdraw</p>
                <img src="images/ban3.png" width="30%" style="height: 100%;" class="w3-right" alt="">
            </div>
        </div>
    </div>
    <div class="w3-container">
        <p style="padding: 5px; font-weight: bold;" class="w3-leftbar w3-border-blue w3-margin w3-text-blue">Farm Products</p>
    </div>
    <!--    <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px" class="w3-margin-bottom">-->
    <!--    <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">-->
    <!--        <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">SFL 1</p>-->
    <!--        <img style="height: 20%;" class="w3-round-large" width="100%" src="images/sfl1.webp" alt="">-->
    <!--        <p class="w3-text-amber">Price: Kes 200</p>-->
    <!--        <p class="w3-small w3-text-white">Cycle: 20days</p>-->
    <!--        <p  class="w3-small w3-text-white">Daily income: kes 20</p>-->
    <!--        <p class="w3-small w3-text-white">Total income: Kes 400</p>-->
    <!--        <button name="sfl1" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>-->
    <!--    </form>-->
    <!--    <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">-->
    <!--        <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">SFL 2</p>-->
    <!--        <img class="w3-image w3-round-large" width="100%" src="images/sfl2.webp" alt="">-->
    <!--        <p class="w3-text-amber">Price: Kes 500</p>-->
    <!--        <p class="w3-small w3-text-white">Cycle: 20days</p>-->
    <!--        <p  class="w3-small w3-text-white">Daily income: Kes 40</p>-->
    <!--        <p class="w3-small w3-text-white">Total income: Kes 800</p>-->
    <!--        <button name="sfl2" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>-->
    <!--    </form>-->
    <!--</div>-->
    <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px" class="w3-margin-bottom">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 1</p>
            <img style="height: 20%;" class="w3-round-large" width="100%" src="images/1.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 750</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: kes 80</p>
            <p class="w3-small w3-text-white">Total income: Kes 1,600</p>
            <button name="ufl1" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 2</p>
            <img class="w3-image w3-round-large" width="100%" src="images/2.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 2,500</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 180</p>
            <p class="w3-small w3-text-white">Total income: Kes 3,600</p>
            <button name="ufl2" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
    </div>
    <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px" class="w3-margin-bottom">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 3</p>
            <img class="w3-image w3-round-large" width="100%" src="images/3.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 5,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 400</p>
            <p class="w3-small w3-text-white">Total income: Kes 8,000</p>
            <button name="ufl3" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 4</p>
            <img class="w3-image w3-round-large" width="100%" src="images/4.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 10,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 850</p>
            <p class="w3-small w3-text-white">Total income: Kes 17,000</p>
            <button name="ufl4" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
    </div>
    <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px" class="w3-margin-bottom">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 5</p>
            <img class="w3-image w3-round-large" width="100%" src="images/5.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 15,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 1,300</p>
            <p class="w3-small w3-text-white">Total income: Kes 26,000</p>
            <button name="ufl5" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 6</p>
            <img class="w3-image w3-round-large" width="100%" src="images/6.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 20,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 1,750</p>
            <p class="w3-small w3-text-white">Total income: Kes 35,000</p>
            <button name="ufl6" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
    </div>
    <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px;" class="">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 7</p>
            <img class="w3-image w3-round-large" width="100%" src="images/7.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 24,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 2,000</p>
            <p class="w3-small w3-text-white">Total income: Kes 40,000</p>
            <button name="ufl7" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 8</p>
            <img class="w3-image w3-round-large" width="100%" src="images/8.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 30,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 2,800</p>
            <p class="w3-small w3-text-white">Total income: Kes 56,000</p>
            <button name="ufl8" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
    </div>
        <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px;" class="">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 9</p>
            <img class="w3-image w3-round-large" width="100%" src="images/7.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 40,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 3,850</p>
            <p class="w3-small w3-text-white">Total income: Kes 77,000</p>
            <button name="ufl9" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 10</p>
            <img class="w3-image w3-round-large" width="100%" src="images/8.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 80,000</p>
            <p class="w3-small w3-text-white">Cycle: 20days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 7,950</p>
            <p class="w3-small w3-text-white">Total income: Kes 159,000</p>
            <button name="ufl10" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
    </div>
        <div style="display: flex; flex-direction: row; align-items: center;justify-content: center; width: 100%;padding: 5px;margin-bottom: 100px;" class="">
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-margin-right w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 11</p>
            <img class="w3-image w3-round-large" width="100%" src="images/7.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 100,000</p>
            <p class="w3-small w3-text-white">Cycle: 30days</p>
            <p  class="w3-small w3-text-white">Daily income: Ks 10,000</p>
            <p class="w3-small w3-text-white">Total income: Kes 300,000</p>
            <button name="ufl11" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
        <form style="background-color: green;" action="home" method="post" class="w3-half w3-border w3-border-yellow w3-card w3-round-large w3-padding">
            <p style="font-weight: bold; margin-bottom: 10px;" class="w3-large w3-center w3-text-white">UFL 12</p>
            <img class="w3-image w3-round-large" width="100%" src="images/8.jpeg" alt="">
            <p class="w3-text-amber">Price: Kes 150,000</p>
            <p class="w3-small w3-text-white">Cycle: 30days</p>
            <p  class="w3-small w3-text-white">Daily income: Kes 15,000</p>
            <p class="w3-small w3-text-white">Total income: Kes 450,000</p>
            <button name="ufl12" style="width: 100%;" class="w3-btn w3-yellow w3-hover-blue w3-round-large">Buy</button>
        </form>
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
    <script>
    $.noConflict()
        jQuery(document).ready(function($){
            $('#deposit').click(function(){
                window.location.replace('deposit')
            })
            $('#withdraw').click(function(){
                window.location.replace('withdraw')
            })
        })
    </script>
</body>
</html>