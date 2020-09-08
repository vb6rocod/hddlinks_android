<?php
include ("../common.php");
//echo "cff.php?loc=https://www.moviehdkh.com&cookie=moviehdkh.dat&dest=".urlencode("moviehdkh_f.php?page=1&tip=release&title=moviehdkh&link=");
//die();
$loc=$_GET['loc'];
$cookie=$_GET['cookie'];
$dest=urldecode($_GET['dest']);
$firefox = $base_pass."firefox.txt";
$soap=$base_cookie.$cookie;
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($firefox)) { // ua firefox
 $ua=file_get_contents($firefox);
 if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
  $h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
  file_put_contents($soap,$h1);
  unlink ("/storage/emulated/0/Download/cookies.txt");
 } elseif (file_exists($base_cookie."cookies.txt")) {
  $h1=file_get_contents($base_cookie."cookies.txt");
  file_put_contents($soap,$h1);
  unlink ($base_cookie."cookies.txt");
 }
if (file_exists($soap)) {
$x=file_get_contents($soap);
if (preg_match("/moviehdkh\.com	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
 $cc=trim($m[1]);
else
 $cc="";
} else {
 $cc="";
}
//print_r ($m);
//////////////////////////////////////////////////
$cf="cf_clearance";
 // test
  $l=$loc;
  /*
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $soap);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close ($ch);
  */
  //echo $h;
  //die();
$cf="cf_clearance";
//$cc="f8fa32636d8645ce09865f61b855587751177be2-1599206311-0-1z1b336d43za7f96604z14ff514f-150";
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: https://www.moviehdkh.com/"."\r\n"
  )
);
$context = stream_context_create($opts);
$html=@file_get_contents($l,false,$context);
//echo $html;
//die();
  if ($html) {
   header('Location: '.$dest);
   exit();
  } else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$loc.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare (save cookie - use cookies.txt add-on)</a>';
   else
    header('Location: cf.php?site='.$loc);
   exit();
  }
} else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$loc.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare</a>';
   else
    header('Location: cf.php?site='.$loc);
   exit();
}
?>
