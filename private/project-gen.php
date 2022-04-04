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
  #getting some vars
  include("./vars.php");

  # Getting data from the form
  $title =  mysqli_real_escape_string($connection, e($_POST['title']));
  $desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
  if (!empty($_FILES["preview-img"])) {		
    if (empty($_FILES["preview-img"]["name"])){}
    else{ 
      $allowed = ["image/png", "image/jpg", "image/jpeg"];
      if (!in_array($_FILES["preview-img"]["type"], $allowed)){
        $up_img = false;
      }
      else{
        $image = $_FILES["preview-img"]["tmp_name"];; 
        $image = base64_encode(file_get_contents(addslashes($image)));
        $up_img = true; 
      }
    }
  }
  $likes = [];
  $likes = json_encode($likes);
  $likes = openssl_encrypt($likes,"AES-128-CBC",$id);

  #Check if title exist for user
  $stmt = $connection->prepare("SELECT `title` FROM `posts` WHERE `name_id` = ? AND `title` = ?");
  $stmt->bind_param('ss', $id,$title);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_row();
  $e_t = $row[0];
  $stmt->close();
  if ($e_t == $title || $title == ""){
    $append = false;
  }else{
    $append = true;
  }

  #Insert data
  if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"]) && $append === true){
    #Appending data to the Database
    $t = time();
    $stmt = $connection->prepare("INSERT INTO `posts` (name_id,title,report,time,likes) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssss', $id,$title,$desc,$t,$likes);
    $stmt->execute();
    $stmt->close();
    # Append image
    if ($up_img === true){
      #$q = "UPDATE `posts` SET `prev_img` = '$image' WHERE `name_id` = '$id' AND `title` = '$title'"; 
      #$connection->query($q) === true;
      $stmt = $connection->prepare("UPDATE `posts` SET `prev_img` = ? WHERE `name_id` = ? AND `title` = ?");
      $stmt->bind_param('sss', $image,$id,$title);
      $stmt->execute();
      $stmt->close();
    }
    #Create new csrf token
    createCSRF();
    mysqli_close($connection);
  }
  header("Location: ../public/profile");
}
?>