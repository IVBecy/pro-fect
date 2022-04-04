<?php 
if ($_SERVER["REQUEST_METHOD"] != "POST"){
   # Redirect on error
   http_response_code(404);
   include("../errors/404.html");
   die();
}else{
  #Session (start and vars)
  session_start();
  $uname = $_SESSION["uname"];
  #Turn off all notices
  error_reporting(0);
  #Adding the script that connects to the DB
  include("./connect.php");
  #getting some vars
  include("./vars.php");

  #Edited Post and reassign vars
  $newArray = json_decode($_COOKIE["editedPost"],true);
  $newTitle = mysqli_real_escape_string($connection,e($newArray["newTitle"]));
  $newDesc = mysqli_real_escape_string($connection,e($newArray["newDesc"]));
  $oldArray = json_decode($_COOKIE["oldPost"],true);
  $oldTitle = mysqli_real_escape_string($connection,e($oldArray["oldTitle"]));
  $oldDesc = mysqli_real_escape_string($connection,e($oldArray["oldDesc"]));

  #Getting the projects from the database
  $stmt = $connection->prepare("SELECT `title`,`report` FROM `posts` WHERE `name_id` = ? AND `title` = ?");
  $stmt->bind_param('ss', $id,$oldTitle);
  $stmt->execute();
  $result = $stmt->get_result();
  $project = $result->fetch_assoc();
  $stmt->close();

  if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
    #Adding new title and description
    $project["title"] = $newTitle;
    $project["report"] = $newDesc;
    #Create new csrf token
    createCSRF();
    #Delete cookies
    setcookie("editedPost", NULL, 0);
    setcookie("oldPost", NULL, 0); 
    #Making the changes in the Database
    $stmt = $connection->prepare("UPDATE `posts` SET `title` = ?, `report` = ? WHERE `name_id` = ? AND `title` = ?");
    $stmt->bind_param('ssss', $newTitle,$newDesc,$id,$oldTitle);
    $stmt->execute();
    $stmt->close();
    mysqli_close($connection); 
  }
  header("Location: ../public/profile");
}
?>