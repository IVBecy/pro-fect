<?php
#Turn off all notices
error_reporting(0);
#Adding the script that connects to the DB
include("./connect.php");
include("./vars.php");
# Get the str parsed in at JS
$str = mysqli_real_escape_string($connection,e($_GET["str"]));
$output = "";
$output_arr = [];
# Get all the usernames that start with the str
$q = "SELECT `uname` FROM `users` WHERE `uname` LIKE '$str%' LIMIT 10";
$names = mysqli_query($connection,$q);
while ($row = mysqli_fetch_row($names)) {
  if ($row != ""){
    foreach($row as $n){
      $output .= "$n"."<br>";
      if (in_array($n,$output_arr)){}else{array_push($output_arr,$n);};
    }
  }
}
if(empty($output_arr)){
  echo "No match";
}else{
  foreach($output_arr as $n){
    echo "<h5><a href='./$n'>$n</a></h5>";
  };
}
?>