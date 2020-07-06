<?php
include ("../common.php");

$sh=$_GET['link'];
if (!isset($_GET['vid']))
file_put_contents($base_cookie.'sh.dat',$sh);
else {
$vid= $_GET['vid'];
file_put_contents($base_cookie.$vid.'.dat',$sh);
}
?>
