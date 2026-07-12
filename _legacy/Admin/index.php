<?php
session_start();
include "../includes/database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="icon" 
     type="image/png" 
     href="home/[removal.ai]_d2c7b213-49fc-4a0e-b504-4f2e27babfcb-vex_logo.svg">
    <link rel="shortcut icon" type="logo" href="home/logij.png" />
     <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Login</title>
</head>
<body style="display: flex; align-items: center; justify-content: center;" class="w3-light-grey">
    <div style="width: 80%; padding: 0; margin-top: 40px; height: fit-content;" class="w3-row w3-white w3-centered w3-card-2 w3-sans-serif w3-panel">
      <div class="w3-half w3-display-container w3-padding-0">
        <img src="back.jpeg" class="w3-image" width="100%" alt="">
        <p style="font-weight: bold;" class="w3-display-middle w3-xxlarge w3-text-white">Welcome<br>Back!</p>
      </div>
      <div class="w3-half w3-padding w3-block">
        <h2 style="font-weight: bold;" class="w3-sans-serif">Login</h2>
        <p class="w3-text-gray">Welcome back! please login to Admin account</p>
      
    <form action="index.php" class="w3-form w3-block" method="post">
      <label class="w3-margin-top"  for="username">Username</label>
      <input style="outline: none;" type="text"  id="username" class="w3-input w3-small w3-margin-bottom w3-border-grey w3-border-2 w3-round-small" placeholder="Enter Username" name="username" required>
      <label class="w3-margin-top" for="username">Password</label>
      <input style="outline: none;" type="password" class="w3-input w3-border-grey w3-border-5 w3-small w3-round-small" id="password"  placeholder="Enter Password" name="password" required>
      <input type="submit" class="w3-btn w3-margin w3-center w3-round-small w3-blue w3-hover-amber" name="submit" value="Login" id="sbm-btn">
      <p class="w3-text-grey w3-medium">Dont have an account? <a class="w3-text-black" href="register.php">register</a></p>
    </form>
       <?php
      if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $uname = "";
      if ($username == "Admin" && $password == "bazarin") {
        $_SESSION['username'] = $username;
        sleep(2);
        echo '<script type="text/javascript">
                 window.location.replace("AdmDashboard.php")
            </script>';
     }else {
        echo "<p class='w3-text-red w3-medium'>Account mismatch, <br>please check your password or username</p>";
    }
  }
     ?>
           <!-- <?php include "includes/footer.php" ?> -->
    </div>
  </div>
</body>
</html>