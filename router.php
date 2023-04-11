<?php
$f=$_SERVER['REQUEST_URI'];
$a=$_SERVER['HTTP_REFERER'];
if (preg_match("/mcloud\.php/",$a)) {
 if (preg_match("/filme\/[^\/]+\/[^\/]+/",$f)) {
 if (!preg_match("/\/ping\//",$f)) {
 $out="";
 foreach ($_SERVER as $parm => $value) {
  $out .=$parm."=".$value."\n";
 }
 $t1=$_SERVER['DOCUMENT_ROOT'];

 if (preg_match("/\/(?:f|e|embed)\/([a-z0-9]+)/i",$a,$m))
  $file=$m[1].".mcloud";
 else
  $file="error.mcloud";
 file_put_contents($t1."/cookie/".$file,$out);
 }
 }
} else {
 return false;
}

?>
