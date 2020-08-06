<?php
include ("../common.php");
$firefox = $base_pass."firefox.txt";
$soap=$base_cookie."cinebloom.txt";
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
 $DEFAULT_CIPHERS =array(
            "ECDHE+AESGCM",
            "ECDHE+CHACHA20",
            "DHE+AESGCM",
            "DHE+CHACHA20",
            "ECDH+AESGCM",
            "DH+AESGCM",
            "ECDH+AES",
            "DH+AES",
            "RSA+AESGCM",
            "RSA+AES",
            "!aNULL",
            "!eNULL",
            "!MD5",
            "!DSS",
            "!ECDHE+SHA",
            "!AES128-SHA",
            "!DHE"
        );
 if (defined('CURL_SSLVERSION_TLSv1_3'))
  $ssl_version=7;
 else
  $ssl_version=0;
  $l="https://www.cinebloom.org";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $soap);
  //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  //curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close ($ch);
  //die();
  if ($info['http_code'] === 200) {
   header('Location: cinebloom_f.php?page=1&tip=release&title=cinebloom&link=');
   exit();
  } else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site=https://www.cinebloom.org#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare</a>';
   else
    header('Location: cf.php?site=https://www.cinebloom.org');
   exit();
  }
} else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site=https://www.cinebloom.org#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare</a>';
   else
    header('Location: cf.php?site=https://www.cinebloom.org');
   exit();
}
?>
