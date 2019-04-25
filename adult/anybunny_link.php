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
}else {
$link = $_GET["file"];
$title=urldecode($_GET["title"]);
}

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://anybunny.com/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$id=str_between($html,"embed/",'"');
//echo $html;
$l="https://vartuc.com/embed/".$id;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://vartuc.com/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  $t1=explode("player.php",$h);
  $t2=explode("'",$t1[1]);
  $ll="https://vartuc.com//kt_player/player.php".$t2[0];
//echo $ll;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $ll);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://vartuc.com/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h1 = curl_exec($ch);
  curl_close($ch);
  //echo $h1;
preg_match("/video_url:([^}]+)/",$h1,$m);
//echo $m[0];
preg_match_all('/(gh\w\w\w)="([^"]+)/',$h1,$m1);
//print_r ($m1);
for ($i=0 ; $i<count($m1[0]);$i++) {
  $link=str_replace($m1[1],$m1[2],$m[0]);
}
//preg_match_all('/(ghc\w\w)="([^"]+)/',$h1,$m1);
//print_r ($m1);

$link=str_replace('"',"",$link);
$link=str_replace("+","",$link);
//echo $link;
//die();
$t1=explode("video_html5_url:",$link);
$link1=trim($t1[1]);
if (!$link1) {
  $t2=explode("video_url:",$link);
  $t3=explode(",video_html5",$t2[1]);
  $link1=$t3[0];
}
$t1=explode("video_url:",$link);
$t2=explode(",video_html5_url",$t1[1]);
$link11=$t2[0];
if (strpos($link1,"xhcdn") !== false) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_REFERER, "http://xhamster.com");
      curl_setopt($ch, CURLOPT_NOBODY,true);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("\n",$t1[1]);
      $link2=trim($t2[0]);
      //echo $ret;
      //die();
      if (!$link2)
        $out=$link1;
      else
       $out=$link2;
} else {
  $out=$link1;
}
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
header('Content-Disposition: attachment; filename="video/mp4"');
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
