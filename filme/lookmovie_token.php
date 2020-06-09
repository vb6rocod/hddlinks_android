<?php
include ("../common.php");
error_reporting(0);
$token = $_GET['token'];
$id=$_GET['id'];
$slug=$_GET['slug'];
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
function getSiteHost($siteLink) {
		// parse url and get different components
		$port="";
		$siteParts = parse_url($siteLink);
		if (isset($siteParts['port']))
		$port=$siteParts['port'];
		else
		$port="";
		if (!$port || $port==80)
          $port="";
        else
          $port=":".$port;
		// extract full host components and return host
		return $siteParts['scheme'].'://'.$siteParts['host'].$port;
}
if ($slug)
$l="https://false-promise.lookmovie.ag/api/v1/storage/shows/?slug=".$slug."&token=".$token."&sk=null&step=1";
else
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
  if ($slug) {
   $l="https://lookmovie.ag/manifests/shows/".$token."/".$time."/".$id."/master.m3u8";
  } else {
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=true";
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=false";
  }
  //echo $l;
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
  if (!$slug) {
  $r=json_decode($h,1);
  if (isset($r['1080p']))
   $link=$r['1080p'];
  if (isset($r['720p']))
   $link=$r['720p'];
  elseif (isset($r['480p']))
   $link=$r['480p'];
  elseif (isset($r['360p']))
   $link=$r['360p'];
  else
   $link="";
  } else {
  //echo $h;
   $base1=str_replace(strrchr($l, "/"),"/",$l);
   $base2=getSiteHost($l);
   if (preg_match("/\.m3u8/",$h)) {
    $a1=explode("\n",$h);
    for ($k=0;$k<count($a1)-1;$k++) {
     if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
    }
    if ($pl[0][0] == "/")
     $base=$base2;
    elseif (preg_match("/http(s)?:/",$pl[0]))
     $base="";
    else
     $base=$base1;
    if (count($pl) > 1) {
    if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
     preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
    else
     preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
    $max_res=max($m[1]);
    $arr_max=array_keys($m[1], $max_res);
    $key_max=$arr_max[0];
    $link=$base.$pl[$key_max];
   } else {
    $link=$base.$pl[0];
   }
  } else {
   $link=$l;
  }
  }
file_put_contents($base_cookie."look_token.txt",$link);
echo '<font style="color:#64c8ff;font-size: 1em;">'.$link.'</font>';
?>
