<?php
   $l="https://seapi.link/?type=imdb&id=tt15325794&max_results=1";
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   //curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $x=json_decode($h,1);
   print_r ($x);

  die();
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Upgrade-Insecure-Requests: 1',
  'Sec-Fetch-Dest: document',
  'Sec-Fetch-Mode: navigate',
  'Sec-Fetch-Site: none',
  'Sec-Fetch-User: ?1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch,CURLOPT_REFERER,"https://seapi.link");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

?>
