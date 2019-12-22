<?php
$link=urldecode($_GET["file"]);
$base=substr($link,0,strlen($link)-strlen(strrchr($link,"/")))."/";
$ua = $_SERVER['HTTP_USER_AGENT'];
header ('Content-Type: video/mp4');
$ch = curl_init();
curl_setopt($ch, CURLOPT_REFERER,$link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_URL, $base."init.mp4");
curl_exec($ch);
curl_setopt($ch, CURLOPT_URL, $link);
curl_exec($ch);
curl_close($ch) ;
?>
