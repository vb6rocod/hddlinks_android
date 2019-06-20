<?php
include ("../common.php");
$cookie=$base_cookie."hqq.txt";
if (file_exists($cookie)) unlink ($cookie);
$ua="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10', #'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0";
$ua = $_SERVER['HTTP_USER_AGENT'];
$l=urldecode($_POST["file"]);

//file_put_contents($base_script."subs/l.txt",$l);
$l=str_replace("&amp;","&",$l);
$l=str_replace("https","http",$l);
$l=str_replace("gt;","gt",$l);
//file_put_contents($base_cookie."cc.txt",$l);
//$l=str_replace("hqq.watch","hqq.tv",$l);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "http://hqq.watch/player/embed_player.php?vid=aGtEK1o2bDc0UytacHJiai9HSUV1Zz09");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);

$h=urldecode($h);
//echo $h;
if (!preg_match("/gt=/",$h))
  echo "Video not found! or bad script";
else {
//file_put_contents("result.txt",urldecode($h));
  $t1=explode("expires",$h);
  $t2=explode("path",$t1[1]);
  echo "expires".$t2[0];
  $y=file_get_contents($cookie);
  $y=str_replace("hqq.watch","hqq.tv",$y);
  file_put_contents($cookie,$y);
}
//echo $h;
?>
