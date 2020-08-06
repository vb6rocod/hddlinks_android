<?php
include ("../common.php");
$firefox = $base_pass."firefox.txt";
$soap=$base_cookie."soap2day.dat";
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
 // test
  $l="https://soap2day.to";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $soap);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close ($ch);
  //die();
  if ($info['http_code'] === 200) {
   header('Location: soap2day_f.php?page=1&tip=release&title=soap2day&link=');
   exit();
  } else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site=https://soap2day.to#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare</a>';
   else
    header('Location: cf.php?site=https://soap2day.to');
   exit();
  }
} else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site=https://soap2day.to#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare</a>';
   else
    header('Location: cf.php?site=https://soap2day.to');
   exit();
}
?>
