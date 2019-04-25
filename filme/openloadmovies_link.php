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
//moviesplanet_link.php?file=planet&page=https%253A%252F%252Fwww.moviesplanet.tv%252Fmovie%252Favengers-infinity-war-online-free
//&image=r_m.php%3Ffile%3Dhttps%3A%2F%2Fwww.moviesplanet.tv%2Fthumbs%2Fmovie_8e22366d932d7f0c90ac219d7468797e.jpg,Avengers%3A+Infinity+War
//title=Eat%23virgula+Pray%23virgula+Love
//&link=planet
//&page=https%253A%252F%252Fwww.moviesplanet.tv%252Fmovie%252Feat-pray-love-online-free
//&image=r_m.php%3Ffile%3Dhttps%3A%2F%2Fwww.moviesplanet.tv%2Fthumbs%2Fmovie_8b821edea1d49d29c0af4d71cd4cd2ac.jpg

if (isset($_POST["q"])) {
$q=$_POST["q"];
$pg_tit=unfix_t(urldecode($_POST["title"]));
$link=$_POST["link"];
} else {
$q=$_GET["q"];
$pg_tit=unfix_t(urldecode($_GET["title"]));
$link=$_GET["link"];
}
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$requestLink=$link;
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
if (strpos($html,"503 Service") !== false) {
$head=array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate, br',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
 if (strpos($h,"503 Service") !== false) {
  if (strpos($h,'id="cf-dn') === false)
   $q= getClearanceLink_old($h,$requestLink);
  else
   $q= getClearanceLink($h,$requestLink);

  curl_setopt($ch, CURLOPT_URL, $q);
  $h = curl_exec($ch);
  curl_close($ch);
 } else {
    curl_close($ch);
 }

  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
}
$r=parse_url($link);
$host=$r["host"];
$t1=explode('LoadPlayer("',$html);
$t2=explode('"',$t1[1]);
$id=$t2[0];
$t3=explode(',"',$t1[1]);
$t4=explode('"',$t3[1]);
$data=$t4[0];
$post="id=".$id."&data=".$data;
$l="https://".$host."/wp-content/plugins/apiplayer/load.php";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $html;
$t1=explode('sources: [',$html);
$t2=explode('],',$t1[1]);
$s=json_decode("[".$t2[0]."]",1);
//print_r ($s);

for ($k=0;$k<count($s);$k++) {
  if ($s[$k]["label"] == $q) {
   $movie=$s[$k]["file"];
   break;
  }
}
//http://ng1.tvseries.net:82/1536822447/f280d51278edfb37acbff2f804a41b77/LzAxLzEzNS8xMzUuMDA5LkRlc3RpbmF0aW9uLldlZGRpbmcuZmYyWkpnM1QubXA0.mp4
//http://ng1.tvseries.net:82/1536864873/c8f2e2c05dba3c7c2ff3f085c262c2af/LzAxLzEzNS8xMzUuMDA5LkRlc3RpbmF0aW9uLldlZGRpbmcuZmYyWkpnM1QubXA0.mp4

$t1=explode("?",$movie);
   $movie_file=substr(strrchr($t1[0], "/"), 1);
   if (preg_match("/mp4|flv/",$movie_file))
   $srt_name = substr($movie_file, 0, -3)."srt";
   else
   $srt_name= $movie_file.".srt";

  $movie=str_replace("https","http",$movie);
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
//header('Cookie: '.$cookie1);
//header('Referer: http://movietv.to/');
header('Content-Disposition: attachment; filename="'.$movie_name.'"');
header("Location: $movie");
} elseif ($flash == "mp") {
$c="intent:".$movie."#Intent;package=com.mxtech.videoplayer.".$mx.";b.decode_mode=1;S.title=".urlencode($pg_tit).";end";
echo $c;
die();
} elseif ($flash == "chrome") {
  //$movie=str_replace("?",urlencode("?"),$movie);
  //$movie=str_replace("&","&amp;",$movie);
  $c="intent:".$movie."#Intent;package=com.mxtech.videoplayer.".$mx.";b.decode_mode=1;S.title=".urlencode($pg_tit).";end";
  header("Location: $c");
} else {
$type="mp4";
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
jwplayer("container").setup({
"playlist": [{
"image": "'.$image.'",
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
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"androidhls": true,
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
