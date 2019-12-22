<?php
$file=urldecode(base64_decode($_GET["file"]));
//echo $file;
parse_str($file,$out);
$link=urldecode($out['link']);
if (strpos($link,"http") === false) $link="https:".$link;
$origin=urldecode($out['origin']);
//echo $link."<BR>".$origin;
//die();
$ua = $_SERVER['HTTP_USER_AGENT'];
$head=array('Origin: '.$origin.'');
//header ('Content-Type: video/mp4');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_NOBODY,1);
$h = curl_exec($ch);
curl_close($ch) ;
//echo $h;
//die();
/*
if ($h) {
$c=base64_decode(json_decode($h,1)['url']);
header("Location: $c");
}
*/

if (preg_match("/Location\:\s+(http.+)/",$h,$m)) {
  $c=trim($m[1]);
  header("Location: $c");
  //echo $c;
}


?>
