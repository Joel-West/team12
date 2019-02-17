<?php
  $password = $_REQUEST['password'];
  $hashed = password_hash($password, PASSWORD_DEFAULT);
  echo (json_encode($hashed));
?>