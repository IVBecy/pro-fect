<?php 
#Turn off all notices
error_reporting(0);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
# If not logged in, redirect to login page
if ($logged_in === false){
  http_response_code(404);
  header("Location: ./index.html");
  die();
}
ob_start();
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
  <script src="../assets/js/ajax.js"></script>
  <script type="text/jsx" src="../assets/js/react-comps.js"></script>
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Pro-fect - Settings</title>
</head>
<style>
input{
  background-color:rgba(0,0,0,0.05);
}
</style>
<body id="feed_bg">
  <div id="overlay"></div>
  <div id="menu-bar"></div>
  <div class="home-bar">
    <div class="align-right">
      <div class="profile-img" id="dropdown-img">
        <?php echo $dir?>
      </div>
      <div id="menu"></div>
    </div>
    <div class="search-div">
      <form method="POST" action="../public/profile-src.php">
        <input class="search-usrs" type="search" name="src_name" placeholder="Search" onkeyup="nameLookup(this.value)" autocomplete="off"></input>
        <div id="name-guess"></div>
      </form>
    </div>
  </div>
  <br>
  <div class="center-container" >
    <div class="settings-menu">
      <form method="POST" action="./settings.php" enctype="multipart/form-data">
        <h2>Account information</h2>
        <hr>
        <h4>Edit profile picture</h4>
        <input type="file" name="profile-img" accept=".png,.jpg,.jpeg"><br>
        <p>This picture will appear whenever someone looks up your profile.</p>
        <hr>
        <h4>Change username:</h4>
        <input type="text" name="uname" value="<?php echo $uname?>"><br>
        <p>Your name appears on your profile and on any post, that you have shared previously.</p>
        <hr>
        <!--
        <h4>Change email</h4>
        <input type="email" name="email" placeholder="New email address"><br>
        <p>Your email is used for notifying you, of any changes regarding the platform or your account.</p>
        <hr>
        -->
        <h4 style="color:red">Delete your account</h4>
        <p style="margin:0">If you delete your account, there is no turning back.</p>
        <button type="button" id="delete-acc-btn" style="background-color:red">Delete account</button>
        <hr>
        <input type="hidden" name="csrftoken" value="<?php echo $_COOKIE["CSRF-Token"]?>"/>
        <?php 
        function update(){
          global $connection, $id, $followers;
          $uname = $_SESSION["uname"];
          # USERNAME
          function updateUsername(){
            global $connection, $id, $followers;
            $uname = $_SESSION["uname"];
            if (isset($_POST["uname"])){
              $new_uname = mysqli_real_escape_string($connection,e($_POST["uname"]));
              $new_uname = strtolower($new_uname);
              echo $new_uname;
              #check for matching usernames in the BD
              $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `uname` = ?");
              $stmt->bind_param('s',$new_uname);
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_row();
              $db_name = $row[0];
              $stmt->close();
              # Check data
              if ($db_name == $new_uname){
                if($new_uname == $uname){} 
                else{
                  $msg = "This username is taken, try something else";
                  # Redirect on error
                  header("Location: ../errors/error.php?error={$msg}");
                  die();
                }
              }
              else{
                #new uname
                $stmt = $connection->prepare("UPDATE `users` SET `uname` = ? WHERE `uname` = ?");
                $stmt->bind_param('ss', $new_uname,$uname);
                if ($stmt->execute() === true){}
                else{
                  $msg =  "Error: $connection->error";
                  # Redirect on error
                  header("Location: ../errors/error.php?error={$msg}");
                  die();
                }
                $_SESSION["uname"] = $new_uname;
                $stmt->close();
              }
            };
          };
          /*
          # EMAIL
          function updateEmail(){
            global $connection, $id, $followers;
            $uname = $_SESSION["uname"];    
            if (isset($_POST["email"])){
              $new_email = mysqli_real_escape_string($connection,e($_POST["email"]));
              $stmt = $connection->prepare("SELECT `email` FROM `users` WHERE `email` = ?");
              $stmt->bind_param('s',$new_email);
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_row();
              $db_email = $row[0];
              $stmt->close();
              # process data
              if ($new_email == $db_email && !empty($new_email)){
                $msg = "This email is taken";
                # Redirect on error
                header("Location: ../errors/error.php?error={$msg}");
                die();
              }
              else if (empty($new_email)){}
              else{
                $stmt = $connection->prepare("UPDATE `users` SET `email` = ? WHERE `uname` = ?");
                $stmt->bind_param('ss', $new_email,$uname);
                if ($stmt->execute() === true){}
                else{
                  $msg =  "Error: $connection->error";
                  # Redirect on error
                  header("Location: ../errors/error.php?error={$msg}");
                  die();
                }
                $stmt->close();
              }
            };
          };
          */
          # PROFILE PIC
          function updateProfImg(){
            global $connection, $id, $followers;
            $uname = $_SESSION["uname"];
            global $connection;
            if (!empty($_FILES["profile-img"])) {		
              if (empty($_FILES["profile-img"]["name"])){}
              else{ 
                $allowed = ["image/png", "image/jpg", "image/jpeg"];
                if (!in_array($_FILES["profile-img"]["type"], $allowed)){
                  $msg = "You are trying to upload a not allowed file type";
                  # Redirect on error
                  header("Location: ../errors/error.php?error={$msg}");
                  die();
                }
                else{
                  $image = $_FILES["profile-img"]["tmp_name"];; 
                  $image = base64_encode(file_get_contents(addslashes($image)));
                  $q = "UPDATE `users` SET `img` = '$image' WHERE `uname` = '$uname'"; 
                  $stmt = $connection->prepare("UPDATE `users` SET `img` = ? WHERE `uname` = ?");
                  $stmt->bind_param('ss', $image,$uname);
                  if ($stmt->execute() === true){}
                  else{
                    $msg =  "Error: $connection->error";
                    # Redirect on error
                    header("Location: ../errors/error.php?error={$msg}");
                    die();
                  }
                  $stmt->close();
                }
              }
            }
          };
          updateUsername();
          #updateEmail();
          updateProfImg();
        }
        if(isset($_POST['submit'])){
          if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
            #Create new csrf token
            createCSRF();
            update();
            header("Location: ./profile.php");
          }
          mysqli_close($connection);
        }
        ?>
        <input type="submit" value="Update" name="submit" style="background-color:#09D202;margin:0">
      </form>
    </div>  
  </div>
</body>
</html>