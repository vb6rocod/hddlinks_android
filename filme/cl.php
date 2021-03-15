<?php
include ("../common.php");
$cookie=$_GET['cookie'];
$host=$_GET['host'];
$target=$_GET['target'];
$title=$_GET['title'];
$firefox = $base_pass."firefox.txt";

if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($firefox)) { // ua firefox
 $ua=file_get_contents($firefox);
 if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
  $h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
  file_put_contents($base_cookie.$cookie,$h1);
  unlink ("/storage/emulated/0/Download/cookies.txt");
 } elseif (file_exists($base_cookie."cookies.txt")) {
  $h1=file_get_contents($base_cookie."cookies.txt");
  file_put_contents($base_cookie.$cookie,$h1);
  unlink ($base_cookie."cookies.txt");
 }
 // test
  $l=$host;
  $www=parse_url($l)['host'];
  $t1=explode(".",$www);
  $k=count($t1);
  $www=$t1[$k-2].".".$t1[$k-1];
  //echo $www;
  //die();
  $x="";
  if (file_exists($base_cookie.$cookie))
   $x=file_get_contents($base_cookie.$cookie);
  if (preg_match("/".preg_quote($www, '/')."	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
   $cc=trim($m[1]);
  else
   $cc="";
//print_r ($m);
//////////////////////////////////////////////////
  $cf="cf_clearance";
  $opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
       "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$host.""."\r\n"
  )
 );
 $context = stream_context_create($opts);
 $h=@file_get_contents($l,false,$context);

  if ($h) {
   header('Location: '.$target.'?page=1&tip=release&title='.$title.'&link=');
   exit();
  } else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$host.'&cookie='.$cookie.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare cookie</a>';
   else
    header('Location: cf.php?site='.$host.'&cookie='.$cookie);
   exit();
  }
} else {
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
 $cc="";
 $l=$host;
 $opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
       "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$host.""."\r\n"
  )
 );
 $context = stream_context_create($opts);
 $h=@file_get_contents($l,false,$context);
  if ($h) {
   header('Location: '.$target.'?page=1&tip=release&title='.$title.'&link=');
   exit();
  } else {
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$host.'&cookie='.$cookie.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare cookie</a>';
   else
    header('Location: cf.php?site='.$host.'&cookie='.$cookie);
   exit();
  }
}
?>
