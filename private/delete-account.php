<?php 
if ($_SERVER["REQUEST_METHOD"] != "POST"){
  # Redirect on error
  http_response_code(404);
  include("../errors/404.html");
  die();
}else{
  #Turn off all notices
  error_reporting(0);
  #Adding the script that connects to the DB
  include("../private/connect.php");
  #Getting some vars
  include("../private/vars.php");
  #Delete posts
  if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
    $stmt = $connection->prepare("DELETE FROM `posts` WHERE `name_id` = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    #Delete user's name from the array of followers
    foreach($followers as $u){
      #delete the follow name
      $stmt = $connection->prepare("SELECT `follows` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$u);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $follower_follows = $row[0];
      $stmt->close();
      #decrypt data
      $follower_follows = openssl_decrypt($follower_follows,"AES-128-CBC",$u);
      $follower_follows = json_decode($follower_follows,true);
      #remove from array
      if (in_array($id,$follower_follows)){
        $pos = array_search($id,$follower_follows);
        unset($follower_follows[$pos]);
        $follower_follows = array_values($follower_follows);
        $follower_follows = json_encode($follower_follows);
        $follower_follows = openssl_encrypt($follower_follows,"AES-128-CBC",$u);
        # run command in db
        $stmt = $connection->prepare("UPDATE `users` SET `follows` = ? WHERE `id` = ?");
        $stmt->bind_param('ss',$follower_follows,$u);
        $stmt->execute();
        $stmt->close();
      }
      #delete the follower name
      $stmt = $connection->prepare("SELECT `followers` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$u);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $follower_followers = $row[0];
      $stmt->close();
      # decrypt data
      $follower_followers = openssl_decrypt($follower_followers,"AES-128-CBC",$u);
      $follower_followers = json_decode($follower_followers,true);
      # remove
      if (in_array($id,$follower_followers)){
        $pos = array_search($id,$follower_followers);
        unset($follower_followers[$pos]);    
        $follower_followers = array_values($follower_followers);
        $follower_followers = json_encode($follower_followers);
        $follower_followers = openssl_encrypt($follower_followers,"AES-128-CBC",$u);
        #run command in db
        $stmt = $connection->prepare("UPDATE `users` SET `followers` = ? WHERE `id` = ?");
        $stmt->bind_param('ss',$follower_followers,$u);
        $stmt->execute();
        $stmt->close();
      };
    }

    #Delete user's name from the array of follows
    foreach($follows as $u){
      #delete the follow name
      $stmt = $connection->prepare("SELECT `follows` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$u);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $follow_follows = $row[0];
      $stmt->close();
      #decrypt data
      $follow_follows = openssl_decrypt($follow_follows,"AES-128-CBC",$u);
      $follow_follows = json_decode($follow_follows,true);
      #remove from array
      if (in_array($id,$follow_follows)){
        $pos = array_search($id,$follow_follows);
        unset($follow_follows[$pos]);
        $follow_follows = array_values($follow_follows);
        $follow_follows = json_encode($follow_follows);
        $follow_follows = openssl_encrypt($follow_follows,"AES-128-CBC",$u);
        # run command in db
        $stmt = $connection->prepare("UPDATE `users` SET `follows` = ? WHERE `id` = ?");
        $stmt->bind_param('ss',$follow_follows,$u);
        $stmt->execute();
        $stmt->close();
      }
      #delete the follower name    
      $stmt = $connection->prepare("SELECT `followers` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$u);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $follow_followers = $row[0];
      $stmt->close();
      #decrypt data
      $follow_followers = openssl_decrypt($follow_followers,"AES-128-CBC",$u);
      $follow_followers = json_decode($follow_followers,true);
      # remove
      if (in_array($id,$follow_followers)){
        $pos = array_search($id,$follow_followers);
        unset($follow_followers[$pos]);
        $follow_followers = array_values($follow_followers);
        $follow_followers = json_encode($follow_followers);
        $follow_followers = openssl_encrypt($follow_followers,"AES-128-CBC",$u);
        #run in db
        $stmt = $connection->prepare("UPDATE `users` SET `follows` = ? WHERE `id` = ?");
        $stmt->bind_param('ss',$follow_followers);
        $stmt->execute();
        $stmt->close();
      }
    };
    #Delete the user's profile in the DB :(
    $stmt = $connection->prepare("DELETE FROM `users` WHERE `id` = ?");
    $stmt->bind_param('s',$id);
    $stmt->execute();
    $stmt->close();
    header("Location: ./logout.php"); 
  }else{
    # REDIRECT TO ERROR PAGE
  }
};
?>