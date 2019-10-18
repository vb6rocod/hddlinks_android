<?php
$l=$_GET['file'];
$ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER, $link);
  //curl_setopt($ch, CURLOPT_REFERER, "https://vipmovies.to");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  $x = curl_exec($ch);
  curl_close($ch);
  //echo $x;
  preg_match("/Location:\s+(\S+)/",$x,$m);
  header("Location: $m[1]");
?>
