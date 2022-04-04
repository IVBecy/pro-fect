<?php 
if ($_SERVER["REQUEST_METHOD"] != "POST"){
  # Redirect on error
  http_response_code(404);
  include("../errors/404.html");
  die();
}else{
  #Session (start and vars)
  session_start();
  # Turn off all notices
  error_reporting(0);
  #Adding the script that connects to the DB
  include("./connect.php");
  #getting some vars
  include("./vars.php");

  # CSRF check
  if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
    #Deletion cookie
    $delete_item = mysqli_real_escape_string($connection,e($_COOKIE["ToBeDeleted"]));

    #Delete the post from the DB
    $stmt = $connection->prepare("DELETE FROM `posts` WHERE `name_id` = ? AND `title` = ?");
    $stmt->bind_param('ss',$id,$delete_item);
    $stmt->execute();

    #Create new csrf token
    createCSRF();

    #Delete cookie 
    setcookie("ToBeDeleted", NULL, 0);
    mysqli_close($connection);
    header("Location: ../public/profile");
  }; 
};
?>