<?php
  $Password = $_REQUEST['Password'];
  $Hashed = array('hash' => password_hash($Password, PASSWORD_DEFAULT));
  $Hashed = (object) $Hashed;
  echo json_encode($Hashed);
?>