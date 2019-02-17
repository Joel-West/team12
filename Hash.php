<?php
  $password = $_REQUEST['password'];
  $hashed = Hash::make($password);
  echo (json_encode($hashed));
?>