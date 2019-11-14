<?php
//  return a araay of links
function multilink($l,$cookie) {
 $r=array();
 $ua = $_SERVER['HTTP_USER_AGENT'];
 if (preg_match("/gomostream\.com/",$l)) {
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/var\s+tc\s+\=\s+\'(\S+)\'/mei",$h,$m);
  $tc=$m[1];
  preg_match("/\_token\"\: \"(\S+)\"/mei",$h,$o);
  $token=$o[1];
  $t1=explode("slice(",$h);
  $h1=$t1[1];
  preg_match("/(\d+)\,\s*(\d+)/mei",$h1,$n);
  $a=$n[1];
  $b=$n[2];
  preg_match("/return.*?\"(\d+)\".*?\"(\d+)/mei",$h1,$p);
  $c=$p[1];
  $d=$p[2];
  $e=substr($tc,$a,$b-$a);
  $f=strrev($e);
  $j=$f.$c.$d;
  $l="https://gomostream.com/decoding_v3.php";
  $post="tokenCode=".$tc."&_token=".$token;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'x-token: '.$j.'',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Origin: https://gomostream.com',
  'Connection: keep-alive',
  'Referer: https://gomostream.com');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  foreach ($x as $key => $value) {
   if ($value <> "" && !preg_match("/gomostream\.com/",$value))
     $r[]=$value;
   elseif ($value && preg_match("/gomostream\.com/",$value)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $value);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/Location:\s*(\S+)/i",$h,$z)) {
      $r[]=$z[1];
    }
   }
  }
 }
 return $r;
}
?>
