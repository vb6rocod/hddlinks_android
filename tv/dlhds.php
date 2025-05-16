<?php
$link=urldecode(base64_decode($_GET['link']));
$host=urldecode(urldecode($_GET['host']));
$tip=$_GET['tip'];
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0";
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$host.'/',
  'Origin: '.$host
  );
if ($tip=="m3u8") {

  //print_r ($head);
  $path=dirname($link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/URI\=\"([^\"]+)\"/",$h,$m)) {
   $h=str_replace($m[1],str_replace("key2","key",$m[1]),$h);
   //echo $h;
  }
  //if (preg_match_all ("/^(?!#).+/m",$h,$m)) {
  $h = preg_replace_callback(
  "/^(?!#).+/m",
  function($a) {
  global $host;
  global $path;
  return $a[0];
  },
  $h
);
//file_put_contents("dlhd_m3u8.txt",$link."\n".$h);
echo $h;
} elseif ($tip=="key") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //file_put_contents("dlhd_ket.txt",$link."\n".$h);
  echo $h;
} elseif ($tip=="ts") {
//echo $link;
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$host.'/',
  'Origin: '.$host
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_exec($ch);
  curl_close($ch);
  //echo $h;
}
?>
