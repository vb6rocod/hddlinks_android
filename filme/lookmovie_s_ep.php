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
$fs_target="lookmovie_fs.php";
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
$cookie=$base_cookie."lookmovie.txt";

$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0";
// $link=str_replace("/view/","/play/",$link);
//$link=str_replace("/view","/watch",$link);
//echo $link;
//print_r ($head);
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,$last_good);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //$h=str_replace(" ","",$h);
  //echo urlencode($h);
  //echo $h;
  if (preg_match("/href\=\s*\"\s*(https.*?\/play\/.*?)\"/",$h,$m)) {
    $l1=$m[1];
    $ref=parse_url($l1)['host'];
  }
  if (preg_match("/player\-iframe\"\s+src\=\"([^\"]+)\"/i",$h,$m)) {
    $l1=$m[1];
    $ref=parse_url($l1)['host'];
  }
  $l1=str_replace("&amp;","&",$l1);
  */
  //echo $l1;
////////////////////////////////////////  check threat-protection
// check [url] => https://slavillibyer.monster/threat-protection?t=4a857e36ab6aea4b1701c5cbf8b4c2a4c0986585
 $l1=str_replace("/view/","/play/",$link);
 $ref="lookmovie2.to";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);

  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $l=$info['url'];
  /////////////////////////////////////////// check threat-protection
  if (preg_match("/threat\-protection/",$l)) {
    $csrf="";
    $key="";
    if (preg_match("/\_csrf\"\s*value\=\"([^\"]+)\"/",$h,$c))
      $csrf=$c[1];
    if (preg_match("/grecaptcha\.execute\(\'([^\']+)\'/",$h,$k))
      $key=$k[1];
    require_once("rec.php");
    $sa="submit";
    $new_host="https://".parse_url($l)['host'].":443";
    $co=str_replace("=",".",base64_encode($new_host));
    $loc="https://".parse_url($l)['host'];
    $token=rec($key,$co,$sa,$loc);
    $post="_csrf=".$csrf."&tk=".$token;
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: '.strlen($post),
    'Origin: https://'.$ref,
    'Connection: keep-alive',
    'Referer: '.$l,
    'Upgrade-Insecure-Requests: 1');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if (isset($info['redirect_url'])) {
     $l=$info['redirect_url'];
     if (preg_match("/second/",$l)) {
     file_put_contents($base_cookie."lookmovie_ref1.txt",$l."|".$ref."|".$csrf);
     echo '<a href="look.html">Solve captcha</a>';
     } else {
       echo 'Try again';
       echo '<script>setTimeout(function(){ history.go(-1); }, 2000);</script>';
     }
     exit;
    }
  }
////////////////////////////////////////
    //echo $h;
  $id="";
  $hash="";
  $ex="";
  if (preg_match("/hash\:\s*[\"|\']([^\"\']+)[\'\"]/",$h,$n))
   $hash=$n[1];
  if (preg_match("/expires\:?\s*\'?(\d+)/",$h,$m))
   $ex=$m[1];
  if (preg_match("/year\:\s*\'(\d+)\'/",$h,$y))
   $year=$y[1];
$t1=explode("window.seasons='",$h);
$t2=explode("';",$t1[1]);
$z=str_replace('\\\\\\"',"'",trim($t2[0]));
$t1=explode("seasons:",$h);
$t2=explode("};",$t1[1]);
$z=str_replace('\\\\\\"',"'",trim($t2[0]));

//die();
$z=str_replace("\\","", $z);
$z=str_replace("\n","",$z);
//echo $z;
preg_match_all("/title\:\s+\'(.*?)\'\,\s+index\:\s+\'(.*?)\'\,\s+episode\:\s+\'(.*?)\'\,\s+id\_episode\:\s+\'?(.*?)\'?\,\s+season\:\s+\'(.*?)\'/si",$z,$m);
//print_r ($m);
$r=array();
for ($k=0;$k<count($m[5]);$k++) {
 $r[$m[5][$k]][$m[3][$k]]=array($m[1][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
}
//////////////////////////////////////////////////////////
$n=0;
foreach($r as $key=>$value) {
  $sezoane[]=$key;
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;

foreach($r as $key=>$value) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($key).'">Sezonul '.($key).'</a></TD>';
$p++;
if ($p == 10) {
 echo '</tr>';
 $p=0;
 }
}
if ($p < 10 && $p > 0 && $k > 9) {
 echo '</TR>'."\r\n";
}
echo '</TABLE>';

foreach($r as $key=>$value) {
  $season=$key;
  $sez = $season;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;

  foreach($r[$sez] as $v) {
  //print_r ($v);
  $img_ep="";
  $episod="";
  $ep_tit="";
  $episod=$v[1];
  $link="https://".$ref."/api/v1/security/episode-access?id_episode=".$v[2]."&hash=".$hash."&expires=".$ex;
  $img_ep="https://lookmovie.io/images/p/w300".$v[3];
  $title=$v[0];
  //$ep_tit=html_entity_decode($t2[0]);
  $title=str_replace("\\","",$title);
  $title=prep_tit($title);
  $ep_tit=trim(preg_replace("/Episode\s+\d+/","",$title));
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;

  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$img_ep."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&slug=".$slug;
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
//}
?>
</body>
</html>
