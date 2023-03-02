<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
$ep_title=prep_tit($ep_title);
$year=$_GET["year"];
/* ====================== */
$fs_target = "flixhq_fs.php";
$width="200px";
$height="100px";
$has_img="no";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<meta charset="utf-8">
<title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2><BR>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
$sezoane=array();
$link_sez=array();
  $id = substr(strrchr($link, "-"), 1);
  $path = substr(parse_url($link)['path'],1);
  $mid=$path;
  $host=parse_url($link)['host'];
  $l="https://api.consumet.org/movies/flixhq/info?id=".urlencode($path);
  $l="https://api.consumet.org/movies/flixhq/info?id=".$path;
  //echo $l;
  $l="https://flixhq.to/ajax/v2/tv/seasons/".$id;
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://flixhq.to');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //curl_close($ch);
//echo $h;
  $videos=explode('<a data-id="',$h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('"',$video);
 preg_match("/Season\s+(\d+)/i",$video,$s);
 $sezoane[$t1[0]]=$s[1];
}
//$r=json_decode($h,1);
//print_r ($r);

$n=0;
$z=1;
$k=0;



echo '<table border="1" width="100%">'."\n\r";

$p=0;
foreach($sezoane as $key => $value) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($value).'">Sezonul '.($value).'</a></TD>';
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
//$z=1;
$p=0;
$k=0;

foreach($sezoane as $key => $value) {
  $sez = $value;
  $season=$value;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;


  $l="https://flixhq.to/ajax/v2/season/episodes/".$key;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  //die();
  $videos=explode('<a id="episode-',$h);
  //foreach($f[$sez] as $k=>$v) {
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
  $img_ep="";
  $episod="";
  $ep_tit="";
  $t1=explode('"',$video);
  $ep_id=$t1[0];
  //$episod = $f[$sez][$k][2];

  $link="episodeId=".$ep_id."&mediaId=".urlencode($mid);
  $t1=explode('title="',$video);
  $t2=explode('"',$t1[1]);
  // title="Eps 6: Mudd&#39;s Women">
  preg_match("/Eps\s+(\d+)\s*\:\s+([^\"]+)/i",$t2[0],$s);
  $episod=$s[1];
  $ep_tit = $s[2];
  $ep_tit = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $ep_tit);
  $img_ep="blank.jpg";

  $year="";
   $epNr=$episod;

  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;
  //$link="moviesjoy_fs.php?tip=series&link=".urlencode($l)."&title=".urlencode(fix_t($tit))."&ep_tit=".urlencode(fix_t($ep_tit1))."&ep=".$epNr."&sez=".$sez."&image=".$image;
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=".$epNr."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&host=".$host;
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
}// end ep.
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }

}
echo '</table>';
curl_close($ch);
?>
</body>
</html>
