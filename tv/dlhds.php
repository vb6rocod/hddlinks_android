<?php
$link=urldecode(base64_decode($_GET['link']));
$host=urldecode(urldecode($_GET['host']));
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0";
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$host.'/',
  'Origin: '.$host
  );

  //print_r ($head);
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
  //file_put_contents("dlhds.txt",$h);
  $h=preg_replace("/key\d\./","key.",$h);
  //$h=preg_replace("/top\d\./","top.",$h);
  if (preg_match("/URI\=\"([^\"]+)\"/",$h,$m)) {
  if (!file_exists("dlhds.key")) {
  curl_setopt($ch, CURLOPT_URL, $m[1]);
  $h1 = curl_exec($ch);
  file_put_contents("dlhds.key",$h1);
  }
  $h=str_replace($m[1],""."dlhds.key",$h);
  }
  curl_close($ch);
  //file_put_contents("dlhds.m3u8",$h);
  echo $h;

?>
