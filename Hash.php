<?php
  $Password = $_REQUEST['Password'];
  $Hashed = password_hash($Password, PASSWORD_DEFAULT);
  echo (json_encode($Hashed));
?>