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
    
    function initiate_stk($amount, $phone, $ref){
        $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://backend.payhero.co.ke/api/v2/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "amount": '.$amount.',
        "phone_number": "'.$phone.'",
        "channel_id": 3324, 
        "provider": "m-pesa", 
        "external_reference": "'.$ref.'",
        "callback_url": "https://m-vase.club/callback.php"
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Basic OGlUSFZFaUhMWnN3a2hHVVBHc3A6anVoWFZrRk5qSVl0MGNMOERGMlR3dlhrQ0VWUWJHNDVVVnNaMEdDSw=='
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    // echo $response;
    $results = json_decode($response, true);
    $resData = [
        'messsage' => '',
        'status' => '',
        'data' => ''
        ];
    if($results['success']){
    $resData = array(
        'data' => $results,
        'Message' => 'Mpesa stk Inititated successfully. Check phone '.$phone.' And confirm with mpesa pin',
        'Status' => 'Success'
        );
    }else{
        $resData = array(
        'data' => $results,
        'Message' => 'Mpesa STK Push failed. Kindly reach our customer care for quick assistance',
        'Status' => 'Failed'
        );
    }
    return $resData;
    }
    
    if(isset($_POST['deposit'])){
        $amount = $_POST['amount'];
        $phone = $_POST['phone'];
        $stk = initiate_stk($amount, $phone, $email);
        $status = $stk['Status'];
        if($status == 'Success'){
            $msg = $stk['Message'];
        }else{
            $error = $stk['Message'];
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
    <title>Deposit</title>
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
            <p class="w3-xlarge w3-center">Deposits</p>
        </div>
    </div>
    <div style="width: 100%;display: flex;flex-direction: column; align-items: center;align-items: center;" class="w3-container">
        <div class="w3-panel w3-center">
            <p style="margin: 0;" class="w3-text-green w3-large">Balance</p>
            <p style="margin: 0;" class="w3-text-blue w3-xlarge"> Kes<?=number_format($balance, 2)?></p>
        </div>
        <div style="width: 97%;background-color: rgba(107, 94, 19, 0.5);" class="w3-panel w3-round-large w3-border-amber w3-border">
            <li class="w3-text-amber w3-margin">Enter amount and phone number</li>
            <li class="w3-text-amber w3-margin">Click submit and you will recieve stk pop up</li>
            <li class="w3-text-amber w3-margin">Enter Mpesa pin and refresh the page and amount will reflect automatically</li>
        </div>
           <?php 
        if ($msg) {
            echo '        <div style="width: 97%;background-color: rgba(2, 99, 78, 0.5)" class="w3-panel w3-round-large w3-border-green w3-border">
            <li class="w3-text-white w3-margin">'.$msg.'</li>
        </div>';
        }
        if ($error) {
            echo '        <div style="width: 97%;background-color: rgba(146, 33, 5, 0.5);" class="w3-panel w3-round-large w3-border-red w3-border">
            <li class="w3-text-red w3-margin">'.$error.'</li>
        </div>';
        }
        ?>
        <form action="deposit" method="post" style="background-color: white;width: 97%;display: flex;flex-direction: column; align-items: center;align-items: center;" class="w3-panel w3-card w3-round-large w3-padding">
            <input style="background-color: transparent;outline: none;" type="number" placeholder="Enter Amount" class="w3-input w3-border-green w3-large w3-margin-bottom " name="amount">
            <input style="background-color: transparent;outline: none;" type="tel" name="phone" class="w3-input w3-border-green w3-margin-bottom w3-large" placeholder="Enter phone number eg 07..." id="">
            <button style="width: 60%;" class="w3-btn w3-margin w3-green w3-round-large" name="deposit">Submit</button>
    </form>
    </div> 
</body>
</html>