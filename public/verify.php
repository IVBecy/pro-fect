<?php 
if ($_SERVER["REQUEST_METHOD"] != "POST"){
  # Redirect on error
  http_response_code(404);
  include("../errors/404.html");
  die();
}else{
  # Connect 
  include("../private/connect.php");
  # Emailing function
  include("../private/email-conf.php");
  #Turn off all notices
  error_reporting(E_ALL & ~E_NOTICE);
  #cross site scripting prevention
  function e($str){
    return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
  }
  session_start();
  # Set session vars later to use in signup.php
  $_SESSION["email"] = mysqli_real_escape_string($connection, e($_POST['email']));
  $_SESSION["uname"] = mysqli_real_escape_string($connection, e($_POST['uname']));
  $_SESSION["pass"] = mysqli_real_escape_string($connection, e($_POST['pass']));

  # Checking email
  $email = mysqli_real_escape_string($connection, e($_POST['email']));
  $stmt = $connection->prepare("SELECT `email` FROM `users` WHERE `email` = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $logged_email = $result->fetch_row();
  $logged_email = $logged_email[0];
  $stmt->close();
  # Check for the same email
  if($logged_email == $email){
    # Redirect on error
    session_destroy();
    $msg =  "This email is already in use.";
    header("Location: ../errors/error.php?error={$msg}");
  }

  # Check username
  $uname = mysqli_real_escape_string($connection, e($_POST['uname']));
  $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param("s", $uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $logged_uname = $result->fetch_row();
  $logged_uname = $logged_uname[0];
  $stmt->close();
  if($logged_uname == $uname){
    # Redirect on error
    session_destroy();
    $msg = "This username is taken.";
    header("Location: ../errors/error.php?error={$msg}");
  }

  # Generate verification code
  $verification_code__ = random_int(100000, 999999);
  
  # Mail sending
  ## Load HTML and change hard code to data
  $email_body = file_get_contents("../mail/verification.html");
  $email_body = str_replace("{NAME}",$uname,$email_body);
  $email_body = str_replace("{VER-CODE}",$verification_code__,$email_body);
  ## Alt body
  $alt_body = `
  Hi $uname,
  Please verify your Pro-fect account.

  Verification code:

    $verification_code__
  `;
  mailing($email,$uname,$email_body,$alt_body);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--  Browser Icon  -->
  <link rel="icon" href="./assets/imgs/favicon.ico">
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
  <title>Pro-fect - Verify email</title>
</head>
<body>
    <div class="center-container verify-email">
      <h4>We sent you a 6-digit verification code to: <br><?php echo $_POST["email"];?></h4>
      <form action="../private/signup.php" method="POST">
      <input type="text" name="u_code" inputmode="numeric" required><br>
      <input type="hidden" name="o_code" value="<?php echo $verification_code__;?>">
      <input type="submit" value="Verify">
      </form>
    </div>
</body>
</html>