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
$cookie=$base_cookie."jizz.dat";
if (strpos($link,"http") === false) $link="https:".$link;
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //$ua="Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0";
  //$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  $head=array('Cookie: __cfduid=d504034b00b9358db0fbbc4e515abf6941552202973');
  //echo $link;
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER, "http://jizzbunker.com");
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
*/
//echo $html;
$html=@file_get_contents($link);
$link1 = urldecode(str_between($html, "src:'", "'"));
$link1=str_replace("https","http",$link1);
//$link1="http://c26.cdn3x.com/v/xWMsUvq4A0kg1Sm_Leb-iA/1553899666/34/26/98/0002342698.480";
//$link1="http://c18.cdn3x.com/v/OCqN6vBz1otdsRXupaanmw/1553899240/46/48/37/0002464837.480";
//$link1="http://c18.cdn3x.com/v/Z2mSZBGg4l6VX3qrlZ6TUw/1553899444/46/48/37/0002464837.480";
//$link1=$link1."&type=.mp4";
//$link1="http://d11.cdn3x.com/v/iCnipAIE0PaPK3zKbuLhKw/1533588756/98/95/33/0001989533.480";
//$link1="http://dll.cdn3x.com/v/iCnipAIE0PaPK3zKbuLhKw/1533588756/98/95/33/0001989533.480&type=.mp4";
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //                                                                                                                                                                                                                                                                                                                                                                                                                                                                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h1 = curl_exec($ch);
  curl_close($ch);
  echo $h1;
  die();
*/
//$link1=$link1."&video/video.mp4";
//intent:http://d13.cdn3x.com/v/kUgTxvDqtvte3DcHX7wJvA/1533589376/98/95/68/0001989568.480#Intent;package=com.mxtech.videoplayer.pro;browser_fallback_url=http%3A%2F%2Fd13.cdn3x.com%2Fv%2FkUgTxvDqtvte3DcHX7wJvA%2F1533589376%2F98%2F95%2F68%2F0001989568.480;S.title=busty+horny+woman+masturbating+on+web;end
    $out=$link1;
    //$out=$out."?v=video.mp4";
//if ($flash=="mp") $flash="flash";
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
//header('Content-type; application/octet-stream');
header('Content-Disposition: attachment; filename="video.mp4"');
header("Location: $out");
} elseif ($flash == "mp") {
//$c="intent:".$out."#Intent;package=com.mxtech.videoplayer.".$mx.";S.browser_fallback_url=".urlencode($out).";S.title=".urlencode($title).";end";
$c="intent:".$out."#Intent;package=com.mxtech.videoplayer.".$mx.";type=video/mp4;S.title=".urlencode($title).";end";
//$c="intent:".$out."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
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
<script src="../jwplayer.js"></script>

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
"skin": {
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
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
