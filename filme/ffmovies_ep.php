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
$fs_target="ffmovies_fs.php";
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
/*
if (preg_match("/(.*?)(\d+)$/",$tit,$m)) {
  $season=$m[2];
  $tit=trim($m[1]);
} else {
  $season=1;
}
*/
$ua     =   $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."ffmovies.dat";
$host="fmovies.solar";
$host="ffmovies.co";
$host="www12.fmovies.to";
$host=file_get_contents($base_fav."ffmovies_f.txt");
$time=round(time()/100)*100;
  $l1="https://".$host."/user/ajax/menu-bar?ts=".$time."&_=744";
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host."/tv-series");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  
$l="https://".$host."/ajax/film/servers?id=".$link."&_=840&ts=".$time;
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://'.$host.'/film/in-the-tall-grass.'.$link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
$x=json_decode($h,1);
$h=$x['html'];
//echo $h;
$s=array();
$videos = explode('<li><a', $h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2=explode('"',$t1[1]);
  $href=$t2[0];
  $t3=explode('span>',$t1[1]);
  $t4=explode("<",$t3[1]);
  $ep_tit=trim($t4[0]);
  $t1=explode('data-kname="',$video);
  $t2=explode('"',$t1[1]);
  $t3=explode(":",$t2[0]);
  $episod=$t3[1];
  $sez1=$t3[0];
  $s[$sez1][$episod]=$ep_tit;
}
//die();
//$s=array_unique($s);
//print_r ($s);
//$s1=array_unique($s[1]);
//print_r ($s1);
//die();
//ksort($s);
//ksort($s);

echo '<table border="1" width="100%">'."\n\r";

$p=0;

foreach ($s as $key => $value) {
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
foreach ($s as $key => $value) {
echo '<table border="1" width="100%">'."\n\r";
 $season=$key;

echo '<TR>';
echo '<td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center">Sezonul '.$season.'</TD>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">'."\n\r";
$year="";
$img_ep="";
$sez=$season;
$n=0;
foreach ($s[$key] as $key1=>$value1) {
  $episod=$key1;
  $ep_tit=$value1;
  if ($value1 <> "")
  $ep_tit_d=$season."x".$episod." ".$value1;
  else
  $ep_tit_d=$season."x".$episod;
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$img_ep."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&hash=".$hash;
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
