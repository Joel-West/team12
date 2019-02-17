<?php
  $Password = $_REQUEST['Password'];
  $Hashed = (object) $Password;
  echo json_encode($Hashed);
?>