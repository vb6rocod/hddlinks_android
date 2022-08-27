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

if (isset($_POST["tip"])) {
$tip=$_POST["tip"];
$pg_tit=unfix_t(urldecode($_POST["title"]));
$ep_tit=unfix_t(urldecode($_POST["ep_title"]));
$sez=$_POST["sez"];
$ep=$_POST["ep"];
$image=urldecode($_POST["image"]);
$requestLink=urldecode($_POST["link"]);
} else {
$tip=$_GET["tip"];
$pg_tit=unfix_t(urldecode($_GET["title"]));
$ep_tit=unfix_t(urldecode($_GET["ep_title"]));
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$image=urldecode($_GET["image"]);
$requestLink=urldecode($_GET["link"]);
}
if ($tip=="series") $pg_tit = $pg_tit." ".$sez."x".$ep." - ".$ep_tit;
$l="http://www.tvseries.net".$requestLink;
$l="https://tvseries.net".$requestLink;
//echo $l;
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  //die();
  if (strpos($requestLink,"/tvshows") !== false || strpos($requestLink,"/tvplay") !== false) {
  $t1=explode('checkurl.php',$html);
  $t2=explode('"',$t1[1]);
  $l1="http://www.tvseries.net/checkurl.php".$t2[0];
  $l1="https://tvseries.net/checkurl.php".$t2[0];
  $l1=str_replace("free=false","free=trus",$l1);
  //$l1=$l1."&free=true";
  //echo $l1;
  //$l1="http://www.tvseries.net/checkurl.php?id=1__29723_33&free=trus";
  //$l1="http://www.tvseries.net/checkurl_tv.php?id=998892627_&free=true";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $movie = curl_exec($ch);
  curl_close($ch);
  } else {
  $t1=explode('movieplay_size.php',$html);
  $t2=explode('"',$t1[1]);
  $l1="http://www.tvseries.net/movieplay_size.php".$t2[0];
  $l1="https://tvseries.net/movieplay_size.php".$t2[0];

  //die();
  //$l1="https://tvseries.net/movieplay_try.php".$t2[0];
//Cookie: PHPSESSID=6eedc0nvq816krtpdcql9jp220; _ga=GA1.2.320204891.1661287503; _gid=GA1.2.1828465577.1661287503; vodcmss_username=07499XE7TSKGdmIuurhSE4hR7fvhksb7aiaqGtaxJwRf5q3suA; vodcmss_uid=83beRfpfeTJlnXO%2Bn15SGjVBCZjA%2F4pXjeI%2Bx7NwO9NXGUs; vodcmss_type=7039hfa%2F4vWZ3bEkdPajjPdCgrl%2F4rOVPYM9A6lX; vodcmss_online=ae0eV7Oqbv74Anh8aVAYbpljj9PVqK6Ht8M0iPfmj4P5UhvU; vodcmss_auth=b95a9H%2FnA8kqiwIjXZHimmJnAI7ynwTaOh3qMji4uuG4bckGj%2BUnZmCTgNLHP2%2FJDIGRFifyt%2Bv%2FjQW6ew;
//Cookie: PHPSESSID=6eedc0nvq816krtpdcql9jp220; _ga=GA1.2.320204891.1661287503; _gid=GA1.2.1828465577.1661287503; vod
//Cookie: PHPSESSID=6eedc0nvq816krtpdcql9jp220; _ga=GA1.2.320204891.1661287503; _gid=GA1.2.1828465577.1661287503; vodcmss_username=07499XE7TSKGdmIuurhSE4hR7fvhksb7aiaqGtaxJwRf5q3suA; vodcmss_uid=83beRfpfeTJlnXO%2Bn15SGjVBCZjA%2F4pXjeI%2Bx7NwO9NXGUs; vodcmss_type=7039hfa%2F4vWZ3bEkdPajjPdCgrl%2F4rOVPYM9A6lX; vodcmss_online=ae0eV7Oqbv74Anh8aVAYbpljj9PVqK6Ht8M0iPfmj4P5UhvU; vodcmss_auth=b95a9H%2FnA8kqiwIjXZHimmJnAI7ynwTaOh3qMji4uuG4bckGj%2BUnZmCTgNLHP2%2FJDIGRFifyt%2Bv%2FjQW6ew; _gat=1
//Cookie: PHPSESSID=6eedc0nvq816krtpdcql9jp220; _ga=GA1.2.320204891.1661287503; _gid=GA1.2.1828465577.1661287503; vodcmss_username=07499XE7TSKGdmIuurhSE4hR7fvhksb7aiaqGtaxJwRf5q3suA; vodcmss_uid=83beRfpfeTJlnXO%2Bn15SGjVBCZjA%2F4pXjeI%2Bx7NwO9NXGUs; vodcmss_type=7039hfa%2F4vWZ3bEkdPajjPdCgrl%2F4rOVPYM9A6lX; vodcmss_online=ae0eV7Oqbv74Anh8aVAYbpljj9PVqK6Ht8M0iPfmj4P5UhvU; vodcmss_auth=b95a9H%2FnA8kqiwIjXZHimmJnAI7ynwTaOh3qMji4uuG4bckGj%2BUnZmCTgNLHP2%2FJDIGRFifyt%2Bv%2FjQW6ew; _gat=1

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $movie = curl_exec($ch);
  curl_close($ch);
  //echo $movie."\n";
  //die();
  $l1="https://tvseries.net/moviesource.php";
  $post="flag=1";
  $l1="https://tvseries.net/getcaption.php?movid=1103489&urlid=104210&lang=english&type=1";
  ///getcaption.php?movid=1103484&urlid=104200&lang=english&type=0
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: 6',
'Origin: https://tvseries.net',
'Connection: keep-alive',
'Referer: https://tvseries.net/movies/2022/Spin-Me-Round.html');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  // https://ng1.tvseries.net/1661330976/f0d86391a9cdc40d9a35eb8e0393040a/LzAxLzEzMi8xMzIuODM2LlRoZS5JbW1hY3VsYXRlLlJvb20uWkd0TllPc0oubXA0.mp4
  // https://ns2.tvseries.net/1661331419/c73a14159cf9644474c0bbeb32689ca0/LzAxLzEwMi8wMi4wNDQuVGhlLkRheS5BZnRlci5Ub21vcnJvdy43QVJlSThkVi5tcDQ=.mp4
  // https://ng1.tvseries.net/1661331390/cf7ecbc1002b51caa70c460da48007f4/LzAxLzEwMi8wMi4wNDQuVGhlLkRheS5BZnRlci5Ub21vcnJvdy43QVJlSThkVi5tcDQ=.mp4
  }
