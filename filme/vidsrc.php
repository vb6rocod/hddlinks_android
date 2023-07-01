<?php
  $out=$_GET["file"];
  $out=urldecode($out);
  $t1=file_get_contents("vidsrc_time.txt");
  if (time() -$t1 > 60) {
  $l=file_get_contents("vidsrc.txt");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc.stream/',
  'Origin: https://vidsrc.stream/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  file_put_contents("vidsrc_time.txt",time());
  }
header("location: $out");
?>
