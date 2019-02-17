<?php
  $Password = $_REQUEST['Password'];
  $Hashed = $_REQUEST['Hashed'];
  $Bool = password_verify($Password,$Hashed);
  echo (json_encode($Bool));
?>