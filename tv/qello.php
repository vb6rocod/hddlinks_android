<?php
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
$link=$_GET["link"];
$title=urldecode($link);
$ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
$l="https://qello.com/tv?channel=".urlencode($link);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://qello.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('data-psettings="',$html);
  $t2=explode('"',$t1[1]);
  $out="[".$t2[0]."]";
  $out=htmlspecialchars_decode($out,ENT_QUOTES);
  $out=html_entity_decode($out,ENT_QUOTES);
  //echo $out;
  $x=json_decode($out,1);
  $r=$x[0]["playlistsongsarr"];
  //print_r ($r);
$p=array();
$out="";
$out="#EXTM3U"."\r\n";
#EXT-X-PLAYLIST-TYPE:VOD"."\r\n";
for ($k=0;$k<count($r);$k++) {
  $p["playlist"][$k]["title"]=$r[$k]["title"];
  $p["playlist"][$k]["image"]=$r[$k]["image"];
  $p["playlist"][$k]["sources"][0]["type"]="application/vnd.apple.mpegurl";
  $p["playlist"][$k]["sources"][0]["file"]=$r[$k]["sources"][0]["file"];
  $out .="#EXTINF:-1, ".$r[$k]["title"]."\r\n";
  //$out .="#EXTINF:100.0,"."\r\n";
  $out .=$r[$k]["sources"][1]["file"]."\r\n";
}
//$out .="#EXT-X-ENDLIST";
$play=json_encode($p,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//echo $play;
file_put_contents($base_sub."play.rss",$play);
file_put_contents($base_sub."play.m3u",$out);

$p = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
//echo $url;
$movie=str_replace("/tv/qello.php","",$p)."/subs/play.m3u";
$flash="flash";
if ($flash=="mpc") {
if (file_exists($base_pass."mpc.txt")) {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$movie.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
}
}
elseif ($flash=="direct") {
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="'.$title.'"');
header("Location: $movie");
} elseif ($flash == "mp") {
$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
echo $c;
die();
} elseif ($flash == "chrome") {
  $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header("Location: $c");
} else {
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
"playlist": "../subs/play.rss",
    captions: {
        color: "#FFFFFF",
        fontSize: 20,
        edgeStyle: "raised",
        backgroundOpacity: 0
    },
"height": $(document).height(),
"width": $(document).width(),
"skin": {
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"title": "'.$title.'",
"abouttext": "'.$title.'",
"androidhls": true,
"startparam": "start",
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
