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
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
if (isset($_POST["link"])) {
$link = urldecode($_POST["link"]);
$id=str_replace(" ","%20",$link);
$title = urldecode($_POST["title"]);
} else {
$id = $_GET["file"];
$title = urldecode($_GET["title"]);
}
$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
//http://tkn.4tube.com/801028665/desktop/1080+720+480+360+240
$l="https://www.4tube.com/videos/".$id."/ceva";
//echo $l;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://www.4tube.com/");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      //curl_setopt ($ch, CURLOPT_POST, 1);
      //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      if (!$h) die();
      $t0=explode('button id="download',$h);
      $t1=explode('data-id="',$t0[1]);
      $t2=explode('"',$t1[1]);
      $id=$t2[0];
      $t1=explode('data-quality="',$h);
      $t2=explode('"',$t1[count($t1)-1]);
      $q=$t2[0];
      //echo $id;
if ($id) {
$filelink="https://tkn.4tube.com/".$id."/desktop/1080+720+480+360+240";
$filelink="https://tkn.kodicdn.com/".$id."/desktop/1080+720+480+360+240";
$filelink="https://token.4tube.com/0000000".$id."/desktop/1080+720+480+360+240";
//echo $filelink;
$head=array('Accept: application/json, text/javascript, */*; q=0.01','Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2','Origin: http://www.4tube.com');
//POST /801039571/desktop/1080+720+480+360+240 HTTP/1.1
$post="/".$id."/desktop/1080+720+480+360+240";
$post="";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "http://www.4tube.com/");
      curl_setopt ($ch, CURLOPT_POST, 1);
      curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      if (!$h) die();
$r=json_decode($h,1);
//echo $h;
//print_r ($r);
//$c=count ($r);
$out=$r[$q]["token"];
//if (!$out) $out=$r["480"]["token"];
//if (!$out) $out=$r["360"]["token"];
//print $out;
//die();
}
$out=str_replace("&amp;","&",$out);
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
  $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode("play").";end";
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
