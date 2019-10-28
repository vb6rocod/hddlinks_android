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
/* ------------------------------------------------------- */
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
/* ------------------------------------------------------- */
$jwv='<script src="../jwplayer.js"></script>';
$skin='{
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
function prep_tit($s) {
  $ret=htmlspecialchars_decode($s,ENT_QUOTES);
  $ret=html_entity_decode($ret,ENT_QUOTES);
  $ret=str_replace("&#8211;","-",$ret);
  $ret=str_replace("&#8217;","'",$ret);
  $ret=str_replace("&#8211","",$ret);
  $ret=urldecode(str_replace("%E2%80%93","-",urlencode($ret)));
  $ret=trim(preg_replace("/(dublat|in romana|cu sub|gratis|subtitrat|onlin|film|sbtitrat|\shd)(.*)/i","",$ret));
  return $ret;
}
$indirect="/streamplay1\.|thevideo\.|vev\.|vidup\.|hindipix\.in|waaw|hqq\.|pajalusta\./";
?>
