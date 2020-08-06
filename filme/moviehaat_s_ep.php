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
$fs_target="moviehaat_fs.php";
$has_img="no";
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
$l=$link;
//echo $l;
$sezoane=array();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  //curl_close($ch);
  $n=0;
  $videos = explode('href="', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $link=$l.$t1[0];
    if (preg_match("/Season(\s*\-\s*|\s*)(\d+)/i",$video,$y)) {
    $s=$y[2];
    if ($s) $sezoane[$s]=$link;
    }
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;
$c=count($sezoane);
foreach($sezoane as $key=>$value) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($key).'">Sezonul '.($key).'</a></TD>';
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

foreach($sezoane as $key=>$value) {
  $sez = $key;
  $season = $sez;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;
  //echo $value;
  curl_setopt($ch, CURLOPT_URL, $value);
  $html = curl_exec($ch);
  //echo $html;
  $vids = explode('href="', $html);
  unset($vids[0]);
  //$videos = array_values($videos);
  $vids = array_values($vids);
  foreach($vids as $vid) {
  $img_ep="";
  $episod="";
  $ep_tit="";
  $t2=explode('"',$vid);
  $link=$value.$t2[0];

  $img_ep=$image;
  $title=urldecode($t2[0]);
  //$ep_tit=html_entity_decode($t2[0]);
  $title=str_replace("&nbsp;"," ",$title);
  $title=prep_tit($title);
  $ep_tit="";
  // S01 E01 - A Stitch in Time.mkv
  if (preg_match("/S\d+\s*E(\d+)/i",$title,$m))
    $episod=$m[1];
  elseif (preg_match("/\d+x\s*(\d+)/i",$title,$m))
    $episod=$m[1];
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
echo '</table>';
}
curl_close($ch);
?>
</body>
</html>
