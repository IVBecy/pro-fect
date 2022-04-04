<?php
#Destroy the session, remove vars and redirect
session_start();
#connect to DB
include("./connect.php");
#Set logout time
$t = time();
$uname = $_SESSION["uname"];
$stmt = $connection->prepare("UPDATE `users` SET `logout-time` = ? WHERE `uname` = ?");
$stmt->bind_param('ss', $t,$uname);
$stmt->execute();
$stmt->close();
session_destroy();
header('Location: ../public/index.html');
?>