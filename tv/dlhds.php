<?php
$link=urldecode(base64_decode($_GET['link']));
$host=urldecode(urldecode($_GET['host']));
$tip=$_GET['tip'];
  $port=$_SERVER['SERVER_PORT'];
  $s=$_SERVER['SCRIPT_NAME'];
  $path="http://127.0.0.1:".$port.$s;
  $p=dirname($path);
//$m=file_get_contents("daddy.txt");
//$x=json_decode($m,1);
//$host=$x['host'];
//$link=$x['link'];
//$auth=$x['auth'];
  //$t1=explode("?",$_SERVER['HTTP_REFERER']);
  //$p=dirname($t1[0]);
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0";
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$host.'/',
  'Origin: '.$host
  );

  //print_r ($head);
  if ($tip=="m3u8") {
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_URL, $auth);
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  //$h1 = curl_exec($ch);

  $h = curl_exec($ch);
  preg_match("/URI\=\"([^\"]+)\"/",$h,$m);
  $h=str_replace($m[1],$p."/dlhds.php?link=".base64_encode($m[1])."&host=".urlencode($host)."&tip=ts",$h);
  $h = preg_replace_callback(
  "/^(?!#).+/m",
  function($a) {
  global $host;
  global $p;
  return $p."/dlhds.php?link=".base64_encode($a[0])."&host=".urlencode($host)."&tip=ts";},
  $h
  );

  /*
  //file_put_contents("dlhds.txt",$h);
  $h=preg_replace("/key\d\./","key.",$h);
  //$h=preg_replace("/top\d\./","top.",$h);
  if (preg_match("/URI\=\"([^\"]+)\"/",$h,$m)) {
  if (!file_exists("dlhds.key")) {
  curl_setopt($ch, CURLOPT_URL, $m[1]);
  $h1 = curl_exec($ch);
  file_put_contents("dlhds.key",$h1);
  }
  $h=str_replace($m[1],""."dlhds.key",$h);
  }
  */
  curl_close($ch);
  file_put_contents("dlhds.txt",$h);
  echo $h;
  } else {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_exec($ch);
   curl_close($ch) ;
   //echo $h;
  }
  //file_put_contents("dlhds.txt",$h);


?>
