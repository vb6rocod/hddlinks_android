<!DOCTYPE html>
<?php
//error_reporting(0);
include ("../common.php");
include ("../util.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$year=$_GET['year'];
$sez=$_GET['sez'];
/* ======================================= */
$width="200px";
$height="100px";
$fs_target="dwatchmovies_fs.php";
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
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
$host=parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/imdb\.com\/title\/tt(\d+)/",$h,$i))
    $imdbid=$i[1];
  else
    $imdbid="";
$n=0;
echo '<table border="1" width="100%">'."\n\r";
echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
preg_match_all("/var\s+([a-z0-9_]+)\s*\=\s*\[(.*?)\]/ms",$h,$m);
//print_r ($m);
for ($z=0;$z<count($m[1]);$z++) {
 $code="\$".$m[1][$z]."=array(".$m[2][$z].");";
 eval ($code);
}
preg_match_all("/case\s+(\d+):[\s|\n|\r]+src\s*\=\s+([a-z0-9_]+)\[(\d+)\]/ms",$h,$m);
//print_r ($m);
$id=array();
for ($k=0;$k<count($m[1]);$k++){
 $code="\$z=count("."\$".$m[2][$k].");";
 eval ($code);
  //echo $z."\n";
 if ($m[3][$k]< $z) {
 $code="\$id[".$m[1][$k]."]="."\$".$m[2][$k]."[".$m[3][$k]."];";
 eval ($code);
 }
}
preg_match_all("/(\<\!--)?\s*\<td\>\<a id\=\"(\d+)\"/ms",$h,$m);
$eps=array();
for ($k=0;$k<count($m[1]);$k++) {
 preg_match("/(\d)(\d+)/",$m[2][$k],$p);
 if (!preg_match("/\!/",$m[0][$k])) {
 $ep=$p[2];
 $serv=$p[1];
 $eps[$ep][]=$id[$m[2][$k]];
 }
}
$n=0;
foreach($eps as $key=>$value) {

  $img_ep=$image;
  $season=$sez;
  $episod=$key;
  $ep_tit="";
  $link=json_encode($eps[$key]);
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;

  if ($episod) {
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$img_ep."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&imdb=".$imdbid;
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
