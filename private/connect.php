<?php
# All the database data we need to be able to connect
include("../help.php");
# Connecting to the server
$connection = mysqli_connect($DB_SERVER,$DB_USERNAME,$DB_PASSWORD,$DB_NAME);
?>