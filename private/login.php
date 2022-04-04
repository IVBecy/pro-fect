<?php
if ($_SERVER["REQUEST_METHOD"] != "POST"){
   # Redirect on error
   http_response_code(404);
   include("../errors/404.html");
   die();
}else{
  #Session (start and vars)
  session_start();
  #Turn off all notices
  error_reporting(0);
  #Adding the script that connects to the DB
  include("./connect.php");
  #Getting some vars
  include("./vars.php");

  # Variables from the form
  $uname =  mysqli_real_escape_string($connection, e($_POST['uname']));
  $uname = strtolower($uname);
  $pass = mysqli_real_escape_string($connection, e($_POST['pass']));

  # Getting the username from the database
  $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s',$uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $logged_uname = $row[0];
  $stmt->close();

  # Getting the password from the database
  $stmt = $connection->prepare("SELECT `pass` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s',$uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $logged_pass = $row[0];
  $stmt->close();

  #Create new csrf token
  createCSRF();

  #Checking for the right data
  if ($uname == $logged_uname) {
    if (password_verify($pass, $logged_pass)) {
      session_regenerate_id();
      $_SESSION["uname"] = $uname;
      header( "Location: ../public/feed" );
    }
    else{
      $logged_in = false;
      $message = "Wrong password, try again.";
    }
  }
  else{
    $logged_in = false;
    $message = "Wrong username.";
  }
  mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--  Jquery link(s)  -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!--  React.js libraries  -->
  <script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
  <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
  <script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
  <!-- Font awesome kit  -->
  <script src="https://kit.fontawesome.com/b82b391bad.js" crossorigin="anonymous"></script>
  <!--  Bootstrap(s)  -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <!-- My scripts -->
  <link rel="stylesheet" href="../assets/css/design.css">
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Pro-fect - Login</title>
</head>
<body id="feed_bg">
<?php if($logged_in == false){?>
 <div class="flex-container">
    <div class="flex-child">
      <div class="center-container">
        <img id="index-img" src="../assets/imgs/logo.png" alt="">
        <h5 id="index-desc">A social media platform developed for sharing projects.</h5>
      </div>
    </div>
    <div class="flex-child">
      <div class="center-container" id="background">
        <form method="POST" action="./login.php">
          <input type="text" name="uname" placeholder="Username" required><br>
          <input type="password" name="pass" placeholder="Password" required><br>
          <p class="bg-danger"><?php echo $message; ?></p>
          <input type="submit" value="Log in" id="signup">
        </form>
        <p>Not a member yet?<br><a href="../public/signup.html">Sign up</a></p>
      </div>
    </div>
  </div>
<?php }?>
</html>