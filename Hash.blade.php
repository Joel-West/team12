<?php
  use Illuminate\Support\Facades\Hash;
  $password = $_REQUEST['password'];
  $hashed = Hash::make('password');
  echo (json_encode($hashed));
?>