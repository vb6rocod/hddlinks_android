<?php
include ("../common.php");
$ua = $_SERVER['HTTP_USER_AGENT'];
$sub=urldecode($_POST["link"]);

$l="https://sub1.hdv.fun/vtt1/".$sub.".vtt";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://sub1.hdv.fun");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);

$h=json_encode($h);
$h=str_replace("\u00e3","\u0103",$h); //mar
$h=str_replace("\u00ba","\u0219",$h);  // si
$h=str_replace("\u00fe","\u021B",$h); //ratiune
$h=str_replace("\u00aa","\u015E",$h); //Si
$h=str_replace("\u00de","\u021A",$h); //NOPTI   (cu virgula)
$h=json_decode($h);
file_put_contents($base_sub."sub_extern.srt",$h);
?>
