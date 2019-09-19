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


file_put_contents($base_sub."sub_extern.srt",$h);
?>
