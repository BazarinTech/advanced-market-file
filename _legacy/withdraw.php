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
    $numActive = count($active);
    if(isset($_POST['withdraw'])){
        $phone = $_POST['phone'];
        $amount = $_POST['amount'];
        $raw= 'Yes';
        if($raw == 'Yes'){
            if ($amount <= $balance) {
                if ($amount >= 50) {
                    $fee = $amount * 0.05;
                    $tamount = $amount - $fee;
                    $balance -= $amount;
                    $withdraw += $amount;
                    $type = 'auto';
                    
                    if($type == 'auto'){
                        $result = initiate_withdrawal($phone, $tamount);
                    if(!isset($result -> error_message)){
                            $msg = "Withdrawal recieved successfully and will be sent to ".$phone." Fee charged Kes".$fee." expected recieving amount Kes".$tamount;
                            $status = 'Success';
                        }else{
                            $msg = "You have successfully withdrawn KES".$amount.", Your withdrawal request shall be processed as soon as verified!!";
                            $status = 'Pending';
                        }
                    
                    }else{
                        $msg = "You have successfully withdrawn KES".$amount.", Your withdrawal request shall be processed within 12 hrs!!";
                        $status = 'Pending';
                    }
                    
                    $sql = "INSERT INTO `transaction` (`email`, `phone`, `amount`, `type`, `status`, `RecAmount`) 
                    VALUES ('".$email."', '".$phone."', '".$amount."', 'Withdraw', '".$status."', '".$tamount."')";
                    $res = mysqli_query($conn, $sql);
                                        $sql = "UPDATE earnings SET balance=$balance, withdraw=$withdraw WHERE email='".$email."'";
                    $res = mysqli_query($conn, $sql);
                }else{
                    $error = "Minimum withdrawal is Kes 150.00";
                }
            }else{
                $error = "Insuficient balance to process this withdrawal request!!";
            }
        }else{
        $msg = "Withdrawals is from monday to friday!!";
    }
}
    function initiate_withdrawal($phone, $amount){
          $curl = curl_init();
           curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://backend.payhero.co.ke/api/v2/withdraw',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "external_reference": "mpesa101",
            "amount": '.$amount.',
            "phone_number": "'.$phone.'",
            "network_code": "63902", 
            "callback_url": "https://m-verbal.club",
            "channel": "mobile",
            "channel_id": 1585,
            "payment_service":"b2c"
        }
        ',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
             'Authorization: Basic OGlUSFZFaUhMWnN3a2hHVVBHc3A6anVoWFZrRk5qSVl0MGNMOERGMlR3dlhrQ0VWUWJHNDVVVnNaMEdDSw=='
          ),
        ));
        $response = curl_exec($curl);
        echo $response;
        curl_close($curl);
        $result = json_decode($response);
        return $result;
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
    <title>Withdraw</title>
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
        <div class="w3-panel w3-padding">
            <a href="account" style="text-decoration: none;" class="w3-xlarge"><i class="fa-solid fa-arrow-left"></i> </a>
        </div>
        <div class="w3-threequarter">
            <p class="w3-xlarge w3-center">Withdrawal</p>
        </div>
    </div>
    <div style="width: 100%;display: flex;flex-direction: column; align-items: center;align-items: center;" class="w3-container">
        <div class="w3-panel w3-center">
            <p style="margin: 0;" class="w3-text-green w3-large">Balance</p>
            <p style="margin: 0;" class="w3-text-blue w3-xlarge">Kes<?=number_format($balance, 2)?></p>
        </div>
        <div style="width: 97%;background-color: rgba(107, 94, 19, 0.5);" class="w3-panel w3-round-large w3-border-amber w3-border">
            <li class="w3-text-amber w3-margin">Enter amount and phone number</li>
            <li class="w3-text-amber w3-margin">Fee charged 5%</li>
            <li class="w3-text-amber w3-margin">Click submit and you will recieve your earnings instantly to your mpesa</li>
            <li class="w3-text-amber w3-margin">Withdrawals are Automatic</li>
        </div>
        <?php 
        if ($msg) {
            echo '        <div style="width: 97%;" class="w3-panel w3-round-large w3-border-green w3-border">
            <li class="w3-text-green w3-margin">'.$msg.'</li>
        </div>';
        }
        if ($error) {
            echo '        <div style="width: 97%;;" class="w3-panel w3-round-large w3-border-red w3-border">
            <li class="w3-text-red w3-margin">'.$error.'</li>
        </div>';
        }
        ?>
        <form action="withdraw" method="post" style="background-color: white;width: 97%;display: flex;flex-direction: column; align-items: center;align-items: center;" class="w3-panel w3-card w3-round-large w3-padding">
            <input style="background-color: transparent;outline: none;" type="number" placeholder="Enter Amount(Kes)" class="w3-input w3-border-green w3-large w3-margin-bottom" name="amount">
            <input style="background-color: transparent;outline: none;" type="tel" name="phone" class="w3-input w3-border-green w3-margin-bottom w3-large" placeholder="Enter phone number(070000000)" id="">
            <button style="width: 60%;" class="w3-btn w3-margin w3-green w3-round-large" name="withdraw">Submit</button>
        </form>
    </div> 
</body>
</html>