<?php
  use Illuminate\Support\Facades\Hash;
  $password = $_REQUEST['password'];
  $hashed = bcrypt($password);
  echo (json_encode($hashed));
?>