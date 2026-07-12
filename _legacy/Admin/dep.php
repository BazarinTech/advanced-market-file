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
if(isset($_POST['wallet'])){
    $amount = $_POST['amount'];
    $phone = $_POST['phone'];
    $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://backend.payhero.co.ke/api/v2/topup',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "amount": '.$amount.',
    "phone_number": "'.$phone.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Basic alpwZW1ud3RiWnYzTVY1alYxcUU6S1FOeFpaWGhMUm1HVmo5ck9nU3BBRWVXOENSUDk0NVF4VWR2cUtBSA=='
  ),
));
$response = curl_exec($curl);
curl_close($curl);
// echo $response;
}
$msg = '';
if(isset($_POST['payment'])){
      $amount = $_POST['amount'];
      $phone = $_POST['phone'];
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
        "channel_id": 1585, 
        "provider": "sasapay",
        "network_code":"63902", 
        "external_reference": "INV-009",
        "customer_name":"John Doe",
        "callback_url": "https://example.com/callback.php"
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Basic alpwZW1ud3RiWnYzTVY1alYxcUU6S1FOeFpaWGhMUm1HVmo5ck9nU3BBRWVXOENSUDk0NVF4VWR2cUtBSA=='
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    // echo $response;
     $results = json_decode($response, true);
     
     if(isset($results['error_message'])){
         $msg = $results['error_message'];
     }else{
         $msg = "Transaction Initiated succesfully!";
     }
 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" 
     type="image/png" 
     href="https://vexuse.co.ke/logo.png">
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
     <script src="https://kit.fontawesome.com/1c8bf27677.js" crossorigin="anonymous"></script>
    <title>Account Services</title>
    <style>
                .alert-info {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #b3d7ff;
    border-radius: 4px;
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
}

/* Optional: Add styles for the close button */
.alert-info .close {
    margin-top: -2px;
    margin-right: -10px;
    color: inherit;
    
}
    </style>
    
</head>
    <body class="w3-sans-serif w3-light-gray">
   <div style="padding: 0; width: 100vw;" class="w3-container w3-row">
   <div id="sidebar" class="w3-cell w3-padding w3-block w3-quarter w3-blue" style="width: fit-content; display: flex; flex-direction: column; align-items: start; overflow-x: hidden;">
            <div style="margin: 0; width: 100%;" class="w3-row w3-panel w3-margin-bottom w3-border-bottom w3-border-black w3-border-10">
            <p class="w3-cell w3-xxlarge">Admin</p>
            <p id="close" style="margin: 0;" class="w3-button w3-xxlarge w3-display-topright" onclick="document.getElementById('sideBar').display='none'">&times;</p>
            </div>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none w3-round-small" href="AdmDashboard.php"> <span><i class="fa-solid fa-wallet"></i></span> Dashboard</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="deposits.php"> <span><i class="fa fa-youtube-square" aria-hidden="true"></i></span> Deposits</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="withdrawals.php"> <span><i class="fa-solid fa-money-bill-trend-up"></i></span> Withdrawals</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="users.php"><span><i class="fa fa-share-square" aria-hidden="true"></i></span> Users</a>
            <a class="w3-bar-item w3-button w3-large w3-margin-bottom w3-hover-text-amber w3-hover-none  w3-round-small" href="service.php"> <span><i class="fa-solid fa-square-poll-vertical"></i></span> Service</a>
            </div>
          <div style="padding: 0; margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;" class="w3-cell w3-container w3-threequarter">
            <div style="height: auto; width: 100%; margin: 0;" class="w3-container w3-bar w3-row w3-card w3-white">
                    <div id="pussy" style="height: 100%; display: flex; align-self: center; flex-direction: row; align-items: center; justify-content: center;" class="w3-left w3-margin-top">
                        <button id="btn-hide" class="w3-button w3-left"><i class="fa-solid fa-bars"></i></button>
                        <p id="txt-hide" style="font-weight:bold;" class="w3-large w3-bar-item w3-text-green">Bazarin Technologies</p>
                    </div>
                    
                    <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="w3-right w3-margin">
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="w3-border-right w3-border-blue-gray">
                            <p class="w3-text-grey w3-margin w3-medium"><i class="fa-solid fa-bell"></i></p>
                            <p class="w3-text-grey w3-margin w3-medium"><i class="fa-solid fa-envelope"></i></p>
                        </div>
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center;" class="">
                            <p style="margin: 0;" class="w3-small w3-margin">account</p>
                            <a style="text-decoration: none;" href="" class="w3-hover-text-amber w3-large"><i class="fa-regular fa-user"></i></a>
                        </div>
                    </div>
                </div>
      <form style="display: flex; align-items: start; justify-content:center; flex-direction:column;" class="w3-panel w3-round w3-margin w3-card w3-white w3-padding-20" action="dep.php" method="post">
        <h3 class="w3-xlarge">Recharge Service Wallet</h3>
        <input style="outline: none;" type="number" class="w3-large w3-input w3-margin-bottom" name="amount" placeholder="Enter amount to Recharge">
        <input style="outline: none;" type="tel" class="w3-large w3-input w3-margin-bottom" name="phone" placeholder="Enter Phone">
        <input style="width: 50%;" type="submit" class="w3-large w3-round w3-margin w3-btn w3-green w3-hover-amber" onclick="document.getElementById('modal').style.display='none'"  value="Recharge" name="wallet" id="btn">
      </form>
      <form style="display: flex; align-items: start; justify-content:center; flex-direction:column;" class="w3-panel w3-round w3-margin w3-card w3-white w3-padding-20" action="dep.php" method="post">
        <h3 class="w3-xlarge">Recharge Payment Balance</h3>
        <input style="outline: none;" type="number" class="w3-large w3-input w3-margin-bottom" name="amount" placeholder="Enter amount to Recharge">
        <input style="outline: none;" type="tel" class="w3-large w3-input w3-margin-bottom" name="phone" placeholder="Enter Phone">
        <input style="width: 50%;" type="submit" class="w3-large w3-round w3-margin w3-btn w3-green w3-hover-amber" onclick="document.getElementById('modal').style.display='none'"  value="Recharge" name="payment" id="btn">
        <div class="alert alert-info"><?php echo $msg?></div>
      </form>
    
       <a href="../dashboard.php">go to dashboard</a>
  </div>
  </div>
  
   <script>
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
        document.querySelector('#sidebar').style.maxWidth = '50vw'
        document.querySelector('#sidebar').classList.add('w3-sidebar')
       
     })
   
     closeBtn.addEventListener('click', function () {
        document.getElementById('sidebar').style.display = 'none'
     })
</script>
</body>
</html>