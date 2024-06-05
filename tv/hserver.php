<?php
//include ("../common.php");
if (isset($_GET["file"])) {
$file=urldecode($_GET["file"]);
  $port=$_SERVER['SERVER_PORT'];
  $s=$_SERVER['SCRIPT_NAME'];
  $path="http://127.0.0.1:".$port.$s;
  $p=dirname($path);
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: https://www.tvonline123.com',
  'Connection: keep-alive',
  'Referer: https://www.tvonline123.com/');
if (preg_match("/cdn\.tv24\.gdn/",$file)) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $file);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$h = curl_exec($ch);
curl_close($ch) ;
$bb=dirname($file);
  $h = preg_replace_callback(
  "/^(?!#).+/m",
  function($a) {
  global $bb;
  global $p;
  return $p."/hserver.php?link=".urlencode($bb."/".$a[0]);},
  $h
  );
  //file_put_contents("hserver.txt",$h);
  echo $h;
}
} else {
$file=urldecode($_GET["link"]);
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: https://www.tvonline123.com',
  'Connection: keep-alive',
  'Referer: https://www.tvonline123.com/');
if (preg_match("/cdn\.tv24\.gdn/",$file)) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $file);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch) ;
}
}
?>
