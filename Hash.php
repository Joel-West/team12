<?php
  $Password = $_REQUEST['Password'];
  $Hashed = array('hash' => password_hash($Password, PASSWORD_DEFAULT));
  echo json_encode($Hashed);
?>