<?php
include ("../common.php");
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."ffmovies.dat";
//if (file_exists($cookie)) unlink($cookie);
  $l="https://ffmovies.to/tv-series";
  $l="https://ffmovies.to/film/in-the-tall-grass.1q7nx";
  $l="https://ffmovies.to/ajax/film/servers/1qyqw";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
if (strpos($h, "waf-verify"))
  $loc="ffmovies_f.php";
else
  $loc="ffmovies_ff.php?page=1&tip=release&title=ffmovies&link=";
header("Location: $loc");
?>
