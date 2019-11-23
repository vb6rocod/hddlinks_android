<?php
include ("../common.php");
$cookie=$base_cookie."azm.dat";
$token=$_POST['token'];
file_put_contents($cookie,$token);
echo $token;
?>
