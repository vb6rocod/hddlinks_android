<?php
//set_time_limit(0);
error_reporting(0);
include ("../common.php");
$id=""; $pg_id=""; $tit="";
if (isset($_POST["file"])) {
$id = $_POST["file"];
if (array_key_exists("tit",$_POST)) $tit=$_POST["tit"];
if (array_key_exists("pg_id",$_POST)) $pg_id = $_POST["pg_id"];
$title = urldecode($_POST["title"]);
} else {
$id = $_GET["file"];
if (array_key_exists("tit",$_GET)) $tit=$_GET["tit"];
if (array_key_exists("pg_id",$_GET)) $pg_id = $_GET["pg_id"];
$title = urldecode($_GET["title"]);
}
if (strpos($tit,"TV") !== false) $pg_id="9";
if (strpos($tit,"RADIO") !== false) $pg_id="22";
if (strpos($tit,"Koolnet") !== false) $pg_id="5514";

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function enc($string) {
  $local3="";
  $arg1=strlen($string);
  $arg2="mediadirect";
  $l_arg2=strlen($arg2);
  $local4=0;
  while ($local4 < $arg1) {
    $m1=ord($string[$local4]);
    $m2=ord($arg2[$local4 % $l_arg2]);
    $local3=$local3.chr($m1 ^ $m2);
    $local4++;
  }
  return $local3;
}
function rand_string( $length ) {
	$str = "";
	$characters = array_merge(range('a','f'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
 return $str;
}
function search_arr($array, $key, $value)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, search_arr($subarray, $key, $value));
        }
    }

    return $results;
}
function objectToArray($d) {

	if (is_object($d)) {
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

$svr=""; $serv="";
$sub=""; $subtracks="";
$token="";
$str_name="";
$p=array();
$PHP_SELF="";
$Width="";
$Height="";

$filename = $base_pass."seenowtv.txt";
$cookie=$base_cookie."seenowtv.dat";
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
if (file_exists($base_pass."tv.txt")) {
$tv=trim(file_get_contents($base_pass."tv.txt"));
} else {
$tv="dinamic";
}
//echo $id;
if ( is_numeric($id) && $tit !== "RADIO") {
$l="http://www.seenow.ro/smarttv/placeholder/list/id/".$pg_id."/start/0/limit/999";
//$h=file_get_contents($l);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
//echo $h;
$t1=json_decode($h,1);
//print_r ($t1);
//die();
if ($t1) if (array_key_exists("items",$t1)) {
$items=$t1['items'];
//print_r ($items);
$items = array_values($items);

$willStartPlayingUrl = "http://www.seenow.ro/smarttv/historylist/add/id/".$id;
$h=search_arr($items, 'willStartPlayingUrl', $willStartPlayingUrl);
if (!$h) $h=search_arr($items, 'title', $title);
//echo $h[0];
//print $title;
//print_r ($h);
if (sizeof($h)>0 && $pg_id!=22) {
//$t2=file_get_contents($h[0]["playURL"]);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $h[0]["playURL"]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $t2 = curl_exec($ch);
  curl_close($ch);


$h=json_decode($t2);
$p=objectToArray($h);
}  elseif ($pg_id==22) {
  $p=$h[0];
//echo $p;
}
}
if (array_key_exists("streamUrl",$p)) {
$l=$p["streamUrl"];
$t1=explode("token=",$l);
if (sizeof($t1)>1) {
$t2=explode('|',$t1[1]);
$token=$t2[0];
}
$t1=explode('|',$l);
$l=$t1[0];
}
if (!$p) {
$l="http://www.seenow.ro/smarttv/placeholder/view/id/".$id;
//$h=file_get_contents($l);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

$p=json_decode($h,1);
}
//print_r($p);
//die();
if ($p) if (array_key_exists("high quality stream name",$p)) {
$t1="";
if (array_key_exists("streamUrl",$p)) $t1=$p["streamUrl"];
if (!$t1) {
//$t2=file_get_contents($p["playURL"]);
if (array_key_exists("playURL",$p)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $p["playURL"]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $t2 = curl_exec($ch);
  curl_close($ch);



$h=json_decode($t2);
$p=objectToArray($h);
}
}
$str_name=$p["high quality stream name"];
if (array_key_exists("streamUrl",$p)) {
$l=$p["streamUrl"];
$t1=explode("token=",$l);
if (sizeof($t1)>1) {
$t2=explode('|',$t1[1]);
$token=$t2[0];
}
$t1=explode('|',$l);
$l=$t1[0];
}
}
//print_r ($p);
//die();

if($p) {
 $u="user_id=0&transaction_id=0&p_item_id=".$id."&device_id=0&publisher=20";
if (!$token) if (array_key_exists("token-high",$p)) {
 $token=$p["token-high"];
 $l="http://[%server_name%]:1937/live3/_definst_/".$str_name."/playlist.m3u8?".$u."&token=".$token;
} else {
$l="http://178.21.120.198:1935/live3/_definst_/".$str_name."/playlist.m3u8?".$u."&token=".$token;
}
$f=$base_pass."seenow1.txt";
if (array_key_exists("qualities",$p) && file_exists($f)) {
$token=$p['qualities'][0]['token'];
$str_name=$p['qualities'][0]['stream name'];
$app=$p['qualities'][0]['application name'];
if ($app == "seenow")
$l="http://[%server_name%]:8888/".$app."/".$str_name."?user_id=0&transaction_id=0&publisher=24&p_item_id=".$id."&token=".$token;
else
$l="http://[%server_name%]:1937/".$app."/_definst_/".$str_name."/playlist.m3u8?user_id=0&transaction_id=0&publisher=24&p_item_id=".$id."&token=".$token;
} 
 else 
	$site = "flash";
if (array_key_exists("application name",$p)) if ($p["application name"]=="radio") {
$audio=$p["audio stream name"];
$img=$p["image"];
}
}


if ($p) if (array_key_exists("subtitles",$p)) {
$t1=$p["subtitles"];
if (sizeof($t1) > 1) {
   $t2 = search_arr($t1, 'code', 'RO');
   $t3 = $t2[0];
   $sub=$t3["srt"];
}
else
   $sub = $t1;
if ($sub) {
   $t1 = '{"file": "'.$sub.'", "default": true}';
   $subtracks='"tracks": ['.$t1.']';
   }
}
$title = preg_replace('~[^\\pL\d.]+~u', ' ', $title);

if (strpos($base_pass,":") !== false) {
    $title = str_replace("Ş","S",$title);
    $title = str_replace("ş","S",$title);
    $title = str_replace("ș","s",$title);
    $title = str_replace("ș","s",$title);
    $title = str_replace("Ț","T",$title);
    $title = str_replace("ț","t",$title);
    $title = str_replace("ţ","t",$title);
    $title = str_replace("Ț","T",$title);
    $title = str_replace("Ţ","T",$title);
    $title = str_replace("ă","a",$title);
	$title = str_replace("â","a",$title);
	$title = str_replace("î","i",$title);
	$title = str_replace("Î","I",$title);
	$title = str_replace("Ă","A",$title);
}


//$srt_name=$str_name.".srt";
$movie_file=$title.".m3u8";
if ($p) if (array_key_exists("indexUrl",$p)) $svr=$p["indexUrl"];
if ($svr) {
$h=file_get_contents($svr);
$t1=explode('server=',$h);
$t2=explode('&',$t1[1]);
$serv=$t2[0];
}
if ($serv == "") {
  $serv="fms110.mediadirect.ro";
}

if (!($pg_id==22 || $pg_id==5036)) {
if (strpos($tit,"Hustler live") === false) {
$out=str_replace("[%server_name%]",$serv,$l);
$out=str_replace("seenow-smart/_definst_/","seenow/_definst_/mp4:",$out);
} else {
$out=str_replace("[%server_name%]:1937","178.21.120.198:1935",$l);
$out=str_replace("seenow/_definst_/","live3/_definst_/",$out);
$out=str_replace("mp4:",str_replace(' ','',strtolower($title)),$out);
}
$out=str_replace("playlist",$title,$out);
$t1=explode('?',$out);
$out=$t1[0]."?user_id=0&transaction_id=0&publisher=24&p_item_id=".$id."&token=".$token;
} else {
$out=$l;
}
if (strpos($out,"mp4:") !== false)
  $srt_name=$title.".srt";
else {
$t1=explode(".",substr(strrchr($str_name, "/"), 1));
$srt_name = $t1[0].".srt";
}
if (file_exists($base_sub.$srt_name)) unlink($base_sub.$srt_name);

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $sub);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
   }
if ($h) {
   $new_file = $base_sub.$srt_name;
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h);
   fclose($fh);
   
   $subtracks='"tracks": [{"file": "../subs/'.$srt_name.'", "default": true}]';

}
} else {
//$serv="178.21.120.26";
//$out="rtmp://".$serv.":1935/radio/_definst_/".$id.".stream";
//echo $out;
//die();
//$id="5719";
$l="http://www.seenow.ro/smarttv/placeholder/view/id/".$id;
$l="http://www.seenow.ro/windows/historylist/add/id/".$id;
$l="http://www.seenow.ro/smarttv/placeholder/list/id/22/start/0/limit/999";
//$h=file_get_contents($l);
//$l="http://www.seenow.ro/radio-22#kiss-fm";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
//echo $h;
//die();
$p=json_decode($h,1);

//print_r($p);
$x=search_arr($p, 'title', $title);
//print_r ($x);
//die();
$out=$x[0]["streamUrl"];
$img=$x[0]["thumbnail"];
$audio=$x[0]["audio stream name"];
//die();
}
if (preg_match("/tvr/i",$title) || preg_match("/biziday/i",$tit))
 $strech="exactfit";
