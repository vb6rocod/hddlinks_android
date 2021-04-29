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
 if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
  $h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
  file_put_contents($base_cookie.$cookie,$h1);
  unlink ("/storage/emulated/0/Download/cookies.txt");
 } elseif (file_exists($base_cookie."cookies.txt")) {
  $h1=file_get_contents($base_cookie."cookies.txt");
  file_put_contents($base_cookie.$cookie,$h1);
  unlink ($base_cookie."cookies.txt");
 }
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$host.'&cookie='.$cookie.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare cookie</a>';
   else
    header('Location: cf.php?site='.$host.'&cookie='.$cookie);

?>
