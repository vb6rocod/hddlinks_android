<?php
include ("../common.php");
$file=$_GET["file"];
$ua = $_SERVER['HTTP_USER_AGENT'];
if (strpos($file,"filmecinema.net") !== false)
 $cookie=$base_cookie."biz.dat";
elseif (strpos($file,"cinebloom") !== false)
 $cookie=$base_cookie."cinebloom.txt";
elseif (strpos($file,"batflix") !== false)
 $cookie=$base_cookie."batflix.txt";
else
 $cookie=$base_cookie."hdpopcorns.dat";
if (!preg_match("/5movies|filmehd\.|cinebloom\.|batflix\./",$file)) {
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
} elseif (preg_match("/batflix\./",$file)) {
$ua=file_get_contents($base_pass."firefox.txt");
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
} elseif (preg_match("/filmehd\./",$file)) {
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
$head=array('Cookie: cf_clearance=fbbe8f9c57520019735eaa5525d4a3d03c74eb0b-1598774324-0-1z49401450z1b78c8d4z7a4b72cc-150');
$cookie=$base_cookie."hdpopcorns.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $file);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
} elseif (preg_match("/cinebloom\./",$file)) {
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
$cc="e9ed77b6d0353eb9551b09126abd393dfb164cb3-1599934442-0-1z1b336d43za7f96604z14ff514f-250";
$head=array('Cookie: cf_clearance='.$cc);
$cookie=$base_cookie."hdpopcorns.dat";
$cc="e9ed77b6d0353eb9551b09126abd393dfb164cb3-1599934442-0-1z1b336d43za7f96604z14ff514f-250";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $file);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
} else {
include ("../cloudflare1.php");
$cookie=$base_cookie."hdpopcorns.dat";
echo cf_pass($file,$cookie);
}
?>