else
 $strech="bestfit";


//echo $out;
//die();
if ($tit == "RADIO") $flash="direct";
if ($flash=="mpc") {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$out.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
}
if ($flash == "direct") {
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="playlist.m3u8"');
header("Location: $out");
} elseif ($flash == "mp") {
 if (!$out) $out="http://127.0.0.1:8080/scripts/filme/out.mp4";
 $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
 echo $c;
 die();
} elseif ($flash == "chrome") {
  //$movie=str_replace("?",urlencode("?"),$movie);
  //$movie=str_replace("&","&amp;",$movie);
  $c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header("Location: $c");
} elseif (strpos($out,"mp4") !== false) {
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
<BODY>
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"playlist": [{
"sources": [{"file": "'.$out.'", "type": "mp4"}], 
'.$subtracks.'
}],
    captions: {
        color: "#FFFFFF",
        fontSize: 20,
        backgroundOpacity: 0
    },
"height": $(document).height(),
"width": $(document).width(),
"skin": '.$skin.',
"stretching":"'.$strech.'",
"androidhls": true,
"startparam": "start",
"autostart": true,
"fallback": false,
"wmode": "direct",
"stagevideo": true
});
</script>
</BODY>
</HTML>
';
} elseif ($tit == "RADIO") {
//$audio=$out;
//rtmp://fms113.mediadirect.ro/radio/_definst_/kissfm.stream
//$token="49a48349fe948e6f24efcdbdf2859bee2154958787bc2796";
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
<BODY>
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"playlist": [{
"image": "'.$img.'",
"sources": [{"file": "'.$audio.'", "type": "rtmp"}],
}],
"streamer": "rtmp://fms113.mediadirect.ro/radio/_definst_",
"provider": "rtmp",
"height": $(document).height(),
"width": $(document).width(),
"skin": '.$skin.',
"stretching":"exactfit",
"dock": "true",
"autostart": "true",
"controlbar.position": "over",
"controlbar.idlehide": "false",
"backcolor": "000000",
"frontcolor": "ffffff",
"lightcolor": "f7b01e",
"volume": "100",
});
</script>
</BODY>
</HTML>
';
} elseif (strpos($out,"m3u8") !== false) {
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
<BODY>
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"playlist": [{
"image": "'.$img.'",
"sources": [{"file": "'.$out.'", "type": "m3u8"}],
}],
"height": $(document).height(),
"width": $(document).width(),
"skin": '.$skin.',
"stretching":"'.$strech.'",
"androidhls": "true",
"startparam": "start",
"autostart": "true",
"fallback": "false",
"wmode": "direct",
"stagevideo": "true"
});
</script>
</BODY>
</HTML>
';
} else {
$app="live3";
if ($tv=="fms38.mediadirect.ro")
 $serv="fms11".mt_rand(2,3).".mediadirect.ro";
$rtmp="rtmpe://".$serv."/".$app."/_definst_?token=".$token;
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
<script type="text/javascript" src="../filme/jwplayer.js"></script>

</HEAD>
<BODY>
<div id="container"></div>
<script type="text/javascript">
jwplayer("container").setup({
"players": [{"type":"flash","src":"../filme/player.swf"}],
"file": "'.$str_name.'",
"token": "'.$token.'",
"height": $(document).height(),
"width": $(document).width(),
"skin": "../skin.zip",
"stretching":"'.$strech.'",
"dock": "true",
"autostart": "true",
"controlbar.position": "over",
"controlbar.idlehide": "false",
"backcolor": "000000",
"frontcolor": "ffffff",
"lightcolor": "f7b01e",
"streamer": "'.$rtmp.'",
"volume": "100",
});
</script>
</BODY>
</HTML>
';
}
?>
