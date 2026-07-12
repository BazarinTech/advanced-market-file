<?php 
    include 'includes/database.php';
    if (!isset($_GET['invite'])) {
        $ref = '1631';
    }else{
        $ref = $_GET['invite'];
    }
            $msg = "";
            $error = "";
            if (isset($_GET['register'])) {;
                $phone = $_GET['phone'];
                $email = $_GET['email'];
                $password = $_GET['pwrd'];
                $conPass = $_GET['con-pwrd'];
                $sponsor = $_GET['ref'];
                $country = $_GET['country'];
                if ($password == $conPass) {
                    if (strlen($password) > 7) {
                        $sql = "SELECT * FROM `users` WHERE `email` = '".$email."'";
                        $res = mysqli_query($conn, $sql);
                        $num = mysqli_num_rows($res);
                        if ($num) {
                            echo '<script type="text/javascript">
                            alert("Email seems to exist. Please try another one!!")
                           </script>';
                        }else {
                            $stmt = $conn -> prepare ("INSERT INTO `users`(`passwrd`, `phone`, `email`, `refer`, `country`) VALUES (?,?,?,?,?)");
                            $stmt -> bind_param('ssssi', $password, $phone, $email, $sponsor, $country);
                            $stmt -> execute();
                            $sql = "INSERT INTO `earnings`(`email`) VALUES ('".$email."')";
                            $res = mysqli_query($conn, $sql);
                            echo '<script type="text/javascript">
                            window.location.replace("login.php")
                            </script>';
                        }
                    }else{
                        echo '<script type="text/javascript">
                         alert("Password should be greater than 7!!")
                        </script>';
                    }
                }else{
                    echo '<script type="text/javascript">
                    alert("Password Mismatch!!")
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
    <title>Welcome to M-Vase farmers Club -Invest digitaly earn physically</title>
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
    <div style="margin-top: 30px;width: 100%;display: flex; flex-direction: column; align-items: center;justify-content: center;" class="w3-container w3-padding">
        <div style="display: flex; flex-direction: column; align-items: center;justify-content: center;" class="w3-panel">
            <p style='margin: 0' class='w3-large w3-text-black'>M-Vase Farmers</p>
            <p style="font-weight: bold;margin: 0;" class="w3-text-green w3-xlarge">Register</p>
        </div>
        <form style="width: 70%" action="register" method="get">
            <input style="background-color: transparent;outline: none;width: 100%;" type="email" name="email" placeholder="Enter your email" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom" id="" required>
            <input style="background-color: transparent;outline: none;width: 100%;" type="text" name="name" placeholder="Enter your Names" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom " id="" required>
            <input style="background-color: transparent;outline: none;width: 100%;" type="phone" name="phone" placeholder="Enter your Phone" class="w3-input w3-margin-bottom w3-border-bottom w3-border-green" id="" required>
            <select style="background-color: transparent;outline: none;width: 100%;" id="select-inpt" class="w3-input w3-margin-bottom w3-border-bottom w3-border-green"  name="country" id="" required>
                            <option value="254">Kenya</option>
                            <option value="256">Uganda</option>
                            <option value="255">Tanzania</option>
                            <option value="250">Other</option>
                        </select>
            <input style="background-color: transparent;outline: none;width: 100%;" type="text" name="ref" value="<?=$ref?>" class="w3-input w3-margin-bottom w3-border-bottom w3-border-green" id="" required readonly>
            <input style="background-color: transparent;outline: none;width: 100%;" type="password" name="pwrd" placeholder="Enter your password" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom" id="" required>
            <input style="background-color: transparent;outline: none;width: 100%;" type="password" name="con-pwrd" placeholder="Re-Enter your password" class="w3-input w3-border-bottom w3-border-green w3-margin-bottom" id="" required>
            <button style="width: 100%" class="w3-btn w3-green w3-round-large" name="register">Create Account</button>
            <div class="w3-panel">
                <a href="login" class="w3-text-blue w3-left">Login</a>
            </div>
        </form>
    </div>
</body>
</html>