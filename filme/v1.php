<?php
$a=$_SERVER['HTTP_REFERER'];
$f=$_SERVER['REQUEST_URI'];
if (preg_match("/\/(?:f|e|embed)\/([a-z0-9]+)/i",$a,$m)) {
if (!preg_match("/\/ping\//",$f)) {
include ("../common.php");
$out="";
foreach ($_SERVER as $parm => $value) {
$out .=$parm."=".$value."\n";
}

 $file=$m[1].".mcloud";
file_put_contents($base_cookie.$file,$out);
}
}
?>
