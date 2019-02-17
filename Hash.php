<?php
  $password = $_REQUEST['password'];
  $hashed = password_hash($password, PASSWORD_BCRYPT);
  echo (json_encode($hashed));
?>