<?php
/*
echo '
<script>
document.onkeydown =  xx;
function xx(e){
 var instance = $.fancybox.getInstance();
 var charCode = e.keyCode;
 if (charCode == "37"  && instance !== false) {
  var x = document.getElementById("fancy").href;
  $.fancybox.close();
  document.getElementById("fancy").href="imdb_d.php?sens=minus";
  document.getElementById("fancy").click();
 } else if (charCode == "39"  && instance !== false) {
  $.fancybox.close();
  document.getElementById("fancy").href="imdb_d.php?sens=plus";
  document.getElementById("fancy").click();
 }
}
</script>
';
*/
$t1 = dirname($_SERVER['SCRIPT_FILENAME']);
$t1=str_replace("\\","/",$t1);
$t1 = substr($t1, 0, strripos($t1,'/scripts'));

if (file_exists("/data/data/ru.kslabs.ksweb/tmp/"))
  $base_cookie="/data/data/ru.kslabs.ksweb/tmp/";
else
  $base_cookie=$t1."/cookie/";
//echo $base_cookie;
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
  $ret=urldecode(str_replace("%E2%80%A6","...",urlencode($ret)));  //%C2%A0
  $ret=urldecode(str_replace("%C2%A0"," ",urlencode($ret)));
  $ret=trim(preg_replace("/(dublat|in romana|cu sub|gratis|subtitrat|onlin|film|sbtitrat|\shd)(.*)/i","",$ret));
  return $ret;
}
function fixurl($link,$from="") {
 if (substr($link,0,2)=="//") {
   return "https:".$link;
 } elseif (substr($link,0,1)=="/") {
   $host="https://".parse_url($from)['host'];
   return $host.$link;
 } elseif (substr($link,0,4)=="http") {
   return $link;
 } else {
   if (substr($from,-1)=="/") $from .="aaa";
   $p=dirname($from);
   return $p."/".$link;
 }
}
$indirect="/pajalusta\.club|hindipix\.in|waaw\.|hqq\.|realyplayonli\.|strcdn\.org|netu\.wiztube\.xyz|netu\.|div\.str1\.site|fshd\d+\.club|video\.filmeonline|fsohd\.pro/";
?>
