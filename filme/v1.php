<?php
$a=$_SERVER['HTTP_REFERER'];
$f=$_SERVER['REQUEST_URI'];
//file_put_contents("11.txt",$a."\n".$f);
if (preg_match("/mediainfo/",$f)) {
  include ("../common.php");
 //http://localhost/mobile1/scripts/filme/mediainfo/XXVPtK8C44fQroInKbtsRTHEJf0akA8=,161,189,203,164,200,179,137,120,172,142,186,193,182,176,178,159?id=https://vidstream.pro/e/26J3WWZLZJRD?t=4xnZCPckAVAKzA%3D%3D
  $t0=explode("mediainfo",$f);
  $t1=explode("?id=",$t0[1]);
  $t2=explode("?",$t1[1]);

  $host="https://".parse_url($t2[0])['host']."/";
  $l=$host."mediainfo".$t1[0]."?".$t2[1];
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Access-Control-Request-Method: GET',
'Access-Control-Request-Headers: x-requested-with',
'Connection: keep-alive',
'Referer: https://vidstream.pro/',
'Origin: https://vidstream.pro');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
 $file="vidstream".".mcloud";
file_put_contents($base_cookie.$file,$l."\n".$h);
curl_close($ch);
}
?>
