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
$fs_target="lightdl_fs.php";
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
$link=str_replace(" ","%20",$link);
$ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
$head=array('Accept-Encoding: deflate');
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //$html=file_get_contents($link);
  //echo $html;
  preg_match_all("/(http.+\.mkv)\"/i",$html,$o);
  for ($k=0;$k<count($o[1]);$k++) {
   if (preg_match("/S(\d{1,2})E(\d{1,2})/i",$o[1][$k],$w))
    $sezoane[]=$w[1];
  }
$n=0;
//print_r ($sezoane);
$sezoane=array_unique($sezoane);
echo '<table border="1" width="100%">'."\n\r";

$p=0;
//$c=count($sezoane);
//for ($k=0;$k<$c;$k++) {
foreach($sezoane as $key => $value) {
//if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($value).'">Sezonul '.($value).'</a></TD>';
$p++;
if ($p == 10) {
 echo '</tr>';
 $p=0;
 }
}
echo '</TABLE>';
$season="";
$u=array_unique($o[1]);
//print_r ($u);
$n=0;
//print_r ($sezoane);
foreach($sezoane as $key => $value) {
  if ($value <> $season)
  $season=$value;
  $sez = $season;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';

  $n=0;
  //for ($k=0;$k<count($p[1]);$k++) {
  foreach ($u as $key => $value) {
  $img_ep="";
  $episod="";
  $ep_tit="";
  $link=$value;

  $img_ep=$image;
  if (preg_match("/S(\d{1,2})E(\d{1,2})/i",$link,$w))
   $episod=$w[2];
  else
   $episod="1";
  if ($w[1] == $season) {
  $ep_tit=urldecode(substr(strrchr($link, "/"), 1));
  if (preg_match("/S(\d{1,2})E(\d{1,2})(.+)/i",$ep_tit,$y))
    $ep_tit=$y[3];
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;

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
   /*
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
  */
  }
  }
echo '</table>';
}
?>
</body>
</html>
