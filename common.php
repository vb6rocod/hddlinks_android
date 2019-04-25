<?php
$t1 = dirname($_SERVER['SCRIPT_FILENAME']);
$t1 = substr($t1, 0, strripos($t1,'/scripts'));
if (file_exists("/data/data/ru.kslabs.ksweb/tmp/"))
  $base_cookie="/data/data/ru.kslabs.ksweb/tmp/";
else
  $base_cookie=$t1."/cookie/";
if (file_exists("/mnt/sdcard/www/public/cookie/"))
  $base_cookie="/mnt/sdcard/www/public/cookie/";
$base_pass=$t1."/parole/";
$base_fav=$t1."/data/";
$base_sub=$t1."/scripts/subs/";
$base_script=$t1."/scripts/";

$jwv='<script src="../jwplayer.js"></script>';
$skin='{
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
}';
function fix_t($s) {
  $ret=str_replace("&","#amp",$s);
  $ret=str_replace('"','#double',$ret);
  $ret=str_replace("'","#simple",$ret);
  $ret=str_replace(",","#virgula",$ret);
  return $ret;
}
function unfix_t($s) {
  $ret=str_replace("#amp","&",$s);
  $ret=str_replace("#double",'"',$ret);
  $ret=str_replace("#simple","'",$ret);
  $ret=str_replace("#virgula",",",$ret);
  return $ret;
}
?>
