<?php
include ("../common.php");
$file=$_GET["file"];
$ua = $_SERVER['HTTP_USER_AGENT'];
if (strpos($file,"filmecinema.net") !== false)
 $cookie=$base_cookie."biz.dat";
elseif (strpos($file,"cinebloom") !== false)
 $cookie=$base_cookie."cinebloom.txt";
else
 $cookie=$base_cookie."hdpopcorns.dat";
if (strpos($file,"5movies")=== false) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $file);
curl_setopt($ch, CURLOPT_REFERER,$file);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$res = curl_exec($ch);
$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
curl_close($ch) ;
echo $res;
} else {
include ("../cloudflare1.php");
$cookie=$base_cookie."hdpopcorns.dat";
echo cf_pass($file,$cookie);
}
?>
