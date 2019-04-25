<?php
//set_time_limit(0);
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start); 
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini; 
	return substr($string,$ini,$len); 
}
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
if (isset($_POST["link"])) {
$link = urldecode($_POST["link"]);
$link=str_replace(" ","%20",$link);
$title = urldecode($_POST["title"]);
} else {
$link = $_GET["file"];
$title = $_GET["title"];
}
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.tnaflix.com/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  $vid=str_between($h,'VID" type="hidden" value="','"');
  $key=str_between($h,'nkey" type="hidden" value="','"');
  $fid=str_between($h,'vkey" type="hidden" value="','"');
//https://cdn-fck.%sflix.com/%sflix/%s%s.fid?key=%s&VID=%s&premium=1&vip=1&alpha'
//https://cdn-fck.tnaflix.com/tnaflix/437c4fa47135b3be45ea.fid?key=53802a02807aa3209632b764d98c3299&VID=3434025&premium=1&vip=1&alpha
//https://cdn-fck.tnaflix.com/tnaflix/437c4fa47135b3be45ea.fid?key=53802a02807aa3209632b764d98c3299&VID=3434025&nomp4=1&catID=0&rollover=1&startThumb=19&embed=0&utm_source=0&multiview=0&premium=1&country=0user=0&vip=1&cd=0&ref=0&alpha
//https://cdn-fck.tnaflix.com/tnaflix/437c4fa47135b3be45ea.fid?key=53802a02807aa3209632b764d98c3299&VID=3434025&nomp4=1&catID=0&rollover=1&startThumb=19&embed=0&utm_source=0&multiview=0&premium=1&country=0user=0&vip=1&cd=0&ref=0&alpha
$l="https://cdn-fck.tnaflix.com/tnaflix/".$fid.".fid?key=".$key."&VID=".$vid."&rollover=1&startThumb=19&premium=1&country=&user=0&vip=0&cd=u&ref=embed&alpha";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.tnaflix.com/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
//die();
$t1=explode('<videoLink><![CDATA[',$html);
$t2=explode(']]>',$t1[count($t1)-1]);
$link1=$t2[0];
if (strpos($link1,"http") === false && $link1) $link1="https:".$link1;
//$link1 = urldecode(str_between($html, 'contentUrl" content="', '"'));
//if (!$link1) $link1=str_between($html,"html5player.setVideoUrlHigh('","'");
//if (!$link1) $link1=str_between($html,"setVideoUrlLow('","'");
    $out=$link1;
if (strpos($out,"http") === false) $out="";
if ($flash=="mpc") {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$out.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
}
elseif ($flash == "direct") {
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="video.mp4"');
header("Location: $out");
} elseif ($flash == "mp") {
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
echo $c;
} elseif ($flash == "chrome") {
  $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header("Location: $c");
} else {
$out=str_replace("&amp;","&",$out);
$type="mp4";
echo '
<!doctype html>
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.$title.'</title>
<style type="text/css">
body {
margin: 0px auto;
overflow:hidden;
}
body {background-color:#000000;}
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
'.$jwv.'

</HEAD>
<body><div id="mainnav">
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"playlist": [{
"sources": [{"file": "'.$out.'", "type": "'.$type.'"}]
}],
"height": $(document).height(),
"width": $(document).width(),
"skin": '.$skin.',
"autostart": true,
"fallback": false,
"wmode": "direct",
"stagevideo": true
});
</script>
</div></body>
</HTML>
';
}
?>
