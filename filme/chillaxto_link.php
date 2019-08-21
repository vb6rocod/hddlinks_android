<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function get_value($q, $string) {
   $t1=explode($q,$string);
   return str_between($t1[1],"<string>","</string>");
}
   function generateResponse($request)
    {
        $context  = stream_context_create(
            array(
                'http' => array(
                    'method'  => "POST",
                    'header'  => "Content-Type: text/xml",
                    'content' => $request
                )
            )
        );
        $response     = file_get_contents("http://api.opensubtitles.org/xml-rpc", false, $context);
        return $response;
    }

include ("../common.php");
include ("../util.php");
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
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

if (isset($_POST["q"])) {
$q=$_POST["q"];
$pg_tit=unfix_t(urldecode($_POST["title"]));
$link=urldecode($_POST["link"]);
} else {
$q=$_GET["q"];
$pg_tit=unfix_t(urldecode($_GET["title"]));
$link=urldecode($_GET["link"]);
}
$movie=urldecode($q);
$t1=explode("?",$movie);
$movie_file=substr(strrchr($t1[0], "/"), 1);
if (preg_match("/mp4|flv/",$movie_file))
   $srt_name = substr($movie_file, 0, -3)."srt";
else
   $srt_name= $movie_file.".srt";
$movie_name = $movie_file.".mp4";
//$r=get_headers($movie);
//print_r ($r);
//die();
////////////////////////////////////////////////////////////////////////////////////////////////
if (!file_exists($base_sub."sub_extern.srt")) {
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
} else {
  $srt=$base_sub."sub_extern.srt";
  $h=file_get_contents($srt);
  $new_file = $base_sub.$srt_name;
  file_put_contents($new_file,$h);
}

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
header('Content-Disposition: attachment; filename="'.$movie_name.'"');
header("Location: $movie");
} elseif ($flash == "mp") {
$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";b.decode_mode=1;S.title=".urlencode($pg_tit).";end";
echo $c;
die();
} elseif ($flash == "chrome") {
  $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";b.decode_mode=1;S.title=".urlencode($pg_tit).";end";
  header("Location: $c");
} else {
if (strpos($movie,".m3u8") === false)
 $type="mp4";
else
 $type="m3u8";
echo '
<!doctype html>
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.$pg_tit.'</title>
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
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": [{
"title": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
"sources": [{"file": "'.$movie.'","type": "'.$type.'"}],
"tracks": [{"file": "../subs/'.$srt_name.'", "default": true}]
}],
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
"autostart": true,
"title": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
"abouttext": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
"androidhls": true,
"startparam": "start",
"fallback": false,
"wmode": "direct",
"stagevideo": true
});
player.addButton(
  //This portion is what designates the graphic used for the button
  "https://developer.jwplayer.com/jw-player/demos/basic/add-download-button/assets/download.svg",
  //This portion determines the text that appears as a tooltip
  "Download Video",
  //This portion designates the functionality of the button itself
  function() {
    //With the below code,
    window.location.href = player.getPlaylistItem()["file"];
  },
  //And finally, here we set the unique ID of the button itself.
  "download"
);
</script>
</div></body>
</HTML>
';
}
?>
