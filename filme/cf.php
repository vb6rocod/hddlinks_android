<?php
include ("../common.php");
$l=$_GET['site'];
$firefox = $base_pass."firefox.txt";
$ua = $_SERVER['HTTP_USER_AGENT'];
file_put_contents($firefox,$ua);
header("Location: ".$l."");
exit();
?>
