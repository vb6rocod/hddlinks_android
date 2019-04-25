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
$title = urldecode($_GET["title"]);
}
//$html = file_get_contents($link);
$cookie=$base_cookie."sp.dat";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://pl.spankbang.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;


  $t1=explode('data-streamkey="',$html);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $c=file_get_contents($cookie);
  $t1=explode('sb_csrf_session',$c);
  $t2=explode('#',$t1[1]);
  $csrf=trim($t2[0]);
//https://pl.spankbang.com/api/videos/stream
$l="https://pl.spankbang.com/api/videos/stream";
$post="id=".$id."&data=0&sb_csrf_session=".$csrf;

$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Referer: https://pl.spankbang.com/30gee/video/thickness',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-CSRFToken: '.$csrf.'',
'X-Requested-With: XMLHttpRequest');
//echo $post;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt ($ch, CURLOPT_REFERER, "https://event.2target.net");
  //curl_setopt($ch, CURLOPT_COOKIE, $c);
  curl_setopt($ch,CURLOPT_ENCODING, '');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  //curl_setopt($ch, CURLOPT_HTTPGET, false);
  //curl_setopt($ch, CURLOPT_COOKIE, "_ga=GA1.2.1886894430.1543563695; _gid=GA1.2.1431105826.1544626958; ab=2; _gat_gtag_UA_126635533_1=1;");
  //curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
  //curl_setopt($ch, CURLOPT_VERBOSE, true);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $x = curl_exec($ch);
  curl_close($ch);
  //echo $x;
  $r=json_decode($x,1);
  ///print_r($r);
  //die();
  if (isset($r["stream_url_1080p"]) && $r["stream_url_1080p"] !="")
    $out=$r["stream_url_1080p"];
  elseif (isset($r["stream_url_720p"]) && $r["stream_url_720p"] !="")
    $out=$r["stream_url_720p"];
  elseif (isset($r["stream_url_480p"]) && $r["stream_url_480p"] !="")
    $out=$r["stream_url_480p"];
  elseif (isset($r["stream_url_360p"]) && $r["stream_url_360p"] !="")
    $out=$r["stream_url_360p"];
  elseif (isset($r["stream_url_240p"]) && $r["stream_url_240p"] !="")
    $out=$r["stream_url_240p"];
  else
    $out="";

//https://1-211-10851-1.b.cdn13.com/5/0/5060102-720p.mp4?st=jgQnvhK-i9pVWcVgV1bOCA&e=1551385242
//$t0=explode('id="video_player"',$html);
//$t1=explode('source src="',$t0[1]);
//$t2=explode('"',$t1[1]);
//$t3=explode("'",$t2[2]);
//$out=$t2[0];
//if (strpos($out,"http") === false) $out="http:".$out;
if (strpos($out,"http") === false && $out) $out="https:".$out;
$out=str_replace("&amp;","&",$out);
//$out=str_replace("https","http",$out);
//if (!$out) $out="http://127.0.0.1:8080/scripts/filme/out.mp4";
if ($flash=="mpc") {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$out.'"';
  $mpc=trim(file_get_contents($base_pass."vlc.txt"));
  $c = '"'.$mpc.'" --fullscreen --sub-language="ro,rum,ron" "'.$out.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
}
elseif ($flash == "direct") {
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="video/mp4"');
header("Location: $out");
} elseif ($flash == "mp") {
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
echo $c;
} elseif ($flash == "chrome") {
  $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode("play").";end";
  header("Location: $c");
} else {
$out=str_replace("&amp;","&",$out);
//$title="play now..";
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
<script src="../jwplayer.js"></script>

</HEAD>
<body><div id="mainnav">
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"playlist": [{
"sources": [{"file": "'.$out.'", "type": "mp4"}]
}],
"height": $(document).height(),
"width": $(document).width(),
"skin": {
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"autostart": true,
"startparam": "start",
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
