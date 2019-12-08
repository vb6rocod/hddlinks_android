<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$year=$_GET['year'];
/* ======================================= */
$width="200px";
$height="100px";
$fs_target="movie4k_fs.php";
$has_img="yes";
if (preg_match("/(:|-)?\s+Season\s+(\d+)/i",$tit,$m)) {
  $tit=trim(str_replace($m[0],"",$tit));
}
  $rest = substr($tit, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit=trim(str_replace($m[0],"",$tit));
  } else {
   $year="";
  }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<?php

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2>';

$ua = $_SERVER['HTTP_USER_AGENT'];
$captcha="03";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'X-Requested-With: XMLHttpRequest',
'Referer: https://movie4k.ag/',
'Cookie: MarketGidStorage=0 ; captcha='.$captcha.'; approve=1; _gat=1; approve_search=1',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
//echo $h;
$n=0;
$videos = explode('onclick="showEpi', $h);
$sezoane=array();
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
foreach($videos as $video) {
  preg_match("/Season\s+(\d+)/",$video,$m);
  $sezoane[]=$m[1];
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
$p++;
if ($p == 10) {
 echo '</tr>';
 $p=0;
 }
}
if ($p < 10 && $p > 0 && $k > 9) {
 for ($x=0;$x<10-$p;$x++) {
   echo '<TD></TD>'."\r\n";
 }
 echo '</TR>'."\r\n";
}
echo '</TABLE>';

foreach($videos as $video) {
  preg_match("/Season\s+(\d+)/",$video,$m);
  $season=$m[1];
  $sez = $season;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;
  $vids = explode("href='", $video);
  unset($vids[0]);
  //$videos = array_values($videos);
  $vids = array_reverse($vids);
  foreach($vids as $vid) {
  $img_ep="";
  $episod="";
  $ep_tit="";
  $t2=explode("'",$vid);
  $link=$t2[0];
  preg_match("/season-(\d+)-episode-(\d+)/",$vid,$e);
  $episod=$e[2];
  $t1=explode('src="',$vid);
  $t2=explode('"',$t1[1]);
  $img_ep=$t2[0];
  if (strpos($img_ep,"http") === false && $img_ep) $img_ep="https:".$img_ep;
  if (!$img_ep) $img_ep=$image;
  $img_ep=str_replace("w45","w300",$img_ep);
  $t1=explode('</span>',$vid);
  $t2=explode('<',$t1[1]);
  $ep_tit=trim($t2[0]);
  //$ep_tit=html_entity_decode($t2[0]);
  $ep_tit=str_replace("&nbsp;"," ",$ep_tit);
  $ep_tit=prep_tit($ep_tit);

  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;
  if ($episod) {
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$img_ep."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year;
   if ($n == 0) echo "<TR>"."\n\r";
   if ($has_img == "yes")
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank"><img width="'.$width.'" height="'.$height.'" src="'.$img_ep.'"><BR>'.$ep_tit_d.'</a></TD>'."\r\n";
   else
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank">'.$ep_tit_d.'</a></TD>'."\r\n";
   $n++;
   if ($n == 3) {
    echo '</TR>'."\n\r";
    $n=0;
   }
   }
   
}  
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
  //}
echo '</table>';
}
?>
</body>
</html>
