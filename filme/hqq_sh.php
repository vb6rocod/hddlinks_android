<?php
$vid=$_GET['vid'];
$l="https://hqq.tv/e/".$vid;

$ua     =   $_SERVER['HTTP_USER_AGENT'];
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
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
