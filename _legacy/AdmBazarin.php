<?php
      include 'includes/database.php';
      if (isset($_POST['approve'])) {
        $sql = "UPDATE transaction SET status='Approved'";
        $res = $conn -> query($sql);
      }
      if(isset($_POST['update'])){
          $sql = "UPDATE earnings SET roll=1";
          $res = $conn ->query($sql);
      }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <title>Admin</title>
</head>
<body class="w3-light-grey">
    <?php
      $sql = "SELECT * FROM transaction WHERE status='Pending'";
      $res = $conn -> query($sql);
      foreach ($res as $row):
    ?>
    <div class="w3-panel w3-card">
      <p class="w3-text-blue">Name REf: <span class='w3-text-black'><?=$row['email']?></span> </p>
      <p class="w3-text-blue">Amount: <span class='w3-text-black'><?=$row['RecAmount']?></span></p>
      <p class="w3-text-blue">Phone: <span class='w3-text-black'><?=$row['phone']?></span></p>
    </div>
    <?php endforeach?>
    <form action="AdmBazarin" method="post">
      <button name="approve" class="w3-btn w3-blue w3-round w3-margin">Approve all</button>
    </form>
        <form action="AdmBazarin" method="post">
      <button name="update" class="w3-btn w3-blue w3-round w3-margin">Update users</button>
    </form>
</body>
</html>