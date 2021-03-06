<?php
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Getting some vars
include("../private/vars.php");
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <!-- My scripts -->
  <link rel="stylesheet" href="/../assets/css/design.css">
  <script type="text/jsx" src="/../assets/js/index.js"></script>
  <title>Profect - Error</title>
</head>
<body>
  <div class="flex-container">
    <div class="flex-child">
      <div class="center-container">
        <img id="index-img" src="/assets/imgs/logo.png" alt="">
        <h5 id="index-desc">A social media platform developed for sharing projects.</h5>
      </div>
    </div>
    <div class="flex-child">
      <div class="center-container" id="error-box">
        <h4><?php echo e($_GET["error"])?></h4>
      </div>
    </div>
  </div>
</body>
</html>