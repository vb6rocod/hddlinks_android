<?php
$p=$_POST;
//$x=json_encode($p);
$post=http_build_query($p);
$host=$p['host'];

$link="https://".$host."/movie/no-hard-feelings-2023";
$cf="https://basic-bundle-solitary-morning-4d74.quamatbanty02.workers.dev/?";
$cf="https://cors-anywhere.azm.workers.dev/";
//$post="action=doo_player_ajax&post=94524&nume=1&type=movie";
//$post="action=doo_player_ajax&post=93599&nume=1&type=movie";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Origin: https://tv.idlixprime.com',
'Referer: '.$link);
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://tv.idlixplus.net',
'Alt-Used: tv.idlixplus.net',
'Connection: keep-alive',
'Referer: '.$link);
  $l="https://".$host."/wp-admin/admin-ajax.php";
  //echo $link;
  //$l=$cf.$l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h=curl_exec($ch);
  curl_close($ch);
  echo $h;
?>