//http://ng1.tvseries.net:82/1536822447/f280d51278edfb37acbff2f804a41b77/LzAxLzEzNS8xMzUuMDA5LkRlc3RpbmF0aW9uLldlZGRpbmcuZmYyWkpnM1QubXA0.mp4
//http://ng1.tvseries.net:82/1536864873/c8f2e2c05dba3c7c2ff3f085c262c2af/LzAxLzEzNS8xMzUuMDA5LkRlc3RpbmF0aW9uLldlZGRpbmcuZmYyWkpnM1QubXA0.mp4
$movie=str_replace("tvseries.net:/","tvseries.net/",$movie);
$movie=str_replace("play2.vip","play.vip",$movie);
$movie=str_replace("ng1.tvseries.net","ns2.tvseries.net",$movie);
$t1=explode("?",$movie);
   $movie_file=substr(strrchr($t1[0], "/"), 1);
   if (preg_match("/mp4|flv/",$movie_file))
   $srt_name = substr($movie_file, 0, -3)."srt";
   else
   $srt_name= $movie_file.".srt";

  $movie=str_replace("https","http",$movie);
  if (strpos($movie,"http") === false) $movie="";
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
$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg_tit).";end";
echo $c;
die();
} elseif ($flash == "chrome") {
  $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg_tit).";end";
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
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": [{
"title": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
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
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"title": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
"abouttext": "'.preg_replace("/\n|\r/"," ",$pg_tit).'",
"autostart": true,
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
