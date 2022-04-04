<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(0);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");

#Post title
$like_title = mysqli_real_escape_string($connection,e($_GET["title"]));
$src_id = mysqli_real_escape_string($connection,e($_GET["id"]));

#Username to like post for
$stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `id` = ?");
$stmt->bind_param('s', $src_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$src_uname = $row[0];
$stmt->close();

#Getting likes from the posts table
$stmt = $connection->prepare("SELECT `likes` FROM `posts` WHERE `name_id` = ? AND `title` = ?");
$stmt->bind_param('ss', $src_id,$like_title);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$likes = $row[0];
$stmt->close();
$likes = openssl_decrypt($likes,"AES-128-CBC",$src_id);
$likes = json_decode($likes,true);
if (in_array($id,$likes)){
  # Remove the user's username from the like list
  unset($likes[array_search($id,$likes)]);
  $likes = array_values($likes);
  echo '<button class="actions"><i class="far fa-star"></i>Star ('.count($likes).')</button>';
}else{
  # Add the user's username to the like list
  array_push($likes,$id);
  echo '<button class="actions"><i class="fas fa-star"></i>Unstar ('.count($likes).')</button>';
}
# encrypt
$likes = json_encode($likes);
$likes = openssl_encrypt($likes,"AES-128-CBC",$src_id);

# update data
$stmt = $connection->prepare("UPDATE `posts` SET `likes` = ?  WHERE `name_id` = ? AND `title` = ?");
$stmt->bind_param('sss', $likes,$src_id,$like_title);
$stmt->execute();
$stmt->close();
?>