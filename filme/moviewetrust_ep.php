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
$fs_target="moviewetrust_fs.php";
$has_img="yes";
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
if (file_exists($base_pass."tmdb.txt"))
  $key=file_get_contents($base_pass."tmdb.txt");
else
  $key="";
$l="https://api.themoviedb.org/3/tv/".$link."?api_key=".$key;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  $s=json_decode($h,1)['seasons'];
  //print_r ($s);
$sezoane_name=array();
$sezoane=array();
$sezoane_id=array();
for ($k=0; $k<count($s);$k++) {
  //$sezoane_name[]=$s[$k]['name'];  // See Babylon 5
  $sezoane[]=$s[$k]['season_number'];
  if ($s[$k]['season_number'] == 0)
    $sezoane_name[]="Specials";
  else
    $sezoane_name[]="Season ".$s[$k]['season_number'];
  $sezoane_id[]=$s[$k]['id'];
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">'.$sezoane_name[$k].'</a></TD>';
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

for ($x=0; $x<count($sezoane);$x++) {
  $season=$sezoane[$x];
  $sez = $season;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">'.$sezoane_name[$x].'</TD></TR>';
  $n=0;
  $l="https://api.themoviedb.org/3/tv/".$link."/season/".$sez."?api_key=".$key;
  //$l="https://api.themoviedb.org/3/tv/".$sezoane_id[$k]."?api_key=".$key;
  //echo $l;
  //die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT  ,10);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //print_r (json_decode($h,1));
  $r=json_decode($h,1)['episodes'];
  //print_r ($r);
  //die();
  for ($j=0;$j<count($r);$j++) {
  $img_ep="";
  $episod=$r[$j]['episode_number'];
  $ep_tit=$r[$j]['name'];
  if (isset($r[$j]['still_path']))
    $img_ep="http://image.tmdb.org/t/p/w185".$r[$j]['still_path'];
  else
    $img_ep="blank.jpg";
  $year="";
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
}  
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
echo '</table>';
}
?>
</body>
</html>
