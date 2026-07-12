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
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Transactions</title>
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
    <div style="background-color: green; width: 100vw;height: 7vh;display: flex;align-items: center;padding: 0;" class="w3-container w3-card w3-text-white w3-top
    ">
        <div class="w3-panel">
            <a href="account" style="text-decoration: none;" class="w3-xlarge"><i class="fa-solid fa-arrow-left"></i> </a>
        </div>
        <div class="w3-threequarter">
            <p class="w3-xlarge w3-center">Transactions</p>
        </div>
    </div>
    <div style="margin-top: 70px;margin-bottom: 100px;display: flex; flex-direction: column;justify-content: center;align-items: center;width: 97%;" class="w3-main">
    <?php 
            $sql = "SELECT * FROM transaction WHERE email='".$email."' ORDER BY ID DESC";
            $res = mysqli_query($conn, $sql);
            foreach ($res as $row):
        ?>
        <div style="display: flex;justify-content: space-between;align-items: center; margin-top: 50px;width: 100%;background-color: white;" class="w3-panel w3-margin w3-padding w3-round w3-card">
                <div class="w3-left">
                    <p style="font-weight: bold;" class="w3-text-grey w3-large"><?= $row['type']?></p>
                    <p class="w3-small <?php 
                    if ($row['status'] == "Success" || $row['status'] == "Approved") {
                        echo "w3-text-green";
                    }else{
                        echo "w3-text-red";
                    }
                ?>"><?=$row['status']?></p>
                </div>
                <div class="w3-right">
                    <p class="w3-text-orange"><?=$row['date']?></p>
                    <p style="font-weight: bold;" class="<?php 
                    if ($row['type'] == "Deposit") {
                        echo "w3-text-green";
                    }else{
                        echo "w3-text-red";
                    }
                ?>"><?php 
                if ($row['type'] == "Deposit") {
                    echo "+";
                }else{
                    echo "-";
                }
            ?>Kes<?=$row['amount']?></p>
                </div>
            </div>
                    <?php 
            endforeach;
            $num = mysqli_num_rows($res);
            if ($num == 0) {
                echo "<p class='w3-margin w3-center w3-text-grey'>You are yet to perform any transactions</p>";
            }

        ?>
        </div>

</body>
</html>