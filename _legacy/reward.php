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
    header("Location: home");
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
    $numActive = count($active);
    if (isset($_POST['r1'])) {
        if($numActive >= 10){
            $amount = 1000;
            $sql = "INSERT INTO `transaction` (`email`, `type`, `amount`, `status`, `phone`, `details`) 
            VALUES ('".$email."', 'Reward', '1000', 'Success', '".$phone."', 'r1')";
        $res = mysqli_query($conn, $sql);
        //update balance and deposits of the user
        $balance = $balance + $amount;
        $sql = "UPDATE earnings SET balance=$balance WHERE email='".$email."'";
        $res = mysqli_query($conn, $sql);
        echo '<script type="text/javascript">
                        alert("Claimed Succesfully!!")
                    </script>';
        }else{
            echo '<script type="text/javascript">
                        alert("Sorry!! you are yet to reach the minimum requirements for this reward!!")
                    </script>';
        }
    }elseif (isset($_POST['r2'])) {
        if($numActive >= 15){
            $amount = 2000;
            $sql = "INSERT INTO `transaction` (`email`, `type`, `amount`, `status`, `phone`, `details`) 
            VALUES ('".$email."', 'Reward', '2000', 'Success', '".$phone."', 'r2')";
        $res = mysqli_query($conn, $sql);
        //update balance and deposits of the user
        $balance = $balance + $amount;
        $sql = "UPDATE earnings SET balance=$balance WHERE email='".$email."'";
        $res = mysqli_query($conn, $sql);
        echo '<script type="text/javascript">
                        alert("Claimed Succesfully!!")
                    </script>';
        }else{
            echo '<script type="text/javascript">
                        alert("Sorry!! you are yet to reach the minimum requirements for this reward!!")
                    </script>';
        }
    }elseif (isset($_POST['r3'])) {
        if($numActive >= 25){
            $amount = 3000;
            $sql = "INSERT INTO `transaction` (`email`, `type`, `amount`, `status`, `phone`, `details`) 
            VALUES ('".$email."', 'Reward', '3000', 'Success', '".$phone."', 'r3')";
        $res = mysqli_query($conn, $sql);
        //update balance and deposits of the user
        $sql2 = "SELECT * FROM earnings WHERE email = '".$email."'";
        $res2 = mysqli_query($conn, $sql2);
        $res2 = $res2 -> fetch_array();
        $balance = $res2['balance'];
        $balance = $balance + $amount;
        $sql = "UPDATE earnings SET balance=$balance WHERE email='".$email."'";
        $res = mysqli_query($conn, $sql);
        echo '<script type="text/javascript">
                        alert("Claimed Succesfully!!")
                    </script>';
        }else{
            echo '<script type="text/javascript">
                        alert("Sorry!! you are yet to reach the minimum requirements for this reward!!")
                    </script>';
        }
    }elseif (isset($_POST['r4'])) {
        if($numActive >= 40){
            $amount = 6000;
            $sql = "INSERT INTO `transaction` (`email`, `type`, `amount`, `status`, `phone`, `details`) 
            VALUES ('".$email."', 'Reward', '6000', 'Success', '".$phone."', 'r4')";
        $res = mysqli_query($conn, $sql);
        //update balance and deposits of the user
        $sql2 = "SELECT * FROM earnings WHERE email = '".$email."'";
        $res2 = mysqli_query($conn, $sql2);
        $res2 = $res2 -> fetch_array();
        $balance = $res2['balance'];
        $balance = $balance + $amount;
        $sql = "UPDATE earnings SET balance=$balance WHERE email='".$email."'";
        $res = mysqli_query($conn, $sql);
        echo '<script type="text/javascript">
                        alert("Claimed Succesfully!!")
                    </script>';
        }else{
            echo '<script type="text/javascript">
                        alert("Sorry!! you are yet to reach the minimum requirements for this reward!!")
                    </script>';
        }
    }elseif (isset($_POST['ri'])) {
        if($downline >= 20){
            $amount = 30;
            $sql = "INSERT INTO `transaction` (`email`, `type`, `amount`, `status`, `phone`, `details`) 
            VALUES ('".$email."', 'Reward', '1000', 'Success', '".$phone."', 'ri')";
        $res = mysqli_query($conn, $sql);
        //update balance and deposits of the user
        $package = "UFL 1";
        $amount = 10;
        $daily = 1;
        $days = 7;
        $total = $days * $daily;
            $sql = "INSERT INTO orders(`email`, `package`, `amount`, `daily`, `cycle`, `totals`) VALUES('".$email."','".$package."', '".$amount."', '".$daily."', '".$days."', '".$total."')";
            $res = mysqli_query($conn, $sql);
            $sql = "UPDATE earnings SET balance = $balance WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            $msg = "Package bought succesfully";
            echo '<script type="text/javascript">
            alert("Package rewarded succesfully!")
           </script>';
    echo '<script type="text/javascript">
           setTimeout(function () {
               // Hide the preloader
               window.location.replace("task")
             }, 1000);
       </script>';;
        }
    }
    $squeli = "SELECT * FROM `transaction` WHERE email='".$email."' AND details='ri'";
    $resulti = mysqli_query($conn, $squeli);
    $numi = 0;
    if($resulti){
         $numi = mysqli_num_rows($resulti);
    }
    $squel1 = "SELECT * FROM `transaction` WHERE email='".$email."' AND details='r1'";
    $result1 = mysqli_query($conn, $squel1);
    $num1 = 0;
    if($result1){
         $num1 = mysqli_num_rows($result1);
    }
   
    $squel2 = "SELECT * FROM `transaction` WHERE email='".$email."' AND details='r2'";
    $result2 = $conn -> query($squel2);
     $num2 = 0;
    if($result2){
         $num2 = mysqli_num_rows($result2);
    }
    $squel3 = "SELECT * FROM `transaction` WHERE email='".$email."' AND details='r3'";
    $result3 = $conn -> query($squel3);
     $num3 = 0;
    if($result3){
         $num3 = mysqli_num_rows($result3);
    }
    $squel4 = "SELECT * FROM `transaction` WHERE email='".$email."' AND details='r4'";
    $result4 = $conn -> query($squel4);
     $num4 = 0;
    if($result4){
         $num4 = mysqli_num_rows($result4);
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
    <title>Reward</title>
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
    <div style="background-color: green; width: 100vw;height: 7vh;display: flex;align-items: center;padding: 0;" class="w3-container w3-card w3-border-bottom w3-border-green w3-text-white w3-top
    ">
        <div class="w3-panel">
            <a href="account" style="text-decoration: none;" class="w3-xlarge"><i class="fa-solid fa-arrow-left"></i> </a>
        </div>
        <div class="w3-threequarter">
            <p class="w3-xlarge w3-center">Reward Center</p>
        </div>
    </div>
    <div style="margin-top: 70px;margin-bottom: 100px;display: flex; flex-direction: column;justify-content: center;align-items: center;" class="w3-main">

    <div style="width: 100%;display: flex; flex-direction: row;" class="w3-row w3-black w3-border-bottom w3-padding">
            <div class="w3-left w3-block w3-threequarter w3-padding">
                <p class="w3-text-blue w3-large">Reach 10 Active downline <span class="w3-text-white">(<?=$numActive?>/10)</span></p>
                <p class="w3-text-white">Bonus Kes 1,000</p>
                <div class="w3-border w3-round-large">
                    <div class="w3-green w3-round-large" style="height: 10px;width:<?php 
                    $perc = $numActive/10;
                    $perc *= 100;
                    echo $perc
                    ?>%"></div>
                </div>
            </div>
            <form action="reward" method="post" style="display: flex; align-items: center; justify-content: flex-end;" class="w3-right w3-quarter w3-padding">
            <?php 
                if ($num1 > 0) {
                   echo '<button class="w3-btn w3-round w3-orange w3-right">Claimed</button>';
                }else {
                    echo '<button name="r1" class="w3-btn w3-round w3-orange w3-right">Claim</button>';
                }
            ?>
            </form>
    </div>
    <div style="width: 100%;display: flex; flex-direction: row;" class="w3-row w3-black w3-border-bottom w3-padding">
            <div class="w3-left w3-block w3-threequarter w3-padding">
                <p class="w3-text-blue w3-large">Reach 15 Active downline <span class="w3-text-white">(<?=$numActive?>/15)</span></p>
                <p class="w3-text-white">Bonus Kes 2,000</p>
                <div class="w3-border w3-round-large">
                    <div class="w3-green w3-round-large" style="height: 10px;width: <?php 
                    $perc = $numActive/15;
                    $perc *= 100;
                    echo $perc
                    ?>%"></div>
                </div>
            </div>
            <form action="reward" method="post" style="display: flex; align-items: center; justify-content: flex-end;" class="w3-right w3-quarter w3-padding">
            <?php 
                if ($num2 > 0) {
                   echo '<button class="w3-btn w3-round w3-orange w3-right">Claimed</button>';
                }else {
                    echo '<button name="r2" class="w3-btn w3-round w3-orange w3-right">Claim</button>';
                }
            ?>
        </form>
    </div>
    <div style="width: 100%;display: flex; flex-direction: row;" class="w3-row w3-black w3-border-bottom w3-padding">
            <div class="w3-left w3-block w3-threequarter w3-padding">
                <p class="w3-text-blue w3-large">Reach 25 Active downline <span class="w3-text-white">(<?=$numActive?>/25)</span></p>
                <p class="w3-text-white">Bonus Kes 3,000</p>
                <div class="w3-border w3-round-large">
                    <div class="w3-green w3-round-large" style="height: 10px;width: <?php 
                    $perc = $numActive/25;
                    $perc *= 100;
                    echo $perc
                    ?>%"></div>
                </div>
            </div>
            <form action="reward" method="post" style="display: flex; align-items: center; justify-content: flex-end;" class="w3-right w3-quarter w3-padding">
            <?php 
                if ($num3 > 0) {
                   echo '<button class="w3-btn w3-round w3-orange w3-right">Claimed</button>';
                }else {
                    echo '<button name="r3" class="w3-btn w3-round w3-orange w3-right">Claim</button>';
                }
            ?>
            </form>
    </div>
    <div style="width: 100%;display: flex; flex-direction: row;" class="w3-row w3-black w3-border-bottom w3-padding">
            <div class="w3-left w3-block w3-threequarter w3-padding">
                <p class="w3-text-blue w3-large">Reach 40 Active downline <span class="w3-text-white">(<?=$numActive?>/40)</span></p>
                <p class="w3-text-white">Bonus Kes 6,000</p>
                <div class="w3-border w3-round-large">
                    <div class="w3-green w3-round-large" style="height: 10px;width: <?php 
                    $perc = $numActive/40;
                    $perc *= 100;
                    echo $perc
                    ?>%"></div>
                </div>
            </div>
            <form action="reward" method="post" style="display: flex; align-items: center; justify-content: flex-end;" class="w3-right w3-quarter w3-padding">
            <?php 
                if ($num4 > 0) {
                   echo '<button class="w3-btn w3-round w3-orange w3-right">Claimed</button>';
                }else {
                    echo '<button name="r4" class="w3-btn w3-round w3-orange w3-right">Claim</button>';
                }
            ?>
            </form>
    </div>
    </div>
</body>
</html>