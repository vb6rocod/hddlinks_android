<?php
//include ("../common.php");
$file=urldecode(base64_decode($_GET["file"]));
$file=$_GET["file"];
//echo $file;
parse_str($file,$out);
//$link=urldecode($out['link']);
//if (strpos($link,"http") === false) $link="https:".$link;
//$origin=urldecode($out['origin']);
$origin="https://play.playm4u.xyz";
//echo $link."<BR>".$origin;
//die();
$ua = $_SERVER['HTTP_USER_AGENT'];
//$cookie=$base_cookie."gg.dat";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
$head=array('Origin: '.$origin.'');
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: '.$origin.'',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
header ('content-type: application/octet-stream');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $file);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
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

if (preg_match("/Location\:\s+(http.+)/i",$h,$m)) {
  $c=trim($m[1]);
  $c .="|Origin=".urlencode("https://play.playm4u.xyz");
/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $c);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER,$c);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_NOBODY,1);
$h = curl_exec($ch);
curl_close($ch) ;
echo $h;
*/
  header("Location: $c");
//  echo $c;
//print $c;
}


?>
