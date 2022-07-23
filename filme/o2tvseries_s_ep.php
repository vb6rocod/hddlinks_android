<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$year=$_GET['year'];
$sez=$_GET['sez'];
/* ======================================= */
$width="200px";
$height="100px";
$fs_target="o2tvseries_fs.php";
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
$host="https://".parse_url($link)['host'];
//echo $link;
$h1="";
$h2="";
$h3="";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
curl_setopt($ch,CURLOPT_REFERER,$host);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_TIMEOUT, 25);
$h1 = curl_exec($ch);
if (preg_match("/\?page\=2/",$h1)) {
curl_setopt($ch, CURLOPT_URL, $link."?page=2");
$h2 = curl_exec($ch);
}
if (preg_match("/\?page\=3/",$h1)) {
curl_setopt($ch, CURLOPT_URL, $link."?page=3");
$h3 = curl_exec($ch);
}
curl_close ($ch);
$h=$h1.$h2.$h3;
//die();
//echo $h;
$n=0;
echo '<table border="1" width="100%">'."\n\r";
echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';

$videos = explode('a href="/download', $h);
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
$n=0;
foreach($videos as $video) {
  $t1 = explode('"', $video);
  $link = $host."/download".$t1[0];
  $t1 = explode('<h3>', $video);
  $t2 = explode('<', $t1[1]);
  $title = trim($t2[0]);
  $title=prep_tit($title);
  $img_ep=$image;
  $season=$sez;
  $episod="";
  $ep_title="";
  if (preg_match("/S(\d+)E(\d+)\s?(.*)?/i",$title,$m)) {
  //print_r ($m);
  $season=$m[1];
  $episod=$m[2];
  $ep_tit=$m[3];
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;
  }
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
?>
</body>
</html>
