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
    $password = $res['passwrd'];
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
    if(isset($_POST['update'])){
        if($_POST['newPass']){
            if($_POST['prevPass'] == $password){
                $prevPass = $_POST['prevPass'];
                $newPass = $_POST['newPass'];
                $conPass = $_POST['conPass'];
                if($newPass == $conPass){
                    if(strlen($newPass) > 7){
                        $sql = "UPDATE users SET  passwrd='".$newPass."' WHERE email='".$email."'";
                        $res = mysqli_query($conn, $sql);
                        $msg = "Password Updated successfully!!";
                    }else{
                        $msg = "Password characters must be greater than or equal to 8";
                    }
                }else{
                    $msg = "Password mismatch!!";
                }
            }else{
                $msg = "Enter correct previous password";
            }
        }else{
                        $phone = $_POST['phone'];
                        $sql = "UPDATE users SET phone='".$phone."' WHERE email='".$email."'";
                        $res = mysqli_query($conn, $sql);
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
    <title>User Details</title>
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
            <p class="w3-xlarge w3-center">User Details</p>
        </div>
    </div>
    <div style="margin-top: 70px;margin-bottom: 100px;display: flex; flex-direction: column;justify-content: center;align-items: center;width: 100%;" class="w3-main w3-white">
        <div style="width: 100%;" class="w3-row w3-border-bottom w3-padding">
            <p class="w3-text-grey w3-left">Email</p>
            <p class="w3-text-grey w3-right"><?=$email?></p>
        </div>
        <div style="width: 100%;" class="w3-row w3-border-bottom w3-padding">
            <p class="w3-text-grey w3-left">Phone Number</p>
            <p class="w3-text-grey w3-right"><?=$phone?></p>
        </div>
        <div id="btn" style="width: 100%;" class="w3-row w3-border-bottom w3-padding">
            <p class="w3-text-grey w3-left">Modify login password</p>
            <p class="w3-text-grey w3-right"><i class="fa-solid fa-angle-right"></i></p>
        </div>
    </div>
    <button id="logout" style="width: 90%;" class="w3-btn w3-green w3-round-large w3-large">Sign out of acoount</button>
    </div>
    <div id="id01" class="w3-modal">
        <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">
    
          <div class="w3-center"><br>
            <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-hover-red w3-display-topright" title="Close Modal">&times;</span>
            <p class="w3-large">Account Details</p>
          </div>
          <form class="w3-container" action="user" method="post">
            <div class="w3-section">
              <label>Update Password</label>
              <input class="w3-input w3-border w3-margin-bottom" type="password" placeholder="Enter previous Password" name="prevPass">
              <input class="w3-input w3-border w3-margin-bottom" type="password" placeholder="Enter new Password" name="newPass">
               <input class="w3-input w3-border" type="password" placeholder="Confirm new Password" name="conPass">
              <button name='update' class="w3-button w3-block w3-teal w3-section w3-padding" type="submit">Update</button>
            </div>
          </form>
    
          <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
            <button onclick="document.getElementById('id01').style.display='none'" type="button" class="w3-button w3-red">Cancel</button>
          </div>
    
        </div>
      </div>
      <script>
        $(document).ready( function() {
            const editBtn = $('#btn');
            editBtn.click(function (){
                $('#id01').show(100)
            })
            $('#logout').click(function (){
                window.location.replace('logout')
            })
        })
      </script>
</body>
</html>