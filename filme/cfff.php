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
if (preg_match("/batflix\.org	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
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
  $htnl = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close ($ch);

//echo $html;
//die();
  if ($info['http_code'] === 200) {
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
