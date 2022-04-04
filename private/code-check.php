<?php
if ($_SERVER["REQUEST_METHOD"] != "POST"){
  http_response_code(404);
  include("../errors/404.html");
  die();
}else{
    function verifyCode($o,$u){
      # Compare codes
      if ($o == $u){
        $verified = 1;
      }
      else{
        $verified = 0;
      }
      return $verified;
  }
}
?>