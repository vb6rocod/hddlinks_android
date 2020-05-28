<?php
include ("../common.php");
$token = $_GET['token'];
$id=$_GET['id'];
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$l="https://false-promise.lookmovie.ag/api/v1/storage/movies?id_movie=".$id."&token=".$token."&sk=null&step=1";
  //echo $l;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: https://lookmovie.ag',
'Connection: keep-alive',
'Referer: https://lookmovie.ag/movies/view/6619188-the-favorite-2019');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_REFERER, "https://lookmovie.ag");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $time=$r['data']['expires'];
  $token=$r['data']['accessToken'];
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=true";
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=false";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, "https://lookmovie.ag");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  if (isset($r['720p']))
   $link=$r['720p'];
  elseif (isset($r['480p']))
   $link=$r['480p'];
  elseif (isset($r['360p']))
   $link=$r['360p'];
  else
   $link="";
file_put_contents($base_cookie."look_token.txt",$link);
echo '<font style="color:#64c8ff;font-size: 1em;">'.$link.'</font>';
?>
