<?php
include ("../common.php");
if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
$h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
if (preg_match("/hqq\.tv	FALSE	\/	FALSE	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
  $t= $m[1]-time();
  if ($t>0) {
    file_put_contents($base_cookie."max_time_hqq.txt",$m[1]);
    file_put_contents($base_cookie."hqq.txt",$m[2]);
  }
}
unlink ("/storage/emulated/0/Download/cookies.txt");
}
if (file_exists($base_cookie."cookies.txt")) {
$h1=file_get_contents($base_cookie."cookies.txt");
if (preg_match("/hqq\.tv	FALSE	\/	FALSE	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
  $t= $m[1]-time();
  if ($t>0) {
    file_put_contents($base_cookie."max_time_hqq.txt",$m[1]);
    file_put_contents($base_cookie."hqq.txt",$m[2]);
  }
}
unlink ($base_cookie."cookies.txt");
}
if (file_exists($base_cookie."max_time_hqq.txt")) {
$time_now=time();
$time_exp=file_get_contents($base_cookie."max_time_hqq.txt");
   if ($time_exp > $time_now) {
     $minutes = intval(($time_exp-$time_now)/60);
     $seconds= ($time_exp-$time_now) - $minutes*60;
     if ($seconds < 10) $seconds = "0".$seconds;
     $msg_captcha=" | Expira in ".$minutes.":".$seconds." min.";
   } else
     $msg_captcha="";
} else {
   $msg_captcha="";
}
$vid=$_GET['vid'];
$l="https://hqq.tv/e/".$vid;
$l="https://hqq.tv/player/embed_player.php?vid=".$vid."&autoplay=no";
$ua     =   $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://hqq.tv");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
//$h=urldecode($h);
if (strpos($h,"shh='';") !== false) {
$t1=explode("shh='';",$h);
$t2=explode('<script',$t1[1]);

$h="<script".trim($t2[1]);
//$h=json_encode($h);
//$h=str_replace("\u03","\u00",$h);
//$h=json_decode($h);
//echo $h;
$out = '<!DOCTYPE html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Video player</title>
</head>
<body>
';
$out .= '<script>
var shh="";</script>'.$h.'';
$out .= '<script>
//alert (shh);
var request = new XMLHttpRequest();
var the_data = "";
var php_file="sh.php?link=" + shh;
request.open("GET", php_file, true);
request.send(the_data);
window.parent.document.getElementById("hqq_msg").innerHTML = "'.$msg_captcha.'";
parent.$.fancybox.close();
</script>
';
$out .= '</body>
</html>';
echo $out;
} else {
echo '<script>
parent.$.fancybox.close();
</script>';
}
//$fp = fopen('hqq3.html', 'w');
//fwrite($fp, $out);
//fclose($fp);
//file_put_contents("hqq3.html",$out);
?>
