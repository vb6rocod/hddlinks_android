<?php
include ("../common.php");

$sh=$_GET['link'];
file_put_contents($base_cookie.'sh.dat',$sh);
?>
