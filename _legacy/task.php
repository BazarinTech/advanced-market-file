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
    $error = '';
    $msg = '';
    if (isset($_POST['claim'])) {
        if ($roll == 1) {
            $sql4 = "SELECT * FROM orders WHERE email='".$email."'";
            $res4 = mysqli_query($conn, $sql4);
            $sql = "SELECT * FROM orders WHERE email='".$email."'";
            $res = mysqli_query($conn, $sql);
            $rows = mysqli_num_rows($res);
            $res = $res -> fetch_array();
            // echo $rows;
            // print_r($res);
            // var_dump($res4);
            foreach ($res4 as $invs) {
                $package = $invs['package'];
                $id = $invs['ID'];
                $days = $invs['cycle'];
                $daily = $invs['daily'];
                $prodEarning = $invs['earnings'];
                $status = $invs['status'];
                $prodT = $invs['totals'];
                if ($status == 'Active') {
                    if ($rows > 0) {
                        $balance = $balance + $daily;
                        $total = $total + $daily;
                        $prodEarning = $prodEarning + $daily;
                        if ($prodEarning == $prodT) {
                            $status = "Inactive";
                        }else{
                            $status = "Active";
                        }
                        $sql = "UPDATE earnings SET balance='".$balance."', totals='".$total."', roll=0 WHERE email='".$email."'";
                        $res = mysqli_query($conn, $sql);
                        $sql = "UPDATE orders SET earnings='".$prodEarning."', status='".$status."' WHERE ID='".$id."'";
                        $res = mysqli_query($conn, $sql);
                        echo "<script>
            alert('Earnings Claimed succesfully')
        </script>";
                    }else{
                        echo "<script>
            alert('You cannot claim earnings without active machine')
        </script>";
                    }
                }
                }
                
         }else {
            echo "<script>
            alert('Please come back tomorrow')
        </script>";
         }
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
    <title>Tasks</title>
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
    <div style="background-color: green; width: 100vw;height: 7vh;display: flex;align-items: center;justify-content: center;" class="w3-container w3-card w3-text-white">
        <div class="w3-panel">
            <p class="w3-xlarge">Task Center</p>
        </div>
    </div>
    <div style="display: flex;flex-direction: column;justify-content: center;align-items: center;" class="w3-container">
        <form action="task" method="post" style="background-color: green;display: flex;flex-direction: column;justify-content: center;align-items: center;width: 90%;" class="w3-panel w3-round w3-border w3-border-yellow w3-card w3-padding">
            <div style="width: 100;display: flex;" class="">
                <img style="margin: auto;" src="images/orderL.jpeg" class="w3-image w3-circle w3-centered" width="80" alt="">
            </div>
            <p class="w3-large w3-center w3-text-white">Click the button manually to collect earnings earned by the machines within the 24 hours</p>
            <button name="claim" style="width: 70%;" class="w3-btn w3-yellow w3-round-large w3-padding w3-card">Claim Earnings</button>
        </form>
    </div>
    <div style="margin-bottom: 100px;display: flex;flex-direction: column;justify-content: center;align-items: center;" class="w3-container">
    <?php 

$sql = "SELECT * FROM orders WHERE email='".$email."' ORDER BY ID DESC";
$res = mysqli_query($conn, $sql);
foreach ($res as $row):
?>
        <div style="display: flex;margin-top: 50px;width: 90%;background-color: green;" class="w3-panel w3-margin w3-padding w3-border w3-border-yellow w3-round w3-card">
            <img src="images/<?php     if ($row['package'] == "UFL 1") {
        echo "1.jpeg";
    }elseif ($row['package'] == "UFL 2") {
        echo "2.jpeg";
    }elseif ($row['package'] == "UFL 3") {
        echo "3.jpeg";
    }elseif ($row['package'] == "UFL 4") {
        echo "4.jpeg";
    }elseif ($row['package'] == "UFL 6") {
        echo "5.jpeg";
    }elseif ($row['package'] == "UFL 6") {
        echo "6.jpeg";
    }elseif ($row['package'] == "UFL 7") {
        echo "7.jpeg";
    }elseif($row['package'] == "UFL 8"){
        echo "8.jpeg";
    }elseif($row['package'] == "UFL 9"){
        echo "7.jpeg";
    }elseif($row['package'] == "UFL 10"){
        echo "8.jpeg";
    }elseif($row['package'] == "UFL 11"){
        echo "7.jpeg";
    }elseif($row['package'] == "SFL 1"){
        echo "sfl1.webp";
    }elseif($row['package'] == "SFL 2"){
        echo "sfl2.webp";
    }else{
        echo "8.jpeg";
    }
    ?>" width="40%" class="w3-image w3-round-large w3-left" style="height: 100%s;" alt="">
            <div class="w3-block w3-padding">
                <p style="margin: 0; font-weight: bold;" class="w3-text-white w3-large">Product name: <?=$row['package']?></p>
                <p style="margin: 0;" class="w3-medium w3-text-yellow">Cycle: <?=$row['cycle']?>days</p>
                <p style="margin: 0;" class="w3-medium w3-text-yellow">Price: Kes <?=$row['amount']?></p>
                <p style="margin: 0;" class="w3-medium w3-text-yellow">Daily Income: Ksh <?=$row['daily']?></p>
                <p style="margin: 0;" class="w3-medium w3-text-yellow">Total income: Ksh <?=$row['totals']?></p>
                <p style="margin: 0;" class="w3-medium w3-text-yellow">Status: <?=$row['status']?></p>
            </div>
   
        </div>
        <?php 
            endforeach;
            $num = mysqli_num_rows($res);
            if ($num == 0) {
                echo "<p class='w3-margin w3-center w3-text-grey'>You don't have any investment to display</p>";
            }

        ?>
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