<?php 
     require_once 'includes/database.php';
     session_start();
     if(!isset($_SESSION['email'])){
        echo '<script type="text/javascript">
                 window.location.replace("../login.php")
            </script>';
      }else{
          $uname = $_SESSION['email'];
      }
      $msg = "";
      function get_user($db, $email){
            $sql = "SELECT * FROM users WHERE email='".$email."'";
            $res = mysqli_query($db, $sql);
            $res = $res -> fetch_array();
            return $res;
      }
      $user_details = get_user($conn, $uname);
      $country = $user_details['country'];
      if(isset($_POST['deposit'])){
          $val = $_POST['amount'];
          $phone = $_POST['phone'];
          
      }
      if(isset($_POST['submit'])){
          $nameRef = $_POST['name'];
          $phone = $_POST['phone'];
          $amount = $_POST['amount'];
          $type = "Deposits";
          $status = "Pending";
          
          $stmt = $conn -> prepare ("INSERT INTO `transaction`(`email`, `phone`, `amount`, `details`, `type`, `status`) VALUES (?,?,?,?,?,?)");
          $stmt -> bind_param('ssssss', $uname, $phone, $amount, $nameRef, $type, $status);
          $stmt -> execute();
          $msg = "Your deposit submitted successfully!. it will automatically reflect after successfull review";

          echo '<script>
                    alert("'.$msg.'")
                </script>';
           echo '<script>
                window.location.replace("transaction")
            </script>';
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
    <title>Airtel Money</title>
</head>
<body class="w3-green">
    <div class="w3-panel w3-white w3-card w3-margin">
        <div style="border-bottom: 5px dotted green;display: flex;justify-content: center;align-items: center;" class="">
            <img src="images/<?php 
                    if($country == 255 || $country == 256){
                        echo "airtel.png";
                    }elseif($country == 254){
                        echo "mpesa.png";
                    }else{
                        echo "Bitcoin Wallet";
                    }
                ?>" width="20%" alt="">
        </div>
        <div style="display: flex; flex-direction: row;align-items: center;justify-content: space-between;" class="w3-row">
            <div class="w3-block">
                <p style="font-weight: bold;" class="w3">Pay Amount</p>
                <p class="w3-text-black">$<?= number_format($val, 2)?></p>
                <p class="w3-text-blue">---</p>
            </div>
            <div class="w3-block">
                <p style="font-weight: bold;" class="w3">
                <?php 
                    if($country == 255 || $country == 256){
                        echo "Airtel Money";
                    }elseif($country == 254){
                        echo "Mpesa Send Money";
                    }else{
                        echo "Bitcoin Wallet";
                    }
                ?>
                </p>
                <p id='txt' class="w3-text-black">
                    <?php 
                    if($country == 255 || $country == 256){
                        echo "255783110417";
                    }elseif($country == 254){
                        echo "254740771716";
                    }else{
                        echo "Bitcoin Wallet";
                    }
                ?>
                    </p>
                <p id="copy" class="w3-text-blue">Copy</p>
            </div>
        </div>
        <p class='w3-tag w3-red w3-center'><?=$msg?></p>
        <form style="border-bottom: 5px dotted green;display: flex; flex-direction: column;" action="payment" method="post" class="w3-block">
            <input type="text" name="amount" class="w3-border w3-margin w3-padding" readonly value="<?= number_format($val, 2)?>">
            <input style="outline: none;" type="tel" name="phone" class="w3-border w3-margin w3-padding" required value="<?=$phone?>">
            <input style="outline: none;" type="text" name="name" class="w3-border w3-margin w3-padding" placeholder="Transaction reference" required>
            <p class="w3-text-pink w3-margin">Transaction reference is required*</p>
            <button name="submit" class="w3-btn w3-green w3-hover-yellow w3-margin">Confirm</button>
        </form>
        <div class="w3-margin-bottom">
            <p style="font-weight: bold;font-style: italic;" class="w3-text-green">Transparent & Honest</p>
        </div>
    </div>
           <script>
        $.noConflict();
        jQuery(document).ready(function ($){
            $('#copy').click(function (){
                let copyText = $('#txt').text()
                navigator.clipboard.writeText(copyText);
                alert('Text: ' + copyText + '\n' + ' copied successfully')
            })
        })
    </script>
</body>
</html>