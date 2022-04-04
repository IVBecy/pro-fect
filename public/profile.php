<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(0);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
$_SESSION["src_uname"] = $uname;
$src_id = $id;
$_SESSION["src_id"] =  $src_id;
# If not logged in, redirect to login page
if ($logged_in === false){
  http_response_code(404);
  header("Location: ./index.html");
  die();
}
#Getting projects for the user
$collection = [];
$stmt = $connection->prepare("SELECT `title`,`report`,`prev_img` FROM `posts` WHERE `name_id` = ?");
$stmt->bind_param('s',$id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  array_push($collection,$row);
};
$stmt->close();

#Checking if we can show projects
if (count($collection) == 0){
  $show_projects_state = False;
  $msg = "You don't have any projects..."; 
}
else{
  $show_projects_state = True;
  #Reverse the order of the array, so newest will be 1st
  $collection = array_reverse($collection);
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
  <script src="../assets/js/ajax.js"></script>
  <script type="text/jsx" src="../assets/js/react-comps.js"></script>
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Pro-fect - <?php echo $uname;?></title>
</head>
<script type="text/jsx">
  var name
  //follows and followers onclick
  const FollowersOverlay = () => {
    return(
      <div className="popup" id="f"> 
        <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
        <h2>Followers</h2>
        <hr/>
        <?php foreach($followers as $f){
          $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `id` = ?");
          $stmt->bind_param('s',$f);
          $stmt->execute();
          $result = $stmt->get_result();
          $row = $result->fetch_row();
          $f_name = $row[0];
          $stmt->close();
        ?>
          <div id="<?php echo $f_name?>"><a href="<?php echo "./$f_name"?>"><h4><?php echo $f_name?></h4></a></div>
        <?php }?>
      </div>
    )
  }
  const FollowsOverlay = () => {
    return(
      <div className="popup" id="f"> 
        <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
        <h2>Follows</h2>
        <hr/>
        <?php foreach($follows as $f){
          $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `id` = ?");
          $stmt->bind_param('s',$f);
          $stmt->execute();
          $result = $stmt->get_result();
          $row = $result->fetch_row();
          $f_name = $row[0];
          $stmt->close();
        ?>
          <div id="<?php echo $f_name?>"><a href="<?php echo "./$f_name"?>"><h4><?php echo $f_name?></h4></a></div>
        <?php }?>
      </div>
    )
  }
  $(document).ready(() => {
    var followers_btn = document.getElementById("followers");
    var follows_btn = document.getElementById("follows");
    var overlay = document.getElementById("overlay");
    if (followers_btn){
      followers_btn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<FollowersOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times")[0];
          if (x && overlay.style.display == "block") {
            x.onclick = () => {
              overlay.style.display = "none";
            };
          };
        },100)
      }
    }
    if (follows_btn){
      follows_btn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<FollowsOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times")[0];
          if (x && overlay.style.display == "block") {
            x.onclick = () => {
              overlay.style.display = "none";
            };
          };
        },100)
      }
    }
  })
</script>
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
  <div class="center-container">
    <div class="profile-card">
      <?php echo $dir?>
      <h1 id="name-text"><?php echo $uname;?></h1>
      <div class="user-info">
        <h4>Projects: <?php echo count($collection)?></h4>
        <h4 id="followers">Followers: <?php echo count($followers)?></h4>
        <h4 id="follows">Follows: <?php echo count($follows)?></h4>
      </div>
    </div>
  </div>
  <!-- PROJECTS -->
  <?php if ($show_projects_state == True){
    foreach($collection as $k) {
      $stmt = $connection->prepare("SELECT `likes` FROM `posts` WHERE `name_id` = ? AND `title` = ?");
      $stmt->bind_param('ss',$id,$k["title"]);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_row();
      $likes = $row[0];
      $stmt->close();
      # decrypt data
      $likes = openssl_decrypt($likes,"AES-128-CBC",$id);
      $likes = json_decode($likes,true);     
    # Show all the people who stared the post
    $starers = "<div>";
    foreach($likes as $l){
      $stmt = $connection->prepare("SELECT `uname` FROM `users` WHERE `id` = ?");
      $stmt->bind_param('s',$l);
      $stmt->execute();
      $result = $stmt->get_result();
      $src_data = $result->fetch_assoc();
      $star_name  = $src_data["uname"];
      $stmt->close();
      $starers .= "<h5 className='starers'><a href='./$star_name'>$star_name</a></h5><br/>";
    };
    $starers .= "</div>";
    ?>
    <script type="text/jsx">
    // Get Star Gazers
    const TriggerStarInterface = () => {
      var overlay = document.getElementById("overlay");
      overlay.style.display = "block";
      setTimeout(() => { 
        ReactDOM.render(<StarInterface starGazers={<?php echo $starers;?>} />,overlay);
        document.getElementsByClassName("fas fa-times")[0].onclick = () => {
          ReactDOM.unmountComponentAtNode(overlay); 
          setTimeout(() => {overlay.style.display = "none"},70)
        }
      },100);
    };
    </script>
    <div class="post">
      <div class="project" id="<?php echo $k["title"]?>">
        <i class="fas fa-ellipsis-h"></i>
        <div id="project-edits-menu"></div>
        <h2 id="title"><?php echo $k["title"];?></h2>
        <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        <?php if ($k["prev_img"]){echo'<img class="post-preview-img" src="data:image/jpeg;base64,'.$k["prev_img"].'"/>';}?>
      </div>
      <hr>
      <div class="post-actions">
        <div class="actions" id="star"><i class="fas fa-star"></i>Star <?php echo "(".count($likes).")"?></div>
        <div id="star-gazers-reveal"><i class="fas fa-eye" onclick="TriggerStarInterface()"></i></div>
      </div>
    </div>
    <br>
    <br>
  <?php }}else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }?>
</body>
</html>