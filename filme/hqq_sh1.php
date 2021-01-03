<?php
// https://s3.netu.tv/download/QCIhc35H51n_Zms0aJSqVg/1609108972/flv/api/files/videos/2020/12/25/1608925041mf7wz.mp4
// https://s5.netu.tv/download/xmXqWpa0qicdD4C_qQAMbg/1609108972/flv/api/files/videos/2020/12/26/1608936325rgsq1.mp4
// 1608925041mf7wz
// https://cdn-s3.cfeucdn.com/flv/api/files/thumbs_new/2020/12/25/1608925041mf7wz/1608925041mf7wz-640x480-1.jpg
// https://s3.netu.tv/download/QCIhc35H51n_Zms0aJSqVg/1609108972/flv/api/files/videos/2020/12/25/1608925041mf7wz.mp4
// https://filme24.club/netuID.php?id=269205228258235212253253261241232229194271217271255
include ("../common.php");
if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
$h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
if (preg_match("/hqq\.tv	\w+	\/	\w+	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
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
if (preg_match("/hqq\.tv	\w+	\/	\w+	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
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
//$vid="RDNpZFpLTTl3cUtSN1piZUEvem5qZz09";
$l="https://hqq.tv/e/".$vid;
//$l="https://netu.io/e/".$vid;
//$l="https://hqq.to/f/RDNpZFpLTTl3cUtSN1piZUEvem5qZz09";
//$l="https://hqq.to/e/VFhPRPHg09Hk?http_referer=&autoplay=no&embed_from=embed_from";
//$l="https://hqq.tv/player/embed_player.php?vid=RDNpZFpLTTl3cUtSN1piZUEvem5qZz09&need_captcha=1&pop=0";
//echo $l."\n";
//$l="https://hqq.tv/player/embed_player.php?vid=".$vid."&autoplay=no";
if (file_exists($base_cookie."max_time_hqq.txt")) {
$time_now=time();
$time_exp=file_get_contents($base_cookie."max_time_hqq.txt");
   if ($time_exp > $time_now) {
$cookie=$base_cookie."cookies.txt";
$ua     =   $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
//$ua="IE6";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://hqq.tv");
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
$t1=explode('}catch(e){console.warn(e.message);}',$h);
$t2=explode("    };",$t1[1]);
$t3=explode(' /*',$t2[1]);
$h1=str_replace("return decodeURIComponent","shh=r;return decodeURIComponent",$t3[0]);
//echo $h1;
$h1="var shh='';".$h1;
$h1 .="var matches = shh.match(/else\{ video\(\'(\w+)/);";


if (preg_match("/userid\s*\=\s*\"(\w+)\"/",$h,$n)) {
$h1 .= '
// alert (matches[1]);
var request = new XMLHttpRequest();
var the_data = "";
var php_file="sh.php?link=" + matches[1] + "&vid='.$vid.'";
request.open("GET", php_file, true);
request.send(the_data);
document.getElementById("hqq_msg").innerHTML = "'.$msg_captcha.'";
';
//$out='<script data-cfasync="false">'.$out."</script>";
//$out=str_replace("<script>","",$out);
//$out=str_replace("</script>","",$out);
echo $h1;
//} else {
//echo '';
//}
} else {
echo '';
}
}
}
//$fp = fopen('hqq3.html', 'w');
//fwrite($fp, $out);
//fclose($fp);
//file_put_contents("hqq3.html",$out);
?>
