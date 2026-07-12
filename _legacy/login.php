<?php 
    session_start();
    include 'includes/database.php';
    $msg = "";
    $error = "";
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['pwrd'];
        $stmt = $conn -> prepare("SELECT * FROM `users` WHERE `email`=? AND `passwrd`=?");
        $stmt -> bind_param('ss', $email, $password);
        $stmt -> execute();
        $result = $stmt -> get_result();
        $rows = mysqli_num_rows($result);
    if ($rows) {
        $_SESSION['email'] = $email;
        sleep(2);
        echo '<script type="text/javascript">
                window.location.replace("home")
            </script>';
    }else {
        echo '<script type="text/javascript">
        alert("Invalid Account!!")
       </script>';
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
    <link rel="shortcut icon" href="images/sofi.png" />
    <title>Login</title>
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
    <div style="margin-top: 70px;width: 100%;display: flex; flex-direction: column; align-items: center;justify-content: center;" class="w3-container w3-padding">
        <div style="display: flex; flex-direction: column; align-items: center;justify-content: center;" class="w3-panel">
            <p style='margin: 0' class='w3-large w3-text-black'>M-Vase</p>
            <p style="font-weight: bold;margin: 0;" class="w3-text-green w3-xlarge">Login</p>
        </div>
        <form style="width: 70%" action="login" method="post">
            <input style="background-color: transparent;outline: none;width: 100%;" type="email" name="email" placeholder="Enter your email" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom " id="" required>
            <input style="background-color: transparent;outline: none;width: 100%;" type="password" name="pwrd" placeholder="Enter your password" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom" id="" required>
            <button style="width: 100%" class="w3-btn w3-green w3-round-large" name="login">Login</button>
            <div class="w3-panel">
                <a href="register" class="w3-text-blue w3-left">Register</a>
                <a href="register" class="w3-text-blue w3-right">Forgot password</a>
            </div>
        </form>
    </div>
</body>
</html>