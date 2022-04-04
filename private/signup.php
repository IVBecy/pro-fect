<?php
if ($_SERVER["REQUEST_METHOD"] != "POST"){
   # Redirect on error
   http_response_code(404);
   include("../errors/404.html");
   die();
}else{
  #Turn off all notices
  error_reporting(0);
  #error_reporting(E_ALL & ~E_NOTICE);
  #Adding the script that connects to the DB
  include("./connect.php");
  #get some vars
  include("./vars.php");
  # check code verify system
  include("./code-check.php");
  # Start session to get vars
  session_start();

  # Get user's input from verify page
  $user_code = $_POST["u_code"];
  $og_code = $_POST["o_code"];

  # Variables from signup cookie
  $uname = $_SESSION["uname"];
  $uname = strtolower($uname);
  $email = $_SESSION["email"];
  $pass = $_SESSION["pass"];

  # Hashing the password
  $hashed_password = password_hash($pass,PASSWORD_BCRYPT);
  #id 
  $id = mt_rand();
  $q = "SELECT `id` FROM `users` WHERE `id` = '$id'";
  $logged_id = mysqli_query($connection,$q);
  $logged_id = mysqli_fetch_row($logged_id);
  $logged_id = $logged_id[0];
  while ($logged_id == $id){
    $id = mt_rand();
    if ($logged_id != $id){
      break;
    };
  };

  #followers and follows array
  $followers = [];
  $follows = [];
  $followers = json_encode($followers);
  $follows = json_encode($follows);
  $followers = openssl_encrypt($followers,"AES-128-CBC",$id);
  $follows = openssl_encrypt($follows,"AES-128-CBC",$id);

  #Appending data to the DB, if email and username are not found in the DB
  # Check for verification code
  if(verifyCode($og_code,$user_code) === 1){
    $stmt = $connection->prepare("INSERT INTO `users` (id,uname,email,pass,followers,follows) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $id,$uname,$email,$hashed_password,$followers,$follows);
    $stmt->execute();
    $stmt->close();
    # Redirect to site ad setup vars
    createCSRF();
    $_SESSION["uname"] = $uname;
    header("Location: ../public/profile");
}else{
  # Redirect on error
  $msg = "Verification failed, try signing up again.";
  header("Location: ../errors/error.php?error={$msg}");
}
  #close connection
  mysqli_close($connection);
}
?>