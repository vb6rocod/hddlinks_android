<?php
$l=$_POST['link'];
$l=urldecode($l);
//link=https://primasport.one/512.php&title=XXX Adult TV 12&from=fara&mod=direct
//parse_str($l,$r);
//$l=$r['link'];
//echo $l;
//die();
//$l="https://primasport.one/512.php";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://primasport.one/',
'Origin: https://primasport.one'
);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m))
   echo $m[1];
  else
   echo "BAD";

?>
