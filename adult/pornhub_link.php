<?php
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
$l=str_replace(" ","%20",$link);
$title = urldecode($_POST["title"]);
} else {
$l = $_GET["file"];
$title=$_GET["title"];
}
$link="https://www.pornhub.com/embed/".$l;
$link="https://www.pornhub.com/view_video.php?viewkey=".$l;
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
//720","videoUrl":"
$l=str_between($html,'quality":"720","videoUrl":"','"');
$l=str_replace('\\',"",$l);
if (!$l) {
$l=str_between($html,'quality":"480","videoUrl":"','"');
$l=str_replace('\\',"",$l);
}
$out=$l;
//echo $l;
//die();
/*
$h=substr(trim(str_between($html,'var flashvars =','utmSourc')),0,-1);
//echo $h;
$r=json_decode($h,1);
print_r ($r);

foreach($r as $key => $r1) {
 //echo $r1;
 if (strpos($key,'quality_') !== false) {
   $out=$r1;
   break;
 }

}
*/
$out=str_replace("https","http",$out);
//echo $out;
//die();
/*
$t1=explode("var player_quality_720p",$html);
$t2=explode('"',$t1[1]);
$part1=$t2[1];
$t2=explode('"',$t1[2]);
$part2=$t2[1];
if (strpos($part1,"http") !== false)
 $out=$part1.$part2;
else
 $out=$part2.$part1;
if (!$out) {
$t1=explode("var player_quality_480p",$html);
$t2=explode('"',$t1[1]);
$part1=$t2[1];
$t2=explode('"',$t1[2]);
$part2=$t2[1];
if (strpos($part1,"http") !== false)
 $out=$part1.$part2;
else
 $out=$part2.$part1;
}
*/
//$t1=explode("http://cv.pornhub.phncdn.com/videos/",$html);
//$t2=explode('"',$t1[1]);
//$out="http://cv.pornhub.phncdn.com/videos/".$t2[0];
//$out=$link;
//echo $out;
//die();
if (strpos($out,"http") === false) $out="";
if ($flash == "direct") {
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="video/mp4"');
header("Location: $out");
} elseif ($flash == "mp") {
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
echo $c;
} elseif ($flash == "chrome") {
  //$title="Play";
  $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header("Location: $c");
} else {
$out=str_replace("&amp;","&",$out);
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
