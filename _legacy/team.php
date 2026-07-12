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
    $numActive = count($active)
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Team</title>
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
    <div style="background-color: green; width: 100vw;height: 7vh;display: flex;align-items: center;padding: 0;" class="w3-container w3-card w3-text-white">
        <div class="w3-panel">
            <a href="account" style="text-decoration: none;" class="w3-xlarge"><i class="fa-solid fa-arrow-left"></i> </a>
        </div>
        <div class="w3-threequarter">
            <p class="w3-xlarge w3-center">Team Center</p>
        </div>
    </div>
    <div style="background-color: white;display: flex;align-items: center;justify-content: center;width: 100%;" class="w3-container w3-round-large">
        <div class="w3-panel w3-third">
            <P class="w3-text-blue w3-center">Referral Earnings</P>
            <p class="w3-center">Kes <?=number_format($referral, 2)?></p>
        </div>
        <div class="w3-panel w3-border-left w3-border-blue w3-third">
            <P class="w3-text-blue w3-center">Total Downlines</P>
            <p class="w3-center"><?=$downline?></p>
        </div>
        <div class="w3-panel w3-border-left w3-border-blue w3-third">
            <P class="w3-text-blue w3-center">Active Downlines</P>
            <p class="w3-center"><?=$numActive?></p>
        </div>
    </div>
    <div style="display: flex;flex-direction: column;justify-content: center;align-items: center;width: 100%;" class="w3-container">
        <div style="background-color: white;display: flex;flex-direction: column;justify-content: center;align-items: center;width: 100%;" class="w3-panel w3-round w3-border w3-border-blue w3-card w3-padding">
            <p style="font-weight: bold;" class="w3-text-green w3-large w3-center"><?=$userID?></p>
            <input id="txt" type="text" style="outline: none;width: 100%;background-color: transparent;" class="w3-large w3-border w3-border-green w3-round-large" readonly value="https://m-vase.club/register?invite=<?=$userID?>">
            <button id="copy" style="width: 60%;" class="w3-btn w3-green w3-round-large w3-margin">Copy Link</button>
        </div>
    </div>
    <div style="margin-bottom: 100px;display: flex; flex-direction: column;justify-content: center;align-items: center; width: 100%;" class="w3-main">
    <?php 
            $sql = "SELECT * FROM users WHERE refer='".$userID."' ORDER BY ID DESC";
            $res = mysqli_query($conn, $sql);
            foreach ($res as $row):
        ?>
        <div style="display: flex;justify-content: space-between;align-items: center; margin-top: 50px;width: 90%;background-color: white;" class="w3-panel w3-margin w3-padding w3-round w3-card">
        <?php 
                    $phone = $row['phone'];
                    $first = substr($phone, 0, 2);
                    $length = strlen($phone);
                    $last = substr($phone, $length - 2, 2);
                    $display = $first."***".$last;
                ?>
                <div class="w3-left">
                    <p class="w3-text-grey"><?=$display?></p>
                    <p class="w3-text-<?php 
                    if ($row['status'] == "Active") {
                        echo 'green';
                    }else{
                        echo 'red';
                    }
                    ?>"><?=$row['status']?></p>
                </div>
                <p class="w3-text-green w3-center"><?=$row['date']?></p>
                <div class="w3-right">
                    <p class="w3-text-grey">Level</p>
                    <bn class="w3-btn w3-green w3-round-large">Connect</bn>
                </div>
            </div>
        
        <?php 
            endforeach;
            $num = mysqli_num_rows($res);
            if ($num == 0) {
                echo "<p class='w3-margin w3-center w3-text-grey'>You don't have any downline</p>";
            }

        ?>
        </div>
            <script>
        $(document).ready(function (){
            $('#copy').click(function (){
                let copyText = $('#txt').val();
                navigator.clipboard.writeText(copyText);
                alert('Text linked copied successfully')
            })
        })
    </script>
</body>
</html>