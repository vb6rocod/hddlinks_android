<?php
include ("common.php");
$q=$_POST["h"];
//$q=$q-240;
//$out = $q."px";
$out=$q;
$file=$base_pass."height.txt";
file_put_contents($file,$out);
?>
