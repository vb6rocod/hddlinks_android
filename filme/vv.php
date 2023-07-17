<?php
$l=urldecode($_GET['file']);
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0";
$head=array('User-Agent: '.$ua,
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: https://sbfast.com',
'Connection: keep-alive',
'Referer: https://sbfast.com/');
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
     curl_setopt($ch, CURLOPT_TIMEOUT, 25);
     curl_exec($ch);
     curl_close($ch);
     //echo $h;
?>
