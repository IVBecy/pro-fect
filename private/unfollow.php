<?php 
if ($_SERVER["REQUEST_METHOD"] != "POST"){
   # Redirect on error
   http_response_code(404);
   include("../errors/404.html");
   die();
}else{
  #Session (start and vars)
  session_start();
  #Username from URL query
  $src_uname = $_SESSION["src_uname"];
  #Turn off all notices
  error_reporting(0);
  #Adding the script that connects to the DB
  include("./connect.php");
  #Getting some vars
  include("./vars.php");

  #ID
  ## followers id
  $stmt = $connection->prepare("SELECT `id` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s', $src_uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $followers_id = $row[0];
  $stmt->close();
  ## follows id 
  $stmt = $connection->prepare("SELECT `id` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s', $uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $follows_id = $row[0];
  $stmt->close();

  #Following system
  ## followers
  $stmt = $connection->prepare("SELECT `followers` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s', $src_uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $followers = $row[0];
  $stmt->close();
  ## follows
  $stmt = $connection->prepare("SELECT `follows` FROM `users` WHERE `uname` = ?");
  $stmt->bind_param('s', $uname);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $follows = $row[0];
  $stmt->close();

  # Decode data
  $followers = openssl_decrypt($followers,"AES-128-CBC",$followers_id);
  $follows = openssl_decrypt($follows,"AES-128-CBC",$follows_id);
  $followers = json_decode($followers,true);
  $follows = json_decode($follows,true);

  #Delete the pov user from the searched user's followers list ## NOT LOGGED IN USER
  unset($followers[array_search($id,$followers)]);
  $followers = array_values($followers);

  #Delete the searched user from the pov user's following list ## LOGGED IN USER
  unset($follows[array_search($followers_id,$follows)]);
  $follows = array_values($follows);

  #Encrypt data
  $followers = json_encode($followers);
  $follows = json_encode($follows);
  $followers = openssl_encrypt($followers,"AES-128-CBC",$followers_id);
  $follows = openssl_encrypt($follows,"AES-128-CBC",$follows_id);

  #Append new follow to the DB ## NOT LOGGED IN
  $stmt = $connection->prepare("UPDATE `users` SET `followers` = ? WHERE `uname` = ?");
  $stmt->bind_param('ss', $followers,$src_uname);
  $stmt->execute();
  $stmt->close();

  #Append new following to the DB ## LOGGED IN
  $stmt = $connection->prepare("UPDATE `users` SET `follows` = ? WHERE `uname` = ?");
  $stmt->bind_param('ss', $follows,$uname);
  $stmt->execute();
  $stmt->close();

  header("Location: ../public/$src_uname");
}
?>