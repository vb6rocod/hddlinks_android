<?php
class gomo {
 private function strSlice($str, $start, $end) {
	$end = $end - $start;
	return substr($str, $start, $end);
 }
 function gomo_r($l) {
  $x=array();
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://autoembed.to',
  'Origin: https://autoembed.to',
  'Upgrade-Insecure-Requests: 1');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close ($ch);
   if (preg_match("/var\s*tc\s*\=\s*\'([^\']+)\'/",$h,$a)) {
   $tc=$a[1];
   preg_match("/_token\"\:\s*\"([^\"]+)/",$h,$t);
   $token=$t[1];
   preg_match("/slice\((\d+)\,(\d+)/",$h,$b);
   $b1=$b[1];
   $b2=$b[2];
   preg_match("/ \+ \"(\d+)\"\+\"(\d+)\"/",$h,$c);
   $c1=$c[1];
   $c2=$c[2];
   $x1=$this->strSlice($tc,$b1,$b2);
   $x2=strrev($x1);
   $x3=$x2.$c1.$c2;
   $l="https://gomo.to/decoding_v3.php";
   $post="tokenCode=".$tc."&_token=".$token;
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
   'Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'x-token: '.$x3,
   'X-Requested-With: XMLHttpRequest',
   'Content-Length: '.strlen($post),
   'Origin: https://gomo.to',
   'Connection: keep-alive',
   'Referer: https://gomo.to/'
   );

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
   //curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close ($ch);
   $x=json_decode($h,1);

   }
   return $x;
 }
}
?>
