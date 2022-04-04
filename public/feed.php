<?php
#Start session and set uname as a var
session_start();
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
  <!--  Google adsense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2431803516113635" crossorigin="anonymous"></script>
  <!-- My scripts -->
  <link rel="stylesheet" href="../assets/css/design.css">
  <script src="../assets/js/ajax.js"></script>
  <script type="text/jsx" src="../assets/js/react-comps.js"></script>
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Pro-fect - Your feed</title>
</head>
<body id="feed_bg">
  <div id="overlay"></div>
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
  <?php    
  #time
  $t = time();
  #collect
  $collection = [];
  #Get posts
  if ($follows){
    foreach($follows as $p){
      $stmt = $connection->prepare("SELECT `name_id`,`title`,`report`,`time`,`prev_img` FROM `posts` WHERE `name_id` = ? AND `time` <= ?");
      $stmt->bind_param('si',$p,$t);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()){
        array_push($collection,$row);
      }
      $stmt->close();
    }
    #Sort the array by time, so newest will appear on the top
    $time = array_column($collection, "time");
    array_multisort($time, SORT_DESC, $collection);
    #Restrict array to show max 300 posts
    array_slice($collection, 0, 300);
  }
  ?>
  <!--show posts-->
  <?php foreach($collection as $k){ 
      #uname
      $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$k["name_id"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $p_name = $row[0];
      $stmt->close();

      #likes  
      $stmt = $connection->prepare("SELECT `likes` FROM `posts` WHERE `name_id` = ? AND `title` = ?");
      $stmt->bind_param('ss',$k["name_id"],$k["title"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $likes = $row[0];
      $stmt->close();

      ## decrypt
      $likes = openssl_decrypt($likes,"AES-128-CBC",$k["name_id"]);
      $likes = json_decode($likes,true);
      if (in_array($id,$likes)){
        $txt = '<i class="fas fa-star"></i>Unstar ('.count($likes).')';
      }else{
        $txt = '<i class="far fa-star"></i>Star ('.count($likes).')';
      };
    ?>
    <div class="post">
      <h6 class="posted-by">Posted by <a style="color:black" href="<?php echo "./".$p_name?>"><?php echo $p_name?></a></h6>
      <div class="project" id="<?php echo $k["title"]?>">
        <h2 id="title"><?php echo $k["title"];?></h2>
        <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        <?php if ($k["prev_img"]){echo'<img class="post-preview-img" src="data:image/jpeg;base64,'.$k["prev_img"].'"/>';}?>
      </div>
      <hr>
      <div class="post-actions">
        <div onclick="Starring('<?php echo $k['title']?>','<?php echo $k['name_id']?>')" id="<?php echo $k["title"],$k["name_id"],"star";?>"><button class="actions"><?php echo $txt;?></button></div>
      </div>
    </div>
    <!--
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2431803516113635" crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="-6t+ed+2i-1n-4w"
     data-ad-client="ca-pub-2431803516113635"
     data-ad-slot="3378086859">
    </ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
    -->
    <br>
    <br>
  <?php }?>
</body>
</html>