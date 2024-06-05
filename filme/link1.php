<?php
error_reporting(0);
//set_time_limit(0);
include ("../common.php");
$host="";
function getSiteHost($siteLink) {
		// parse url and get different components
		$port="";
		$siteParts = parse_url($siteLink);
		if (isset($siteParts['port']))
		$port=$siteParts['port'];
		else
		$port="";
		if (!$port || $port==80)
          $port="";
        else
          $port=":".$port;
		// extract full host components and return host
		return $siteParts['scheme'].'://'.$siteParts['host'].$port;
}
function get_max_res($h,$l) {
  // $h -- > contents of "master.m3u8"
  // $l -- > "master.m3u8
  if (preg_match("/BANDWIDTH|RESOLUTION/",$h)) {
    // get "dir"
    if (preg_match("/\?/",$l)) {
     $t1=explode("?",$l);
     $l=$t1[0];
    }
    $base1=dirname($l);  // https://aaa.vvv/xxx/ffff
    //echo $base1;
    preg_match("/(https?:\/\/.+)\//",$base1,$m);
    $base2=$m[1];  // https://aaa.vvv
    $pl=array();
    //echo $base2;
    if (preg_match_all ("/^(?!#).+/m",$h,$m)) {
     $pl=$m[0];
     if (substr($pl[0], 0, -2) == "//")
       $base="https:";
     elseif ($pl[0][0] == "/") {
       $base=$base2;
       $base="https://".parse_url($base2)['host'];
     } elseif (preg_match("/http(s)?:/",$pl[0]))
       $base="";
     else
       $base=$base1."/"; // ???????????????????????
     if (count($pl) > 1) {
      if (preg_match_all("/\#EXT-X-STREAM-INF.*?(RESOLUTION|BANDWIDTH)\=(\d+)/i",$h,$r)) {
       $max_res=max($r[2]);
       $arr_max=array_keys($r[2], $max_res);
       $key_max=$arr_max[0];
       return $base.$pl[$key_max];
      } else {
        return $base.$pl[0];
      }
     } else {
      return $base.$pl[0];
     }
    } else {
      return $l;
    }
  } else {
   return $l;
  }
}
function unjuice($source) {
  $juice = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  $pat='@JuicyCodes.Run\(([^\)]+)@';
  if (preg_match($pat,$source,$m)) {
  $e=preg_replace('/\"\s*\+\s*\"/',"",$m[1]);
  $e=preg_replace('/[^A-Za-z0-9+\\/=]/',"",$e);
  $t = "";
  $n=$r=$i=$s=$o=$u=$a=$f=0;
  while ($f < strlen($e)) {
    $s = strpos($juice,$e[$f]);$f+=1;
    $o = strpos($juice,$e[$f]);$f+=1;
    $u = strpos($juice,$e[$f]);$f+=1;
    $a = strpos($juice,$e[$f]);$f+=1;
    $n = $s << 2 | $o >> 4; $r = (15 & $o) << 4 | $u >> 2; $i = (3 & $u) << 6 | $a;
    $t .= chr($n);
    if (64 != $u) $t .= chr($r);
    if (64 != $a) $t .= chr($i);
  }
  return $t;
  }
  return $source;
}
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$location = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
if ($_SERVER["SERVER_PORT"] != "80") {
    $location .= "127.0.0.1" . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["SCRIPT_NAME"];
} else {
    $location .= "127.0.0.1" . $_SERVER["SCRIPT_NAME"];
}
$hash_path = dirname($location);

$my_srt="";
$srt="";
$srt_name = "";
$movie="";
$movie_file="";
$pg="";
$referer="";
$link="";
if (file_exists("lava.m3u8")) unlink ("lava.m3u8");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="mp";
}
//////////////////////////////////////////////////////
$flash_original=$flash;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $os="win";
} else {
    $os="linux";
}
if ($flash=="flash" && $os=="linux")
  $flash="mp";
//////////////////////////////////////////////////////
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
if (isset($_POST["link"])) {
$filelink = urldecode($_POST["link"]);
$filelink=str_replace(" ","%20",$filelink);
//echo $filelink;
$pg = unfix_t(urldecode($_POST["title"]));
$pg=str_replace('"',"",$pg);
$pg=str_replace("'","",$pg);
} else {
$filelink = $_GET["file"];
//echo $filelink;
if (isset($_GET["title"])) $pg = unfix_t(urldecode($_GET["title"]));
$pg=str_replace('"',"",$pg);
$pg=str_replace("'","",$pg);
//$t1=explode(",",$filelink);
//if (sizeof($t1)>1) {
//$pg = urldecode($t1[1]);
//$filelink=urldecode($t1[0]);
//} else {
$filelink=urldecode($filelink);
$filelink_mpc="link1.php?file=".urlencode($filelink)."&title=".urlencode($pg)."&flash=mpc";
if (isset($_GET['flash'])) $flash="mpc";
//}
}
$filelink=str_replace("&amp;","&",$filelink);
//echo $filelink;
if (preg_match("/sub\.info11/",$filelink)) {
$t1=explode("sub.info=",$filelink);
$filelink =$t1[0]."sub.info=".urlencode($t1[1]);
}
// echo $filelink;

//$filelink="https://filmele-online.com/embed/9343.html";
//$filelink="http://www.wootly.ch/?v=6MY4EEE4";
$ua = $_SERVER['HTTP_USER_AGENT'];
if (strpos($filelink,"https://www.google.com/search") !== false) {
 //https://www.google.com/search?hl=en&source=hp&q=intitle%3AThe+Interpreters++mp4+inurl%3Agoogle.com%2Ffile%2Fd%2F
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/https\:\/\/drive\.google\.com\/file\/d\/.*?\/(view|preview|edit)/msi",$h,$m))
    $filelink=$m[0];
  else
    $filelink="";
}
if (preg_match("/imdb\.com/",$filelink)) {
   if (file_exists($base_sub."sub_extern.srt")) unlink($base_sub."sub_extern.srt");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
   if (file_exists($base_pass."tmdb.txt"))
    $api_key=file_get_contents($base_pass."tmdb.txt");
   else
    $api_key="";
  if (isset($_POST["link"])) {
   if (isset($_POST['tmdb']))
   $tmdb=$_POST['tmdb'];
   else
   $tmdb="";
   $imdb=$_POST['imdb'];
   $tip=$_POST['tip'];
   $year=$_POST['year'];
  } else {
   if (isset($_GET['tmdb']))
   $tmdb=$_GET['tmdb'];
   else
   $tmdb="";
   $imdb=$_GET['imdb'];
   $tip=$_GET['tip'];
   $year=$_GET['year'];
  }
  //echo "imdb=".$imdb."tmdb=".$tmdb."pg=".$pg."tip=".$tip;
  if ($imdb) {
   if ($imdb[0] !="t") $imdb="tt".$imdb;
  } else if ($tmdb) {
if ($tip=="movie")
  $l="https://api.themoviedb.org/3/movie/".$tmdb."?api_key=".$api_key."&append_to_response=external_ids";
   else
  $l="https://api.themoviedb.org/3/tv/".$tmdb."?api_key=".$api_key."&append_to_response=external_ids";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($html,1);
  $imdb=$x['external_ids']['imdb_id'];
  //echo $imdb;
  } else {
  if ($tip != "movie") {
    if (!$year)
     $find=$pg." serie";
    else
     $find=$pg." serie ".$year;
  } else {
    if (!$year)
     $find=$pg." movie";
    else
     $find=$pg." movie ".$year;
  }
  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  //echo $url;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/https:\/\/www.imdb.com\/title\/(tt\d+)/ms', $h, $match))
   $imdb=$match[1];
  }
  if ($imdb) {
  //echo $imdb;
  $l="https://www.imdb.com/title/".$imdb."/";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('{"@context',$h);
  $t2=explode('</script',$t1[1]);
  $x='{"@context'.$t2[0];
  $y=json_decode($x,1);
  //print_r ($y);
  if (isset($y['trailer']['embedUrl'])) {
  $l=$y['trailer']['embedUrl'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('{"props":',$h);
  $t2=explode('</script',$t1[1]);
  $x='{"props":'.$t2[0];
  $y=json_decode($x,1);
  //print_r ($y);
  if (isset($y['props']['pageProps']['videoPlaybackData']['video']['playbackURLs'][1]['url']))
    $link=$y['props']['pageProps']['videoPlaybackData']['video']['playbackURLs'][1]['url'];
  }
  }
  /*
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  echo $h;
  */
}
if (preg_match("/upmovies\./",$filelink)) {
  $last_good="https://".parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Origin: '.$last_good,
  'Connection: keep-alive',
  'Referer: '.$last_good);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/Base64\.decode\(\"([^\"]+)\"/",$h,$m)) {
   $out=base64_decode($m[1]);
   if (preg_match("/src\=\"([^\"]+)\"/",$out,$z))
     $filelink=$z[1];
   else
     $filelink="";
  } else {
    $filelink="";
  }
  //echo $filelink;
}
if (preg_match("/hdwatched.*?\./",$filelink)) {
//echo $filelink;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0',
  'Accept: image/avif,image/webp,*/*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Sec-Fetch-Dest: image',
  'Sec-Fetch-Mode: no-cors',
  'Sec-Fetch-Site: same-origin');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/source\s+src\=\"([^\"]+)\"/",$h,$m))
   $link=$m[1];
  if ($link && $flash == "flash2") {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0',
  'Accept: video/webm,video/ogg,video/*;q=0.9,application/ogg;q=0.7,audio/*;q=0.6,*/*;q=0.5',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Range: bytes=0-',
  'Connection: keep-alive',
  'Sec-Fetch-Dest: video',
  'Sec-Fetch-Mode: no-cors',
  'Sec-Fetch-Site: cross-site');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location\:\s*(.+)/i",$h,$m))
   $link=trim($m[1]);
   //$link .="|User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0");
   //$link .="&Accept=".urlencode("video/webm,video/ogg,video/*;q=0.9,application/ogg;q=0.7,audio/*;q=0.6,*/*;q=0.5");
   //$link .="&Range=".urlencode("bytes=0-");
   //$link .="&Referer=".urlencode("https://yandex.net/")."&Origin=".urlencode("https://yandex.net");
  }
}
// streamsrcs.2embed.cc
if (preg_match("/streamsrcs\.2embed\.cc/",$filelink)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://soap2dayz.xyz/',
  'Origin: https://soap2dayz.xyz',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL,$filelink);
  $h = curl_exec($ch);
  preg_match("/iframe src\=\"([^\"]+)\"/",$h,$m);
  $id=$m[1];
  preg_match("/script src\=\"\.\/([^\"]+)/",$h,$n);
  $l="https://streamsrcs.2embed.cc/".$n[1];
  curl_setopt($ch, CURLOPT_URL,$l);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //src',"
  preg_match("/\'src\'\,\"([^\"]+)/",$h,$s);
  $filelink=trim($s[1].$id);
  //echo $filelink;
}
if (preg_match("/vidsrc\.me/",$filelink)) {
  function deobfstr ($hash,$index) {
  $result = "";
  for ($i=0;$i<strlen($hash);$i +=2) {
   $j=substr($hash,$i,2);
   $result .=chr(hexdec($j) ^ ord($index[($i/2) % strlen($index)]));
  }
  return $result;
  }
  //echo $filelink;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc.me/',
  'Origin: https://vidsrc.me/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $l1="";
   if (preg_match("/player\_iframe\"\s+src\=\"([^\"]+)/",$h,$n)) {
   if (substr($n[1],0,4)=="http")
     $l1=$n[1];
   elseif (substr($n[1],0,2)=="//")
     $l1="https:".$n[1];
   }
  //echo $l1."\n";
  //$l1="https://vidsrc.stream/prorcp/Zjg3NTY2Nzg1NzJiMTdmODJhNzZlMmU5NDBiMjAyZjg6YjFScGIyRjFNVkJxU1ZWT1lXUTFWRXB5VkRWaldVUnRRbHA2VmtFek5HWjBTbUkzU2pNeU4zQk9lVTVrUmpSNVJtVldjaXR6ZG5aNE5qbE1TRU5VWlZKTmVHMTJWM2RRVnk5RmJqZG1kWGRuTldoRGFXcEVWa2hXZWtOWWNFbHJRbXczTTFrMFVXWTJTRVpKZEZaNE1GTkJhbmRwZW5CUFN6aFZibGd4TlVwV1YzRk1hbTgwU0ZONlRXaE5MelZaUkdKS1VFUmhNVFpIY0VZMWNtSnlkbWN3WkdreVkzaE5VbE01YzNkRFFYQTBVMnNyY1RRMGJtTkhXRmNyUVhoMFkyWXlhVUZXVVVFdmRsbFBSVFJVUnl0WmJqQnJNa1V4YW5CSmNWSjRjMEU0ZFhKQlVEZDVRMHB5TWtwMVFuaHRXVmxDZEdOTU5FdEpUM0psZVRneGNXcG5ObGhpTURObFlrZFNOM2RHTkc0MVExTkRWaTlHVlU1bmExaEpSSGx5YjNscWJDOHZURXM0ZFV0SVJVVnFlbGxQTm5oT05VOVdhWGhEZFVsaU4ySXlRamRUV0VoSmQybG5SbE5DVDBWTlNGSk1OVEphVFcwMGRuQmlhRmh1WVRKS1JYbEZNVVZPU0hKblFtMDJTMFV3YjFGc2RsQnpVekk1VkhKQlVIZHhla0l6VWpNeldrSkVTSEJLVjI5M1NWaFllWEZJZEhadVdrWnpiM0kxUlRacVZGTkJhRTAwV2pneVVFMHZVbGswV0RZcmNtdHNhazU1Tkc1cGIxWmlXVzQyUVc0MVVUa3ZTSHBSV0dkRGFrUlVlV2gyTVVvMGFuSTJZVFozVDBGTVFWY3hXSFpKVjFGS1NtZ3dSakJrUWtodVVuUTJiV1JOYWpKVlJqQm9UelIwVjJOVVR6VTVSMHNyV0hGR1FUQjNObk5zV2tKdmFGWXhlVWhtVURKWEwwTlhaVFpWV1ZsVFJXUjNjWFJyTVRSdWJ5dG1SWHBsWTBST09VOUxOSEpDV1VkM05WaFFObk5PU1d4QlpXTkVRM2N5ZUZCWldVeHBjeXRwVEVFd01VbzRlVk5vVmtOTFVqaG9aejA9";
  //echo $l1."\n";
  if ($l1) {
  //$l1=str_replace("/rcp/","/prorcp/",$l1);
  //echo $l1;
  //$t1=explode("/rcp/",$l1);
  //$hash=$t1[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('data-i="',$h);
  $t2=explode('"',$t1[1]);
  $index=$t2[0];
  $t1=explode('data-h="',$h);
  $t2=explode('"',$t1[1]);
  $hash=$t2[0];
  //echo trim($hash)."\n";
  $x=deobfstr ($hash,$index);
  //echo $x;
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, fixurl($x));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('data-i="',$h);
  $t2=explode('"',$t1[1]);
  $index=$t2[0];
  $t1=explode('data-h="',$h);
  $t2=explode('"',$t1[1]);
  $hash=$t2[0];

  //echo $x;
  //if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  //$srt=$s[1];
  $link="";
/////////////////////////////
  if (preg_match("/file\:\"\#\d([^\"]+)\"/",$h,$m)) {
  $x=$m[1];
  //echo $x."\n";
  $x = preg_replace("/[^A-Za-z0-9\+\/\=]/", "",$x);
  //https://vidsrc.stream/pjs/pjs_main.js?_=1704986710
  $v=array("//KjMkKzg=", "//Nz0hODY=", "//NjYlNiM=", "//MSYxNTg=" ,"//Kj0kOTI="); // from playerjs.js eval(decode(....
  $v=array("//Kiw0KS4oXykoKQ==","//MzMtKi40LzlbNg==","//Ol0mKjFAQDE9Jg==","//PSg9OjE5NzA1Lw==","//JT82NDk3Lls6NA==");
  //$v=array_reverse($v);
  //print_r ($v);
  for ($k=0;$k<5;$k++) {
   $x=str_replace($v[$k],"",$x);
  }
  $link=base64_decode($x);
  //echo $link;
  } else {
  if (preg_match("/data-h\=\"/",$h)) {
  $t1=explode('data-i="',$h);
  $t2=explode('"',$t1[1]);
  $index=$t2[0];
  $t1=explode('data-h="',$h);
  $t2=explode('"',$t1[1]);
  $hash=$t2[0];
   $link=fixurl(deobfstr ($hash,$index));
  }
  }
  if ($link && $flash <> "flash") {
      $link .="|Origin=".urlencode("https://vidsrc.stream");
      $link .="&Referer=".urlencode("https://vidsrc.stream/");
      
  }
  }
}
if (preg_match("/vidsrc\.to/",parse_url($filelink)['host'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://vidsrc.to',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $url=json_decode($h,1)['result']['url'];
  //echo $url;
  require_once("bunny2.php");
  $bunny=new bunny();
  $filelink = $bunny->decodeVrf($url);
  //echo $filelink;
}
if (preg_match("/stream\.2embed\.cc/",$filelink)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://soap2dayz.xyz/',
  'Origin: https://soap2dayz.xyz',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  $filelink="";
  if (preg_match("/sources\:\s*\[\{file\:\s*\"([^\"]+)\"/",$out,$m)) {
   $link=$m[1];
  }
}

if (strpos($filelink,"embed.smashystream.com") !== false) {
//echo $filelink;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://embed.smashystream.com',
  'Origin: https://embed.smashystream.com',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("\\","",$h);
  //echo $h;
  if (preg_match("/imw\.php|f\w+\.php/",$filelink)) {
  //echo $h;
  //$file=$r['file'];
  if (preg_match("/\[(1080|720|480|360)\]([^\,]+)\,/",$h,$l))
   $link=$l[2];
  else
   $link="";
  //print_r ($l);
  $sub=explode(",",$h);
  $s=preg_grep("/\[(english|romanian).*\](http[^\[]+\.vtt)/i",$sub);
  $srt="";

  foreach($s as $v) {
   if (preg_match("/\[romanian.*\](http.+)/i",$v,$ss)) {
    $srt=$ss[1];
    break;
   } elseif (preg_match("/\[english.*\](http.+)/i",$v,$ss)) $srt=$ss[1];
  }
  } elseif (preg_match("/2cc\.php/",$filelink)) {
  require_once("JavaScriptUnpacker.php");
  $host="https://streamwish.to";
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  //echo $out;
  //die();
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  if (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
  if ($link && $flash <> "flash")  {
   $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
  } elseif (preg_match("/eemovie\.php/",$filelink)) {
    if (preg_match("/file\:\s*\"([^\"]+)\"/",$h,$m))
     $link=$m[1];
  } elseif (preg_match("/void\.php/",$filelink)) {
    $t1=explode('iframe src="',$h);
    $t2=explode('"',$t1[1]);
    $filelink=$t2[0];
  }
}
if (preg_match("/9animetv\./",$filelink)) {
  parse_str(parse_url($filelink)['query'],$r);
  $id=$r['id'];
  //$filelink=str_replace("9animetv.live","aniwave.to",$filelink);
  //echo $filelink;
  $l="https://api.9animetv.live/player-json-api.php?id=".$id;
  //$l=str_replace("9animetv.live","aniwave.to",$l);
  //echo $l;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://api.9animetv.live',
  'Origin: https://api.9animetv.live',
  'Upgrade-Insecure-Requests: 1');
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($r['s'])) { // tv
   $sez=$r['s'];
   $ep=$r['e'];
   $l="";
   for ($k=0;$k<count($x['simple-api']);$k++) {
    if ($x['simple-api'][$k]['season']==round($sez) && $x['simple-api'][$k]['episode'] ==round($ep)) {
     $l= $x['simple-api'][$k]['iframe'];
     break;
    }
   }
  } else { // movie
    $l= $x['simple-api'][0]['iframe'];
  }
  if ($l) {
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<iframe',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $filelink=$t3[0];
  //echo $filelink;
  }
}
if (preg_match("/i\-moviehd\.com/",$filelink)) {
  $t1=explode("l=",$filelink);
  $link=urldecode($t1[1]);
  $host="https://".parse_url($link)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  if ($link && $flash <> "flash")
   $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
}
if (preg_match("/freedrivemovie\./",$filelink)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://freedrivemovie.lol/movies/ghosted/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  $x=$r['embed_url'];
  $x=parse_url($x)['query'];
  parse_str($x,$q);

  $link=$q['source'];
}
if (preg_match("/jeniusplay\.com/",$filelink)) {
   //echo $filelink;
 $t1=explode("&ref=",$filelink);
 $filelink=$t1[0];
 $r="https://".$t1[1]."/";
 if (preg_match("/video\/(\w+)/",$filelink,$m)) {
 $id=$m[1];
 $l="https://jeniusplay.com/player/index.php?data=".$id."&do=getVideo";
 //$l="https://jeniusplay.com/player/index.php?data=c64c545aff0d17ad713c907fdada37d1&do=getVideo";
 $post="hash=".$id."&r=".$r;
 $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
 'Accept: */*',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
 'X-Requested-With: XMLHttpRequest',
 'Content-Length: '.strlen($post),
 'Origin: https://jeniusplay.com',
 'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  //print_r ($r);
  $link=$r['videoSource'];
  if ($link && $flash <> "flash") {
 $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
 'Accept: */*',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Referer: https://jeniusplay.com',
 'Origin: https://jeniusplay.com',
 'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  file_put_contents("lava.m3u8",$h);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } elseif ($flash=="mp") {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  $link .="|Referer=".urlencode('https://jeniusplay.com')."&Origin=".urlencode("https://jeniusplay.com");
  } elseif ($flash=="mpc") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  $link .="|Referer=".urlencode('https://jeniusplay.com')."&Origin=".urlencode("https://jeniusplay.com");
  }

  }
 }
}
if (preg_match("/xemovies\.to/",$filelink)) {
 parse_str(parse_url($filelink)['query'],$r);
 //print_r ($r);
 $link=$r['file'];
 $srt=$r['srt'];
 $filelink="";
}
if (preg_match("/net\-film\.vercel\.app/",$filelink)) {
  //$filelink="https://net-film.vercel.app/api/episode?id=215859";
  //echo $filelink;
$head=json_decode(file_get_contents($base_cookie."netfilm.dat"),1);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  if (preg_match("/application\/json/",$h)) {
  $t1=explode('type="application/json">',$h);
  $t2=explode('</script>',$t1[1]);
  $h=$t2[0];
  }
  $r=json_decode($h,1);
  //print_r ($r);
  /*
  if ($r['status'] != "200"){
   $h = curl_exec($ch);
  if (preg_match("/application\/json/",$h)) {
  $t1=explode('type="application/json">',$h);
  $t2=explode('</script>',$t1[1]);
  $h=$t2[0];
  }
   $r=json_decode($h,1);
  }
  if ($r['status'] != "200"){
   $h = curl_exec($ch);
  if (preg_match("/application\/json/",$h)) {
  $t1=explode('type="application/json">',$h);
  $t2=explode('</script>',$t1[1]);
  $h=$t2[0];
  }
   $r=json_decode($h,1);
  }
  */
  curl_close($ch);
  //print_r ($r);
  if (isset($r['props']['pageProps']['watchInfo']['qualities'][0])) {
  $link=$r['props']['pageProps']['watchInfo']['qualities'][0]['url'];
  //$link=str_replace("akm-cdn", "aws-cdn",$link);
  //$link=str_replace("gg-cdn", "aws-cdn",$link);
  $host=parse_url($link)['host'];
  //$link=str_replace($host,"akm-cdn-play.loklok.tv",$link);
  foreach($r['props']['pageProps']['watchInfo']['subtitles'] as $video) {
   if (preg_match("/en/i",$video['lang'])) {
     $srt=$video['url'];
     if (preg_match("/\?url\=/",$srt)) {
     $t1=explode("?url=",$srt);
     $srt=$t1[1];
     }
   }
  }
  }
}
if (preg_match("/bflix\w*\.|sflix\w*\.|fmovies\w*\.to/",parse_url($filelink)['host'])) {
  require_once("bunny1.php");
  //echo $filelink; //bflixhd.to
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$filelink,
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $url=json_decode($h,1)['result']['url'];
  //echo $url."\n";
  $b=new bunny();
  $filelink=$b->decodeVrf($url);
  //echo $filelink;
  //die();

  if (preg_match("/\?sub\.info\=/",$filelink)) {
   $t1=explode("?sub.info=",$filelink);
   $filelink=$t1[0];
  $l1=urldecode($t1[1]);
  $t1=explode("&",$l1);
  $host1="https://bflix.to";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: '.$host1,
  'Connection: keep-alive',
  'Referer: '.$host1.'/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($t1[0]));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $s=json_decode($h,1);
  //print_r ($s);
  $srt="";
  for ($k=0;$k<count($s);$k++) {
   if (preg_match("/romanian/i",$s[$k]['label'])) {
    $srt=$s[$k]['file'];
    break;
   }
  }
  if (!$srt) {
  for ($k=0;$k<count($s);$k++) {
   if (preg_match("/english/i",$s[$k]['label'])) {
    $srt=$s[$k]['file'];
    break;
   }
  }
  }
  }
  // https://vidstream.pro/e/6EJV49K5QP9M
  //echo $filelink;
  //die();
}
if (preg_match("/moviebox\.com/",$filelink)) {
if (file_exists($base_pass."moviebox.txt")) {
  $h=trim(file_get_contents($base_pass."moviebox.txt"));
  $t1=explode("|",$h);
  $l=$t1[0];
  $appkey=$t1[1];
  $key=$t1[2];
  $iv=$t1[3];
  $appid=$t1[4];
}
function random_token($chars = 32) {
   $letters = '0123456789abcdef';
   return substr(str_shuffle($letters), 0, $chars);
}
$exp=time() + 60 * 60 * 12;
$encrypt_method = "DES-EDE3-CBC";
 $a=parse_url($filelink)['query'];
 parse_str($a,$b);
 $link=urldecode(urldecode($b['file']));
 $tip=$b['tip'];
 $id=$b['id'];
 $fid=$b['fid'];
 $sez=$b['sez'];
 $ep=$b['ep'];
 if ($tip=="movie") {
   $qq=array("childmode" => "0",
       "app_version" => "11.5",
       "appid" => $appid,
       "lang" => "en",
       "expired_date" => $exp,
       "platform" => "android",
       "channel" => "Website",
       "fid" => $fid,
       "uid" => "",
       "module" => "Movie_srt_list_v2",
       "mid" => $id
 );
 } else {
   $qq=array("childmode" => "0",
   "app_version" => "11.5",
   "appid" => $appid,
   "lang" => "en",
   "expired_date" => $exp,
   "platform" => "android",
   "channel" => "Website",
   "fid" => $fid,
   "uid" => "",
   "module" => "TV_srt_list_v2",
   "episode" => $ep,
   "tid" => $id,
   "season" => $sez
  );
 }
$dd=json_encode($qq);
$data = openssl_encrypt( $dd, $encrypt_method, $key, 0, $iv );
$vv=md5(md5("moviebox").$key.$data);

$p=array("app_key" => $appkey,
"verify" => $vv,
"encrypt_data" => $data);
$xx=base64_encode(json_encode($p));
$post="data=".$xx."&appid=27&platform=android&version=129&medium=Website&token".random_token()."=";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://movie.squeezebox.dev/',
'Platform: android',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://movie.squeezebox.dev',
'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $z=json_decode($h,1);
  //print_r ($z);
  for ($k=0;$k<count($z['data']['list']);$k++) {
    $lang=$z['data']['list'][$k]['language'];
    $s=$z['data']['list'][$k]['subtitles'][0]['file_path'];
    if (preg_match("/roman/i",$lang))
      $ss["romanian"]=$s;
    elseif (preg_match("/english/i",$lang))
      $ss['english']=$s;
  }
  $srt="";
  if (isset($ss["romanian"]))
    $srt=$ss["romanian"];
  elseif (isset($ss['english']))
    $srt=$ss['english'];
  $srt=str_replace(" ","%20",$srt); // ???????
}
if (preg_match("/afdah\./",$filelink)) {
    $arrChrs = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "+", "/");
    $reversegetFChars = array();
    $getFStr="";
    $getFCount = 0;
    for ($i = 0; $i < count($arrChrs); $i++) {
        $reversegetFChars[$arrChrs[$i]] = $i;
    }

    function ntos($e) {
        //echo "=".$e."=";
        //return iconv("UTF-8", "ISO-8859-1//TRANSLIT", chr($e));
        return chr($e);
    }
    function readReversegetF() {
        global $reversegetFChars;
        global $getFStr;
        global $getFCount;
        if (!$getFStr) return -1;
        while (true) {
            if ($getFCount >= strlen($getFStr)) return -1;
            $e = $getFStr[$getFCount];
            //echo "readReversegetF=".$e."\n";
            $getFCount++;
            if (isset($reversegetFChars[$e])) {
                return $reversegetFChars[$e];
            }
            if ($e == "A") return 0;
        }
        return -1;
    }

    function setgetFStr($e) {
     global $reversegetFChars;
     global $getFStr;
     global $getFCount;
        $getFStr = $e;
        $getFCount = 0;
    }



    function getF($e) {
        global $reversegetFChars;
        global $getFStr;
        global $getFCount;
        setgetFStr($e);
        $t = "";
        $n = array(4);
        $r = false;
        while (!$r && ($n[0] = readReversegetF()) != -1 && ($n[1] = readReversegetF()) != -1) {
            $n[2] = readReversegetF();
            $n[3] = readReversegetF();
            $t .= ntos($n[0] << 2 & 255 | $n[1] >> 4);
            if ($n[2] != -1) {
                $t .= ntos($n[1] << 4 & 255 | $n[2] >> 2);
                if ($n[3] != -1) {
                    $t .= ntos($n[2] << 6 & 255 | $n[3]);
                } else {
                    $r = true;
                }
            } else {
                $r = true;
            }
        }
        return $t;
    }
    function tor($txt) {
        $map = array();
        $tmp = "abcdefghijklmnopqrstuvwxyz";
        $buf = "";
        for ($j = 0; $j < strlen($tmp); $j++) {
            $x = $tmp[$j];
            $y = $tmp[($j + 13) % 26];
            $map[$x] = $y;
            $map[strtoupper($x)] = strtoupper($y);
        }
        for ($j = 0; $j < strlen($txt); $j++) {
            $c = $txt[$j];
            $buf .= ($c >= 'A' && $c <= 'Z' || $c >= 'a' && $c <= 'z' ? $map[$c] : $c);
        }
        return $buf;
    }
    //echo $filelink;
    // https://afdah.info/embed/3272066
//$filelink="https://www.afdah.info/embed/3272066";
  include("rec.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
  //$ua     =   $_SERVER['HTTP_USER_AGENT'];
  //echo $filelink;
  $host=parse_url($filelink)['host'];
  //$filelink=str_replace($host,"www.afdah.info",$filelink);
  //echo $filelink;
  $key="6LeLo6IZAAAAAD1sHLlRReThaDfdZvxZ07nS0olp";
  $key="6LeLo6IZAAAAAD1sHLlRReThaDfdZvxZ07nS0olp";
  $key="6LeLo6IZAAAAAD1sHLlRReThaDfdZvxZ07nS0olp";
  $co="aHR0cHM6Ly9hZmRhaC5pbmZvOjQ0Mw..";
  $co="aHR0cHM6Ly93d3cuYWZkYWguaW5mbzo0NDM.";
  $co="aHR0cHM6Ly9hZmRhaC52aWRlbzo0NDM.";
  $sa="play1";
  $loc="https://afdah.video";
  $token=rec($key,$co,$sa,$loc);
  $post="g-recaptcha-response=".$token;
  //echo $post;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post),
  'Origin: https://afdah.video',
  'Connection: keep-alive',
  'Cookie: rotator=2',
  'Referer: '.$filelink,
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  //$h = curl_exec($ch);
  $h = curl_exec($ch);
  curl_close($ch);
  //$h=str_replace('src="/','src="https://afdah.info/',$h);
  //echo $h;

  $t1=explode('var kodi =',$h);
  $t2=explode(';',$t1[1]);
  $c=str_replace('unescape',"",$t2[0]);
  $c=str_replace("(e)","(\$e)",$c);
  $c="\$kodi=".$c.";";

  $e="";
  $t1=explode('salt("',$h);
  $t2=explode('");',$t1[1]);
  $e=$t2[0];
  if ($e) {
   eval ($c);
   if (preg_match("/hlsvideo\s*\=\s*\"([^\"]+)\"/",$kodi,$r))
    $link=$r[1];
   if (preg_match("/\/subtitles\/\d+\.srt/",$kodi,$s))
    $srt="https://afdah.video".$s[0];
  }
  $host="afdah.video";
  if ($host && $flash <> "flash") {
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://afdah.video");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //preg_match_all("/^#/",$h,$b);
  //print_r ($b);
  if (preg_match_all("/http.+/",$h,$m))
    $link=trim($m[0][count($m[0])-1]);
  $link=$link."|Referer=".urlencode("https://".$host);
  }
}
if (preg_match("/api\.consumet\.org/",$filelink)) {
//echo urldecode($filelink);
//$filelink="https://api.consumet.org/movies/flixhq/watch?episodeId=39533&mediaId=".urlencode("/watch-tv/watch-star-trek-discovery-39533.4848658");
//$filelink="https://api.consumet.org/movies/flixhq/watch?episodeId=1219894&mediaId=tv%2Fwatch-star-trek-discovery-39533";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $y=json_decode($h,1);
  //print_r ($y);
  if (isset($y['sources'][0]['url'])) {
    $link=$y['sources'][0]['url'];
    $ref=$y['headers']['Referer'];
    $srt="";
    for ($k=0;$k<count($y['subtitles']);$k++) {
     if (preg_match("/roman/i",$y['subtitles'][$k]['lang'])) {
       $srt=$y['subtitles'][$k]['url'];
       break;
     }
    }
    if (!$srt) {
    for ($k=0;$k<count($y['subtitles']);$k++) {
     if (preg_match("/english/i",$y['subtitles'][$k]['lang'])) {
       $srt=$y['subtitles'][$k]['url'];
       break;
     }
    }
    }
  }
}
if (preg_match("/flixhq\.to/",$filelink)) {
   //$t1=explode("id=",$filelink);
   //$id=$t1[1];
   //$l="https://api.consumet.org/movies/flixhq/watch?episodeId=".$id."&mediaId=";
   //echo $filelink;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://flixhq.to/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $y=json_decode($h,1);
  //print_r ($y);
  $filelink=$y['link'];
  //echo $filelink;
  //$link=$y['sources'][0]['url'];

  //if (isset($y['headers']['Referer']))
}
if (preg_match("/emovies\./",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Origin: https://emovies.si',
  'Connection: keep-alive',
  'Referer: https://emovies.si/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $y=json_decode($h,1);
  if ($y['value'][0] == "/")
   $l="https:".$y['value'];
  else
   $l=$y['value'];
  //echo $l;
  //https://embed.vodstream.xyz?
  if (preg_match("/vidmoly\./",$l)) {
   $filelink=$l;
  } else {
  //$l="https://embed.vodstream.xyz/?k=4ed135d59b418a1c175d9cd2cd8a70c5&li=147115&tham=1707129115&lt=os&st=128fa457b4d3b5eb35b0cefb26ee3649&qlt=720p&spq=p&prv=&key=d3ebb6addd07e05bc063230c9d6d63cd&ua=de1bb6a25fc8916d6c022705e7b454c4cde7d71777a4629cd79bde6936854bfd33cca69ccb80509c07036f8a7ca66158d6f31dd9fbe271f73e2fa63f59f9141a86dee16b095c6f30c6c4aab03ab6beb1&h=1707129115";
  //echo "\n".$l."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("\\","",$h);
  //echo $h."\n";
  if (preg_match("/sources\:\s*\[\{\"file\"\:\"([^\"]+)\"/",$h,$m))
   $link=$m[1];
  }

}
if (preg_match("/tvseries\./",$filelink)) {
  $t1=explode("&s=",$filelink);
  $filelink=$t1[0];
  $serv=$t1[1];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/getcaption\.php([^\']+)/",$h,$s))
   $srt="https://tvseries.net/".$s[0];
   //echo $srt;
  if (preg_match("/movieplay\_size\.php([^\"]+)/",$h,$m))
   $l1="https://tvseries.net/".$m[0];
  else if (preg_match("/checkurl\.php([^\"]+)/",$h,$m))
   $l1="https://tvseries.net/".$m[0];
  $l1=str_replace("free=false","free=trus",$l1);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $link = curl_exec($ch);
  curl_close($ch);
  if ($serv=="1")
   $link=str_replace("ng1.tvseries.net","ns2.tvseries.net",$link);
  $link=str_replace("tvseries.net:/","tvseries.net/",$link);
  $link=str_replace("https","http",$link); // ??????
}
if (preg_match("/moviehab\./",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0";
  $host=parse_url($filelink)['host'];
  $head=array('Accept: text/html, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://moviehab.net/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1)["data"]["link"];
  if (preg_match("/play\.moviehab\./",$r)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$r);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  if (preg_match("/sub\.php/",$h)) {
    $t11=explode("sub.php",$h);
    $t22=explode('"',$t11[1]);
    $srt="https://play.moviehab.net/sub.php".$t22[0];
  }
  $l="https://play.moviehab.net/".$t2[0];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location\:\s*(.+)/i",$h,$m))
    $link=trim($m[1]);
  if ($link && $flash <> "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  }
  } else {
   $filelink=$r;
  }
}
if (preg_match("/player\.licenses4\.me/",$filelink)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: null',
  'Referer: https://fsro.io');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match_all("/^(?!\s*\/.*$)(\s+file\:\s*\"([^\"]+).*)/m",$h,$m))
   $link=$m[2][0];
  if (preg_match("/http.+\.(srt|vtt)/",$h,$s))
   $srt=$s[0];
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://player.licenses4.me")."&Origin=".urlencode("https://player.licenses4.me");
}
if (preg_match("/123stream\.fun|2embedplayer\.net|amazingstream\.net/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0";
  $post="button-click=ZEhKMVpTLVF0LVBTLVF0Ti0wWTJMUy1Rei1QLTAtUHRMLTAtVjItUHpBeS1QelF4TnpBei1Qai1WLTU=";
  //$filelink="https://123streaming.rocks/playvideo.php?video_id=TWIyd0IvdERsa2dKZTQxWjdBPT0=&server_id=35&token=TTcyeUNlRmR5MUJES3NJSHJRSnlZVTQ3NjMvbGgxb3hhajRTeDcwdEZ5cWIvSmhuWEU4RlpzdzJSVFVyMmsxaGpnbWVCTXZkMlN1bFdpd0p6blUzOUNuMHdUcHZKOXRpaUxDbUFjZDJUOHBJZXJFTEJKSWFBY3h5UnNnWGd3b001T2FyQ0ExV2VwSmMvdXNqMEJtUm9zblNoTTRNcnV6cW1YcDdaWWIvTTgwVjBHTFN4TGNXRlhYT0c1NFprako2WU1lQXhpVEdkMG5zcGdieGlqeFNmT3FCa0VmOGt4ejBFVi9FamNKSy9wTi8yU0VFR0xZOXNmTEhLM1ZPTzdGSDdDUElCMEw5cTIzVlBZM09FSzk3MzNWRTRqb3BxSWpydE1tN1R2alNQMUNnbVcxQmM4VkoxdE5hcTRtNTJaTmIwQ3c2dWVNSFNNWEdNV3BNVC9MQ0hsQ3pOTUhzQ3ZTM0FHK1Y4c0w2QVNqZmxoS3dSQmJ4MittZFlWeXh0ZjhpUndDRTkxcjZWdGNLQUJHalUvUzJ4VzMrU2RMVnRLTW96MFowL1pwSFM2ZFd1RWJhY0Y0MEg1cGhpdG04Y0plNFhIcDFxazd3Ymd2WktUUm5Wemw4TWJBbGNEdm5KSEF5N29Tb3ZGbDEzRHlBcEtKYmpDaXg=&init=1";
  //echo $filelink;
  $filelink=str_replace("123stream.fun","123streaming.rocks",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://amazingstream.net");
  curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/frameborder\=\"0\" src\=\"([^\"]+)/",$h,$m))
   $filelink=$m[1];
  //echo $filelink;
}
if (preg_match("/hdmoviebox\./",$filelink)) {
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0";
 $filelink=str_replace("/series/","/watch/",$filelink);
 //echo $filelink;
 //die();
  //https://hdmoviebox.net/watch/the-ritual-killer
  $host="https://".parse_url($filelink)['host'];
  //$host="https://hdmoviebox.net";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,$host);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/data\-whatwehave\=\"(\w+)\"\s+data\-lang\=\"(\w+)\"/",$h,$m)) {
  $l="https://hdmoviebox.org/ajax/service";
  $l="https://hdmoviebox.net/ajax/service";
  $l=$host."/ajax/service";
  $post="e_id=".$m[1]."&v_lang=".$m[2]."&type=get_whatwehave";
  //e_id=13059&v_lang=en&type=get_whatwehave
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: '.$host,
  'Connection: keep-alive',
  'Referer: '.$host);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $post;
  $y=json_decode($h,1);
  //print_r ($y);
  $l=$y['api_iframe'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,$host);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('iframe src="',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  if ($l[0]=="/") $l="https:".$l;
  $host="https://".parse_url($l)['host'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,$host);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match("/FirePlayer\(vhash/",$h)) {
  $t1=explode('FirePlayer(vhash,',$h);
  $t2=explode(', false);',$t1[1]);
  $x=json_decode(trim($t2[0]),1);
  //print_r ($x);
  //echo base64_decode("ZGlzazI=");
  // https://ivshare.xyz/cdn/hls/df8e4a74ef6d77e45dc35bed011946f6/master.txt?s=3&d=ZGlzazI=
  $s=$x['videoServer'];
  $d=$x['videoDisk'];
  if (isset($x['tracks'][0]['file']))
   $srt=$host.$x['tracks'][0]['file'];
  $link=$host.$x['videoUrl']."?s=".$s."&d=".base64_encode($d);
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode($host);
  } elseif (preg_match("/sources\:\s*\[\{file\:\"([^\"]+)\"/",$h,$m)) {
   $link=$m[1];
   if (preg_match("/file\:\s*\"(\/srt[^\"]+)/",$h,$m))
    $srt=$host.$m[1];
   if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
 }
  //echo $link;
}
if (preg_match("/yify\.|spacemov\.site|ytsmovie\.tv/",$filelink)) {
  $t1=explode("?",$filelink);
  $post=$t1[1];
  //echo $post;
  //require_once("JavaScriptUnpacker.php");
  //$jsu = new JavaScriptUnpacker();
  $host=parse_url($filelink)['host'];
$cookie=$base_cookie."yify.dat";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua=$_SERVER['HTTP_USER_AGENT'];
  $l="https://".$host."/wp-admin/admin-ajax.php";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://'.$host.'/movies/mind-games-2021/',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: https://'.$host,
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $z=$x['embed_url'];
  $t1=explode('iframe src="',$z);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  //echo $l;
  // https://hlspanel.xyz/video/e74843b99da8b29775c6aa9080436844
  preg_match("/video\/(\w+)/",$l,$m);
  $hash=$m[1];
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://yify.plus");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $h .= $jsu->Unpack($h);
  echo $h;
  */
  $l="https://hlspanel.xyz/player/index.php?data=".$hash."&do=getVideo";
  $post="hash=".$hash;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://hlspanel.xyz',
'Alt-Used: hlspanel.xyz',
'Connection: keep-alive',
'Referer: https://hlspanel.xyz/video/'.$hash);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $y=json_decode($html,1);
  //print_r ($y);
  //die();
  $link=$y['videoSource'];
  $host="hlspanel.xyz";
  /*
  $t1=explode("FirePlayer(vhash,",$h);
  $t2=explode(", false);",$t1[1]);
  $u=json_decode($t2[0],1);
  //print_r ($u);
  $host=parse_url($l)['host'];
  $videoServer=$u['videoServer'];
  $videoUrl=$u['videoUrl'];
  $videoDisk=$u['videoDisk'];
  $link="https://".$host.$videoUrl."?s=".$videoServer."&d=".base64_encode($videoDisk);
  */
  if ($host && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://".$host);
  /*
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://".$host);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  echo $html;
  */
}
if (preg_match("/closeload\./",$filelink)) {
  $host="https://".parse_url($filelink)['host'];
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: '.$host,
  'Connection: keep-alive',
  'Referer: '.$host);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $filelink);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     //curl_setopt($ch, CURLOPT_POST,1);
     //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
     curl_setopt($ch, CURLOPT_TIMEOUT, 25);
     $h = curl_exec($ch);
     curl_close($ch);
     if (preg_match("/track\s+src\=\"([^\"]+)\"/",$h,$m))
      $srt=$host.$m[1];
     //echo $h;
     // track src="
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $t1=explode('src:atob(',$out);
  $t2=explode(')',$t1[1]);
  $t1=explode($t2[0].'="',$out);
  $t2=explode('"',$t1[1]);
  $link=base64_decode($t2[0]);
  if (preg_match("/http/",$link) && $flash <> "flash")
   $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  //echo $out;
}
if (preg_match("/coronamovies\./",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $t1=explode("?file=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $link=$t2[0];
  $srt=$t2[1];
  //echo $srt;
  $filelink="";
}
if (preg_match("/m4umv\.com/",$filelink)) {
  $link=$filelink;
  $filelink="";
}
if (preg_match("/kumfimovie\./",$filelink)) {
//echo $filelink;
  $link=$filelink;
  $filelink="";
}
if (preg_match("/sockshare|wat32/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 45);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $t1=explode('Base64.decode("',$html);
  $t2=explode('"',$t1[1]);
  $a=base64_decode($t2[0]);
  //echo $a;
  if (preg_match("/\<iframe/",$a)) {
   $t1=explode('src="',$a);
   $t2=explode('"',$t1[1]);
   $filelink=$t2[0];
  } else {
   $t1=explode('href="',$a);
   $t2=explode('"',$t1[1]);
   $filelink=$t2[0];
  }
  $filelink=str_replace("///","//",$filelink);
  //echo $filelink;
}
if (preg_match("/trailers\./",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $t1=explode("?file=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $link=$t2[0];

  $srt=$t2[1];
  $filelink="";
  $host=parse_url($link)['host'];
  if ($flash <> "flash") {
   $link=$link."|Origin=".urlencode("https://trailers.to")."&Referer=".urlencode("https://trailers.to")."&Alt-Used=".urlencode($host);
   $link .="&User-Agent=".urlencode($ua);
   }
}
if (preg_match("/voidboost\.net/",$filelink)) {
  $v["bk4"]="$$$####!!!!!!!";
  $v["bk3"]="^^^^^^##@";
  $v["bk2"]="@!^^!@#@@$$$$$";
  $v["bk1"]="^^#@@!!@#!$";
  $v["bk0"]="@#!@@@##$$@@";
  $separator="//_//";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://dbgo.fun");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\[English\]((.*?)\.(srt|vtt))/i",$h,$m))
    $srt=$m[1];
  if (preg_match("/[\'\"]file[\'\"]:\s*[\'\"](\#2[^\'\"]+)/",$h,$m)) {
  $out=$m[1];
  $out=substr($out,2);
  for($i = 4; $i > -1; $i--) {
   if(isset($v["bk".$i])) {
        if($v["bk".$i] != "") {
            $out = str_replace($separator.base64_encode($v["bk".$i]),"",$out);
        }
   }
  }
  $x=base64_decode($out);
  //echo $x."\n";
  if (preg_match_all("/\[([240|360|480|720|1080]+)p\]([^\s]+)/i",$x,$s)) {
    if (preg_match("/poster\=0/",$filelink))
    $link=$s[2][count($s[2])-1];
    else
    $link=$s[2][count($s[2])-2];
  }
  }
 $filelink="";
}
if (preg_match("/cdn\-\d+\.fembed\.stream/",$filelink)) {
  $link=$filelink;
  $filelink="";
  if ($flash <> "flash")
   $link=$link."|Origin=".urlencode("https://infinitum.stream")."&Referer=".urlencode("https://infinitum.stream");
}
if (preg_match("/onion\w+\.\w+/",$filelink)) {
  $host=parse_url($filelink)['host'];
  if (preg_match("/onion\w+\.\w+\/(v|go|e)\//",$filelink)) {
   // onionbox\.org|onionplay\.cloud|onionflix\.cloud
   // https://onionbox.org/v/rjRuyXc6mUO9fxW/
   //echo $filelink;
   //https://flixon.lol/tt22687790
   // https://onionbox.org/v/a86qzi1t0OU8QbC/
   // https://onionbox.org/v/a86qzi1t0OU8QbC/
   //https://onionflux.com/v/ThfuhQ0L9VvgkMR/
   //https://onionflux.com/v/EUbe71Xi8nuH2Uc/
  //https://flixon.lol/
  //https://onionplay.se/
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://flixon.click/');
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  //$h=str_replace("\/","/",$h);
  $h1= unjuice($h);
  //echo $h1;
  $out = $jsu->Unpack($h1);
  $t1=explode('file":"',$out);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  } else {
  $link=$filelink;
  $filelink="";
  }
  if ($flash <> "flash")
   $link=$link."|Origin=".urlencode("https://".$host)."&Referer=".urlencode("https://".$host);
///////////////////
  /*
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  echo $h; // #EXT-X-MAP:URI="init.mp4" bad...........
  */
  ///////////////////////////////////////
}
if (preg_match("/api\.flixnet\.stream/",$filelink)) {
  // https://api.flixnet.stream/LHw2xZcAis6E?imdb_id=tt6710474
  require_once("JavaScriptUnpacker.php");
  //echo $filelink;
  $host=parse_url($filelink)['host'];
  //$jsu = new JavaScriptUnpacker();
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://onionplay.stream/',
  'Connection: keep-alive');
  $host="onionplay.stream";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  $h = htmlspecialchars_decode($h);
  //echo $h;
  $t1=explode('id="files" value="',$h);
  $t2=explode('">',$t1[1]);
  $r=json_decode($t2[0],1);
  $k=key($r);
  //print_r ($r[$k]);
  //$t1=explode('id="files_link" value="',$h);
  //$t2=explode('">',$t1[1]);
  //$r=json_decode($t2[0],1);
  //print_r ($r);
  // [240p]//cloud.cdnland.in/db1f787c507e914b11a24cdcf8f23e61:2022053013/movies/44cd12df907fc4a6beffee07180ac9cf7d582307/240.mp4:hls:manifest.m3u8,
  preg_match_all("/\[(\d+)p\](\/\/[\w\.\/\:\-]+\.m3u8)/",$r[$k],$s);
  $y=array_combine($s[1],$s[2]);
  krsort($y);
  $k=key($y);
  $link="https:".$y[$k];
  if ($flash <> "flash" && $link)
   $link=$link."|Origin=".urlencode("https://".$host)."&Referer=".urlencode("https://".$host);

}
if (preg_match("/fapdot\.(net|org)|dotnet\.stream/",$filelink)) {
  require_once("JavaScriptUnpacker.php");
  //echo $filelink;
  // https://fapdot.net/v/SJpAucggjv4JUo8/
  // https://fapdot.net/v/SJpAucggjv4JUo8/
  //$filelink="https://fapdot.net/v/TnImYmnMQicemQq/";
  //$filelink="https://fapdot.net/v/2K9drYIP5DNX5Fj/";
  $host=parse_url($filelink)['host'];
  $jsu = new JavaScriptUnpacker();
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://flixnet.stream/',
  'Connection: keep-alive');
  //'Referer: https://fapdot.org',
  //'Origin: https://fapdot.org');
  $ch = curl_init($filelink);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match("/JuicyCodes\.Run/i",$h)) {
  $h1= unjuice($h);
  //echo $h1;
  $out = $jsu->Unpack($h1);
  //echo $out;
  $t1=explode('file":"',$out);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  } else if (preg_match("/var\s+\w+\s*\=\s*\[(.*?)\]/",$h,$m)) {
  $e="\$c=array(".$m[1].");";
  eval ($e);
  //print_r ($c);
  preg_match("/String\.fromCharCode\(parseInt\(value\)\s*\-\s*(\d+)/",$h,$p);
  $out="";
  for ($k=0;$k<count($c);$k++) {
    $out .=chr($c[$k]-$p[1]);
  }
  //echo $out;
  // String.fromCharCode(parseInt(value) - 77785034
  preg_match("/file\:\s*[\'|\"](.*?)[\'|\"]/",$out,$n);
  $link=$n[1];
  }

  if ($flash <> "flash" && $link)
   $link=$link."|Origin=".urlencode("https://".$host)."&Referer=".urlencode("https://".$host);
}
if (preg_match("/lightdl\.xyz/",$filelink)) {
//echo $filelink;
  $t1=explode("file=",$filelink);
  $link=$t1[1];
  $link=str_replace(" ","%20",$link);
  $filelink=$t1[0];
}
if (preg_match("/anilist1\.ir|animdl\.cf/",$filelink)) {
  $link=$filelink;
}
if (preg_match("/vidlink\.org/",$filelink)) {
  //echo $filelink;
  // https://vidlink.org/embed/59eee43ef893828c34ca9f06
  if (preg_match("/(embed|post)\/(\w+)/",$filelink,$m))
   $id=$m[2];
  else
   $id="";
  $l="https://vidlink.org/embed/info?postID=".$id;
  //echo $l;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://vidlink.org");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['embed_urls'])) {
   if ($x['embed_urls'])
    $filelink=$x['embed_urls'];
  elseif (isset($x['tr_h'])) {
   $link="https://tr.vidlink.org/statics/pl/".$x['tr_h']."/pl.jpg";
   if ($flash <> "flash")
    $link=$link."|Referer=".urlencode("https://tr.vidlink.org");
   $filelink="";
  }
  }
  $srt="";
  for ($k=0;$k<count($x['subs']);$k++) {
    if (preg_match("/Romanian/i",$x['subs'][$k]['label'])) {
      $srt=$x['subs'][$k]['file'];
      break;
    }
  }
  if (!$srt) {
  for ($k=0;$k<count($x['subs']);$k++) {
    if (preg_match("/English/i",$x['subs'][$k]['label'])) {
      $srt=$x['subs'][$k]['file'];
      break;
    }
  }
  }
  //echo $filelink."\n".$srt;
}
if (preg_match("/apimdb\.net/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  //echo $filelink;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://apimdb.net");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;

$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://apimdb.net',
'Upgrade-Insecure-Requests: 1');
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>$head
  )
);
//echo $filelink;
$context = stream_context_create($opts);
//$h=@file_get_contents($filelink,false,$context);
  //echo $h;
  // https://apimdb.net/ajax/get_tv_link/?id=R3Bw_&imdb=tt0118480&s=1&e=4
  if (preg_match("/\<iframe/",$h)) {
    $t1=explode("<iframe",$h);
    $t2=explode('src="',$t1[1]);
    $t3=explode('"',$t2[1]);
    $filelink=$t3[0];
    //echo $filelink;
  } elseif (preg_match("/div id\=\'picasa/",$h)) {
    $t1=explode("div id='picasa",$h);
    $h=$t1[1];
    //$h=str_replace("\\","",$h);
    //echo $h;
    require_once("JavaScriptUnpacker.php");
    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h);
    $out = $jsu->Unpack($out);
    //echo $out;
    if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h.$out, $s)) // not sure
    $srt=$s[1];
    $t1=explode("file:'",$h.$out);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
    if ($link[0] == "/") $link= "https://apimdb.net".$link;

  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_REFERER,"https://apimdb.net");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://apimdb.net");
    $filelink="https://apimdb_vip.net/tv";
  } elseif (preg_match("/file22\:\'/",$h)) {
    $t1=explode("file:'",$h);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
  }
}
if (preg_match("/cdn\.jwplayer\.com/",$filelink)) {  // from https://www.lunchflix.com
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  $link=$x['playlist'][0]['sources'][0]['file'];
  $srt=$x['playlist'][0]['tracks'][0]['file'];
}
if (preg_match("/vikv\.net/",$filelink)) {
 $x=parse_url($filelink);
 $q=$x['query'];
 parse_str($q,$r);
 $s=$r['sub'];

 if ($s) $srt="https://sub1.hdv.fun/vtt1/".$s.".vtt";
 //print_r ($r);
 /*
let u=s("hdv_user");var d=document.getElementById("player");let c;
c="https://hls.hdv.fun/m3u8/"+e+".m3u8?u="+btoa(o(btoa(o(Math.random().toString(36).slice(-10)+u)))
https://sub1.hdv.fun/js/bundle37.js
*/
$id=$r['id'];
$user=$r['user'];
$post='{"hls_name":"'.$id.'","hdv_user":"'.$user.'"}';
$head=array('Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/json',
'Origin: https://hls.hdv.fun',
'Content-Length: '.strlen($post),
'Connection: keep-alive');
$l="https://hls.hdv.fun/captcha";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  
  $a1="k05d93aaiu";
  $a3=$r['user'];
 //$a1="ghl";
 //$a3=time()*1000;
 $a4=base64_encode(strrev($a1.$a3));
 $a5=base64_encode(strrev($a4));
 $link="https://hls.hdv.fun/m3u8/".$r['id'].".m3u8?u=".$a5;
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $link);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_USERAGENT, $ua);
 curl_setopt($ch, CURLOPT_REFERER,"https://hls.hdv.fun");
 curl_setopt($ch, CURLOPT_ENCODING, "");
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
 curl_setopt($ch, CURLOPT_TIMEOUT, 25);
 //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
 $h = curl_exec($ch);
 curl_close($ch);
 //$t1=explode("\n",$h);
 $out="";
 /*
 for($k=0;$k<count($t1);$k++) {
  if ($t1[$k][0]=="#")
   $out .=$t1[$k]."\n";
  elseif ($t1[$k][0] == "h") {
   $t2=explode("--w--",$t1[$k]);
   $i=substr($t2[4],0,-4);
   $out .="https://lh3.googleusercontent.com/proxy/".$x['ggc'][$i]."=s0"."\n";
  }
 }
  */
  $out=preg_replace_callback(
    "/http.*?(\d+)\.png/",
    function ($m) {
      global $x;
      return "https://lh3.googleusercontent.com/proxy/".$x['ggc'][$m[1]]."=s0";
    },
    $h
  );

 file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
}
if (preg_match("/playerhost\.net/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location:\s*(.+)/i",$h,$m))
   $filelink=trim($m[1]);
}
/////////////////////////////////////////////////////////////////////////////////////
// from movieforfree.co
if (preg_match("/newslink\.club|ezylink\.co/",$filelink)) {
  // https://newslink.club/flaG
  // https://ezylink.co/cMLR
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://movieforfree.co");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('var json =',$h);
  $t2=explode(';',$t1[1]);
  $r=json_decode(trim($t2[0]),1);
  //print_r ($r);
  if (isset($r['tracks'][0]['file']))
   $srt=$r['tracks'][0]['file'];
  if (isset($r['videoData']['videoSources'][0]['file'])) {
    $link= $r['videoData']['videoSources'][0]['file'];
    if ($link && $flash <> "flash") {
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://plyr.xyz");
    //curl_setopt($ch, CURLOPT_REFERER,"https://movieforfree.co");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close ($ch);
    //echo $h;
    $link=get_max_res($h,$link);
    //echo $link;
    $out="#EXTM3U"."\r\n";
    $out .="#EXT-X-VERSION:3"."\r\n";
    $out .="#EXT-X-STREAM-INF:BANDWIDTH=5000000,RESOLUTION=1920x1080"."\r\n".$link;
    file_put_contents("lava.m3u8",$out);
    $link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
     $link=$link."|Origin=".urlencode("https://plyr.xyz")."&Referer=".urlencode("https://plyr.xyz");
     $link=$link."&User-Agent=".urlencode($ua);
    }
  }
}
if (preg_match("/playdrive\.xyz|playdrive\.plyr\.xyz/",$filelink)) {
//echo $filelink;
// playdrive.plyr.xyz
  // https://playdrive.xyz/e/JYJ8JXDJEloo7JD/?sub=https://sub.plyr.xyz/tt3484204.srt
  // https://playdrive.xyz/d/JYJ8JXDJEloo7JD
  // https://playdrive.xyz/embed/1IsGv8iIApLCvizJMVWoZ18McXmJMrcS5/view/?sub=https://sub.ezylink.co/tt4382872.srt
  // https://playdrive.xyz/embed/1IsGv8iIApLCvizJMVWoZ18McXmJMrcS5/dl
  // https://playdrive.plyr.xyz/e/hvP9eZNdvqeKfgP/?sub=https://sub.plyr.xyz/tt9645476.srt
  $filelink=str_replace("/d/","/e/",$filelink);
  if (preg_match("/sub\=/",$filelink)) {
    $t1=explode("sub=",$filelink);
    $srt=$t1[1];
  }
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://movieforfree.co");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;

  if (preg_match_all("/\[(360|480|720|1080)P\](http[\w\/\:\?\-\_\.\=]+)\,/i",$out,$m)) {
  //print_r ($m);
  $r=array();
  $r=array_combine($m[1],$m[2]);
  //print_r ($r);
  if (isset($r['1080']))
    $link=$r['1080'];
  elseif (isset($r['720']))
    $link=$r['720'];
  elseif (isset($r['480']))
    $link=$r['480'];
  elseif (isset($r['360']))
    $link=$r['360'];
  else
    $link="";
  } elseif (preg_match("/sources\:\[/",$out)) {

  $t1=explode('sources:[',$out);
  $t2=explode(']',$t1[1]);
  $x =json_decode("[".$t2[0]."]",1);
  //print_r ($x);
  if (isset($x[0]['file'])) {
   for ($k=0;$k<count($x);$k++) {
    if ($x[$k]['label'] == "1080P") {
      $link=$x[$k]['file'];
      break;
    } elseif ($x[$k]['label'] == "720P") {
      $link=$x[$k]['file'];
      break;
    } elseif ($x[$k]['label'] == "480P") {
      $link=$x[$k]['file'];
      break;
    } elseif ($x[$k]['label'] == "360P") {
      $link=$x[$k]['file'];
      break;
    }
   }
  }
  $link=str_replace("/?","?",$link);
  if (substr($link, -1)=="/") $link=substr($link, 0, -1);
   if ($link && $flash <> "flash") {
     $link=$link."|Origin=".urlencode("https://playdrive.xyz")."&Referer=".urlencode("https://playdrive.xyz");
     $link=$link."&User-Agent=".urlencode($ua);
   }
  } else {   // ?????????????????????????
  if (preg_match("/location:\s*(.+)/i",$h,$n)) {
   $filelink= "https://playdrive.xyz".trim($n[1]);
  }
  //echo $filelink;
  //die();
  if (preg_match("/\/(e|d|embed)\/(\w+)/",$filelink,$m)) {
   $id=$m[2];
   $filelink="https://playdrive.xyz/d/".$id;

  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://movieforfree.co");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('id="playdrive',$h);
  preg_match_all("/href\=\"(.*?)\"/",$t1[1],$p);
  $link=$p[1][count($p[1])-1];
  if (preg_match("/\/(1080|720)\//",$p[1][0])) $link=$p[1][0];
  $link=str_replace("/?","?",$link);
  if (substr($link, -1)=="/") $link=substr($link, 0, -1);
   if ($link && $flash <> "flash") {
     $link=$link."|Origin=".urlencode("https://playdrive.xyz")."&Referer=".urlencode("https://playdrive.xyz");
     $link=$link."&User-Agent=".urlencode($ua);
   }
  }
  }
}
if (preg_match("/(https:\/\/player\.plyr\.xyz)|(https:\/\/plyr\.xyz\/player)/i",$filelink)) {
// https://plyr.xyz/player/index.php?data=Wxej7Wh7EPFDInd
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Connection: keep-alive',
'Referer: https://movieforfree.co');
//echo $filelink;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('var json =',$h);
  $t2=explode(';',$t1[1]);
  $r=json_decode(trim($t2[0]),1);
  //print_r ($r);
  if (isset($r['tracks'][0]['file']))
   $srt=$r['tracks'][0]['file'];
  if (isset($r['videoData']['videoSources'][0]['file'])) {
    $link= $r['videoData']['videoSources'][0]['file'];
    if ($link && $flash <> "flash") {
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://plyr.xyz");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close ($ch);

    //echo $h;
    //$h=file_get_contents($link);
    $link=get_max_res($h,$link);
    $out="#EXTM3U"."\r\n";
    $out .="#EXT-X-VERSION:3"."\r\n";
    $out .="#EXT-X-STREAM-INF:BANDWIDTH=5000000,RESOLUTION=1920x1080"."\r\n".$link;
    file_put_contents("lava.m3u8",$out);
    $link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
     $link=$link."|Origin=".urlencode("https://plyr.xyz")."&Referer=".urlencode("https://plyr.xyz");
     $link=$link."&User-Agent=".urlencode($ua);
    }
  }
  //echo $srt;
}
/////////////////////////////////////////////////////////////////////////////////////
if (preg_match("/playmezzz\.xyz\/vod\//",$filelink)) {
  // https://playmezzz.xyz/vod/12/The.Bloody.Man.2022.720p.WEBRip.900MB.x264-GalaxyRG.mp4/playlist.m3u8
  // from m4uhd
  $link=$filelink;
}
if (preg_match("/hellabyte\.cloud/",$filelink)) {
  //echo $filelink;
 // https://hellabyte.cloud/drive/s/NbzkZeuL5l5oRgistrGqZMueXEy333/view?v=zM6458G68
 //$filelink="https://embed.hellacdn.com/NbzkZeuL5l5oRgistrGqZMueXEy333?v=zM6458G68";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://m4uhd.tv/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //$h=file_get_contents($filelink);
  //echo $h;
  
}
if (preg_match("/play\.playm4u\.xyz/",$filelink)) {
  $t1=explode("caption=",$filelink);
  $srt=$t1[1];
  //echo $srt;
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://playhq.net");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match("/idSub\s*\=\s*\"([^\"]+)\"/",$h,$s)) {
    $idsub=$s[1];
    $srt="http://subsubforme.xyz/sub/sub/".$idsub.".srt";
  }
  //echo $srt;
  if (preg_match("/idfile\s*\=\s*\"(\w+)\"/",$h,$m))
   $id=$m[1];
  if (preg_match("/idUser\s*\=\s*\"(\w+)\"/",$h,$m))
   $user=$m[1];
  $t1=explode("DOMAIN_API = '",$h);
  $t2=explode("'",$t1[1]);
  $DOMAIN_API=$t2[0];
  $t1=explode("DOMAIN_LIST_RD = [",$h);
  $t2=explode("]",$t1[1]);
  $code="\$DOMAIN_LIST_RD = [".$t2[0]."];";
  eval ($code);
  $l=$DOMAIN_API.$user."/".$id;
  //echo $l;
  // https://api-plhq.playm4u.xyz/apidatard/5e8dd16b70eac4137a676553/62c7a501ff61d4a8b8578ebd
  // https://api-plhq.playm4u.xyz/apidatard/5e8dd16b70eac4137a676553/62c7a501ff61d4a8b8578ebd
  // https://api-sing.vnstream.net/apiv3/5e8dd16b70eac4137a676553/5f3f1a8d4d6f1d25eb2ce99d
  //$l="https://api-sing.vnstream.net/apiv3/5e8dd16b70eac4137a676553/5f3f1a8d4d6f1d25eb2ce99d";
  //print_r ($DOMAIN_LIST_RD);
  $post="referrer=https://m4uhd.tv";  // &typeend=html

  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'Content-Cache: no-cache',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: https://play.playm4u.xyz',
  'Alt-Used: play.playm4u.xyz',
  'Connection: keep-alive',
  'Referer: https://play.playm4u.xyz');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
///////////////////////////////////////////////
  $x=json_decode($h,1);
  //print_r ($x);
  $v=$x['v'];
  $dd=$x['domainrd'];
  // https://play.playm4u.xyz
  // https://plhq01.ggccntt001.xyz/stream/v5/dc1cefd01015f476319f2c9b00279be3d190f964794ca8ad1519232b1cee50b57b3f21a4293057936660f6b87708c8fe.html
  // https://plhq01.ggccntt001.xyz/stream/v5/dc1cefd01015f476319f2c9b00279be3d190f964794ca8ad1519232b1cee50b57b3f21a4293057936660f6b87708c8fe.html
  // https://plhq01.ggccntt001.xyz/stream/v5/5e8dd16b70eac4137a676553/dc1cefd01015f476319f2c9b00279be3d190f964794ca8ad1519232b1cee50b57b3f21a4293057936660f6b87708c8fe.html
  // https://plhq01.ggccntt001.xyz/stream/v5/c0ca54b7d5df2e14a31e037d1449ece4e635ca95c39da3e099206a4aa5b89ff85a359bfe2be0404959e1f7813a91a3fb.html
  // https://plhq01.ggccntt001.xyz/stream/v5/9e0ffdc2b4282ac1cec4e4e7fa5f06177d86cb4efa82777ed5026cf3f12faa80da750da066c19b59899d73b30bd36619.html
  $out = "#EXTM3U"."\n";
  $numdm_rd = count($DOMAIN_LIST_RD);
  $out .= "#EXT-X-VERSION:3"."\n";
  $out .="#EXT-X-TARGETDURATION:".$x["tgdr"]."\n";
  $out .="#EXT-X-PLAYLIST-TYPE:VOD"."\n";
  for($i = 0; $i < count($x['data'][0]); $i++) {
   $out .="#EXTINF:".$x['data'][0][$i].","."\n";
   //$out .="https://".$DOMAIN_LIST_RD[$i % $numdm_rd]."/rdv".$v."/".$x["quaity"]."/".$user."/".$x['data'][1][$i].".rd"."\n";
   //$out .="https://".$DOMAIN_LIST_RD[$i % $numdm_rd]."/stream/v".$v."/".$x['data'][1][$i].".html"."\n";
   $t1=explode("|",$x['data'][1][$i]);
   if ($t1[0]==2)
   $out .=$t1[1]."\n";
   else
   $out .=$dd."/rdv".$t1[0]."/".$t1[1]."\n";
  }
  $out .="#EXT-X-ENDLIST";
  //echo $out;
  //die();
  // "hserver.php?file=".
  file_put_contents("lava.m3u8",$out);
  //echo $out;
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  //$link .="|Origin=".urlencode("https://play.playm4u.xyz")."&Referer=".urlencode("https://play.playm4u.xyz");
  //$link .="&User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:100.0) Gecko/20100101 Firefox/100.0");
  //$link=$link."&User-Agent=".urlencode($ua);
  }
}
if (preg_match("/hls\.ezylink\.co/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER,"https://player.ezylink.co");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('var json =',$h);
  $t2=explode(';',$t1[1]);
  $r=json_decode(trim($t2[0]),1);
  if (isset($r['tracks'][0]['file']))
   $srt=$r['tracks'][0]['file'];
  if (isset($r['videoData']['videoSources'][0]['file'])) {
    $link= $r['videoData']['videoSources'][0]['file'];
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://player.ezylink.co");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close ($ch);
    //echo $h;
    //$h=file_get_contents($link);
    $link=get_max_res($h,$link);
    if ($link && $flash <> "flash") {
     $link=$link."|Origin=".urlencode("https://hls.ezylink.co")."&Referer=".urlencode("https://hls.ezylink.co");
     $link=$link."&User-Agent=".urlencode($ua);
    }
  }
}

/////////////////////////////////////////////////////
if (preg_match("/uniquestream/",$filelink)) {
//echo $filelink;
// uniquestreaming.net
 $cf="https://basic-bundle-solitary-morning-4d74.quamatbanty02.workers.dev/?";
 $t1=explode("id=",$filelink);
 $t2=explode("&",$t1[1]);
 $id=$t2[0];
 $t1=explode("&type=",$filelink);
 $type=$t1[1];
 $host=parse_url($filelink)['host'];
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
 $l="https://".$host."/wp-admin/admin-ajax.php";
 $l=$cf.$l;
 $post="action=doo_player_ajax&post=".$id."&nume=1&type=".$type;
 //echo $post;
 //$post="action=doo_player_ajax&post=300522&nume=1&type=movie";
 $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Referer: https://'.$host.'',
 'Content-Type: application/x-www-form-urlencoded',
 'Content-Length: '.strlen($post).'',
 'Origin: https://'.$host.'',
 'Connection: keep-alive');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $x=json_decode($html,1);
  //print_r ($x);
  $l="https:".json_decode($html,1)['embed_url'];
  $l=str_replace(" ","%20",$l);
  //echo $l."\n";
  //$head=array('Origin: https://'.$host);
  // Referer: https://uniquestream.net/
  //$l="https://hls.uniquestream.net/db_embed?id=G36F366670306F326A616D76706A723";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://uniquestream.net/',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $t1=explode("let url = '",$html);
  $t2=explode("'",$t1[1]);
  $link=$t2[0];
  $t1=explode("track['file'] = encodeURI('",$html);
  $t2=explode("'",$t1[1]);
  $srt=$t2[0];
  $srt=str_replace(" ","%20",$srt);
  //echo $srt;
  //echo $link;
  /*
  if (strpos($link,".m3u8") !== false) {
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  echo $html;
  $link1=get_max_res($html,$link);
  }
  */
  if ($flash <> "flash") {
    $host="https://".parse_url($link)['host'];
    $host="https://uniquestream.net/";
    $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
}
if (preg_match("/123stream\.be|emovies\.io/",$filelink)) {
  $host=parse_url($filelink)['host'];
  parse_str(parse_url($filelink)['query'],$q);
  $id=$q['episode_id'];
  $serv=$q['s'];
  $l="https://123stream.be/ajax/v4_get_sources?s=".$serv."&id=".$id."&_=".(time()*1000);
  $l="https://emovies.io/ajax/v4_get_sources?s=".$serv."&id=".$id."&_=".(time()*1000);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
  $host="emovies.io";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  //$x=json_decode($html,1);
  //print_r ($x);
  $l=json_decode($html,1)['value'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match("/tracks:\s*\[(.*?)\]/",$h,$n)) {
   $t1=explode(",{",$n[1]);
   $x=json_decode($t1[0],1);
   $srt=$x['file'];
   if (strpos($srt,"http") === false) $srt="https:".$srt;
  }
  if (preg_match("/slug\"\,\"value\":\"(.*?)\"/",$h,$s)) {   // vserver
   $filelink="https://playhydrax.com/?v=".$n[1];
  } elseif (preg_match("/sources:\s*\[(.*?)\]/",$h,$m)) {
   $t1=explode(",{",$m[1]);
   $x=json_decode($t1[0],1);
   $link=$x['file'];
  }
}
if (preg_match("/filmele-online\.com/",$filelink)) {
//echo $filelink;
//$filelink="https://filmele-online.com/server/an-unremarkable-christmas-2020.html";
// https://filmele-online.com/server/an-unremarkable-christmas-2020.html
//$l="http://filmele-online.com/embed/10050.html";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: 78',
'Origin: https://filmele-online.com',
'Connection: keep-alive',
'Referer: '.$filelink,
'Cookie: domain-alert=1',
'Upgrade-Insecure-Requests: 1');
$post="filmul=filmul&content-protector-submit.x=572&content-protector-submit.y=232";
$post="filmulnow=filmul&content-protector-submit.x=570&content-protector-submit.y=238";
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('tracks:',$h);
  $t2=explode('file": "',$t1[1]);
  $t3=explode('"',$t2[1]);
  if (preg_match("/srt|vtt/",$t3[0]))
   $srt=$t3[0];
  $t1=explode('file": "',$h);
  $t2=explode('"',$t1[1]);
  $l2=$t2[0];
  if ($l2[0]=="/") $l2="https:".$l2;
  $t1=explode("|",$l2);
  $ref = $t1[1];
  $link=$t1[0];
  if (preg_match("/url\=/",$link) && $flash <> "flash") {
  $t1=explode("url=",$link);
  $link=$t1[1];
  }
  if ($flash <> "flash" && $link) $link =$link."|".$ref;
}
if (preg_match("/m4ufree\.yt/",$filelink)) {  // cinebloom
//$h=file_get_contents($filelink);
//echo $h;
//echo $filelink;
  parse_str(parse_url($filelink)['query'],$q);
  $id=$q['id'];
  if (isset($q['sub']))
   $srt=$q['sub'];
  $link="https://m4ufree.yt/playlist/".$id."/".(time()*1000);
  if ($flash=="flash") {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:72.0) Gecko/20100101 Firefox/72.0');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch,CURLOPT_REFERER,$filelink);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);

   if (preg_match ("/^(?!#).+/m",$h,$m)) $link="https://m4ufree.yt".trim($m[0]);
  }
}
if (preg_match("/vid215\.xyz/",$filelink)) {  // cinebloom
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:72.0) Gecko/20100101 Firefox/72.0');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch,CURLOPT_REFERER,$filelink);
   curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_NOBODY,1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h2 = curl_exec($ch);
   curl_close($ch);
   //echo $h2;
   if (preg_match("/location\:\s*(.+)/i",$h2,$m))
    $filelink=trim($m[1]);
   else
    $filelink="";
   //echo $filelink;
}
if (preg_match("/cdn1\.moviehaat\.net/",$filelink)) {
 $link=str_replace(" ","%20",$filelink);
}
if (preg_match("/moviehaat\.net\//",$filelink)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://moviehaat.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
}
if (strpos($filelink,"fsharetv.") !== false) {
//echo $filelink;
$filelink=htmlspecialchars_decode($filelink, ENT_QUOTES);
//echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  /*
  $t1=explode('location.replace("',$h);
  $t2=explode('"',$t1[1]);
  $filelink=$t2[0];
  //$filelink="https://fsharetv.io/navigating";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.io");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  $filelink="https://fsharetv.io/navigating";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://google.com");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  if (preg_match("/captions\"\s+src\=\"(.*?)\"/",$h,$s)) {
    $srt="https://fsharetv.io".$s[1];
  }
  $t1=explode("Movie.setSource('",$h);
  $t2=explode("'",$t1[1]);
  $l="https://fsharetv.io/api/file/".$t2[0]."/source";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  if (isset($r['data']['file']['sources'][0]['src'])) {
   $link="https://fsharetv.io".$r['data']['file']['sources'][0]['src'];
  file_put_contents("lava.m3u8",$link);
  if ($flash == "flash") {

  //$p = dirname($_SERVER['HTTP_REFERER']);
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
   if ($flash <> "flash")
    $link=$link."|Referer=".urlencode("https://fsharetv.io");
  }
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
}
if (strpos($filelink,"streamlord.com") !== false) {
  //$filelink="http://www.streamlord.com/episode-izombie-s05e11-21102.html";
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.streamlord.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\<iframe class\=\"loading\-iframe\" id\=\"iframe\" src\=\"([^\"]+)\"/",$h,$m)) {
   $filelink=$m[1];
  } else {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
   $srt=$m[1];
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $h .= $jsu->Unpack($h);
  }
  //echo $h;
  if (preg_match("/(https?\:\/\/stream\d+\.streamlord\.com\:8080.+)\"/",$h,$m))
   $link=$m[1];
  if (preg_match("/return\(\"([^\"]+)\"/",$h,$m))
   $link=$m[1];
  }
}
if (preg_match("/streamdb\./",$filelink)) {   // from streamlord
//echo $filelink;
  //https://streamdb.top/player/wctv.php?data=6968614-mv
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.streamlord.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\<iframe width\=\"100\%\" height\=\"100\%\" frameborder\=\"0\" src\=\"([^\"]+)\"/",$h,$m)) {
  $filelink=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.streamlord.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  } elseif (preg_match("/iframe class\=\"loading\-iframe\" id=\"iframe\" src\=\"([^\"]+)\"/",$h,$m)) {
  $filelink=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.streamlord.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  }
  $srt="";
  $link="";
  if (preg_match("/var\s+server\d\s+\=\s+\"([^\"]+)\"/",$h,$m))
    $link=$m[1];
  $h=str_replace("\\","",$h);
  preg_match_all("/file\"\:\"([^\"]+)\"\,\"label\"\:\"([\w|\s]+)\"\,\"kind\"\:\"captions\"/",$h,$s);
  //print_r ($s);
  for ($k=0;$k<count($s[2]);$k++) {
   if (preg_match("/romanian/i",$s[2][$k])) {
     $srt=$s[1][$k];
     //echo $srt;
     break;
   }
   }
   if (!$srt) {
   for ($k=0;$k<count($s[2]);$k++) {
     if (preg_match("/english/i",$s[2][$k])) {
     $srt=$s[1][$k];
     break;
   }
  }
  }
  //echo $srt;
  //echo $h;
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.streamlord.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  if ($flash <> "flash")
   $link .="|Referer=".urlencode("https://streamdb.co")."&Origin=".urlencode("https://streamdb.co");
  }
}
if (strpos($filelink,"player.apimdb.net") !== false) {
  $filelink=str_replace(" ","%20",$filelink);
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"player.apimdb.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\<iframe.+src\=\"(.*?)\"/",$h,$m)) {
   $l=$m[1];
   if (substr($l, 0, 5) == "/play") {
    $l= "https://player.apimdb.net".$l;
    $l=str_replace(" ","%20",$l);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,"https://player.apimdb.net");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);

    $t1=explode('class="picasa">',$h);
    $h1=$t1[1];

    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h1);
    $out1 = $jsu->Unpack($out);
    $t1=explode("sources:[{file:'",$out1);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
    if (substr($link, -1) == "#") $link = substr($link, 0, -1);
   } else {
    if (strpos($l,"http") === false)
     $filelink="https:".$l;
    else
     $filelink=$l;
   }
  } else {
    $t1=explode('class="picasa">',$h);
    $h1=$t1[1];
    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h1);
    $out1 = $jsu->Unpack($out);
    //echo $out1;
    $t1=explode("sources:[{file:'",$out1);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
    if (substr($link, -1) == "#") $link = substr($link, 0, -1);
  }
}
if (strpos($filelink,"123files.club") !== false) {
  $filelink=str_replace(" ","%20",$filelink);
  //echo $filelink;
//$l="https://123files.club/play/1mp4-cl/play.php?id=TTZxc0tqWXFOekpOTHNyUDE4c295YzBCQUE9PQ==&imdb=tt3896198&s=&e=&type=movie";
  //file_put_contents("123files.html",$h);
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://play.123files.club");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\<iframe.+src\=\"(.*?)\"/",$h,$m)) {
   $l=$m[1];
   if (substr($l, 0, 5) == "/play") {
    $l= "https://play.123files.club".$l;
    $l=str_replace(" ","%20",$l);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,"https://play.123files.club");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);

    $t1=explode('class="picasa">',$h);
    $h1=$t1[1];

    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h1);
    $out1 = $jsu->Unpack($out);
    $t1=explode("sources:[{file:'",$out1);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
    if (substr($link, -1) == "#") $link = substr($link, 0, -1);
   } else {
    if (strpos($l,"http") === false)
     $filelink="https:".$l;
    else
     $filelink=$l;
   }
  } else {
    $t1=explode('class="picasa">',$h);
    $h1=$t1[1];
    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h1);
    $out1 = $jsu->Unpack($out);
    //echo $out1;
    $t1=explode("sources:[{file:'",$out1);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];
    if (substr($link, -1) == "#") $link = substr($link, 0, -1);
  }
}
if (preg_match("/watch4unow\.com|gateaflam\.com|aflamgulf\.com/",$filelink)) { // from movs4u
 //echo $filelink."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html."\n"."=================="."\n";
  if (preg_match("/http\-equiv\=\"refresh\"/",$html)) {
   $t1=explode('url=',$html);
   $t2=explode('"',$t1[1]);
   $filelink=$t2[0];
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING, "");
   //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
  if (preg_match("/\<iframe/",$h)) {  // https://gdriveplayer.co
  if (preg_match("/src\=[\'|\"](.*?)[\'|\"]/",$h,$z)) {
    if (strpos($z[1],"http") === false)
      $filelink="https:".$z[1];
    else
      $filelink=$z[1];
  }
  } elseif (preg_match("/file\"\:\s*\"([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]+)\"/",$h,$m)) {
    $link=trim($m[1]);
  }
  } elseif (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/", $html)) {
    require_once("JavaScriptUnpacker.php");
    $jsu = new JavaScriptUnpacker();
    $h = $jsu->Unpack($html);
    //echo $h;
    if (preg_match("/file\"\:\s*\"([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=\!]+)\"/",$h,$m)) {
     $link=trim($m[1]);
    }
  }
}
if (strpos($filelink,"tvhd-online.") !== false) {
$cookie=$base_cookie."tvhd.dat";
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: http://tvhd-online.com');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $html = curl_exec($ch);
  curl_close($ch);
  $t1=explode('streamsrc = "',$html);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch, CURLOPT_REFERER, "http://tvhd-online.com");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location:\s*(http.+)/i",$h,$m))
    $link=trim($m[1]);
}
if (strpos($filelink,"flixgo.biz") !== false) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  //echo $filelink."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://flixgo.biz");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('file": "',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if ($link && $flash <> "flash")
   $link=$link."|Origin=".urlencode("https://flixgo.biz");
}
if (strpos($filelink,"redirector.gdriveplayer") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
  $filelink=str_replace(" ","%2B",$filelink);
  $filelink=str_replace("%20","%2B",$filelink);
  $filelink=str_replace("+","%2B",$filelink);
  //$filelink="https://redirector.gdriveplayer.me/drive/redir.php?id=941e3821c1d0c962c2f2714d73ff7086&type=movie&ids=19866&judul=The+Silence+of+the+Marsh+(2020)";
  //echo $filelink;
  $head=array('Cookie: __cfduid=d83ebc4bdc888e85e43779a3b520125b91585512433; __cf_bm=5da2f62b2e6dfcfb573a8534007d32c15a5351d8-1587721583-1800-AU1qQoht1c7OplnUGq+bBk7r32aR6qKjYn6OstgnxsIAeCWPWo4KnWsTIMDidpJchlINgtmsUx8I1aXfFPIliLlq8PZ8ZxaiCipaPlBEkSwV; access=d41d8cd98f00b204e9800998ecf8427e; redir=941e3821c1d0c962c2f2714d73ff7086');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, "https://database.gdriveplayer.me");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match_all("/location:\s*(http.+)/i",$h,$m))
    $link=trim($m[1][count($m[1])-1]);
  //$link=$filelink;
}
if (strpos($filelink,"foumovies.") !== false) {
  $t1=explode("?file=",$filelink);
  $filelink=$t1[0];
  $host=parse_url($filelink)['host'];
  $fname=$t1[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $q=array();
  if (preg_match_all("/input name\=\"(FName|FSize|FSID)\"\s*type\=\"hidden\"\s*value\=\"(.*?)\"/msi",$html,$m)) {
  for ($k=0;$k<count($m[0]);$k =$k + 3) {
    if ($m[2][$k] == $fname) {
      $q['FName']=$fname;
      $q['FSize']=$m[2][$k+1];
      $q['FSID']=$m[2][$k+2];
    }
  }
  $l="https://".$host."/thank-you-for-downloading/";
   $post=http_build_query($q);
   //echo $post;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   //$t1=explode('anchor.href = "',$h);
   //$t2=explode('url=',$t1[1]);
   //$t3=explode('"',$t1[1]);
   //$link=$t3[0];
   // url=http://188.165.248.6/downloads/Moonfall.2022.007.BR.mp4
   if (preg_match("/url\=(https?\:\/\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/.*?\.mp4)/",$h,$n)) {
    $link=$n[1];
    $link=str_replace("&#038;","&",$link);
   }
  }
}
if (strpos($filelink,"pubfilm.xyz") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://pubfilm.xyz");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=array();
  $t1=explode('var jw =',$h);
  $t2=explode('</script',$t1[1]);
  $r=json_decode(trim($t2[0]),1);
  if (isset($r['file']))
   $link=$r['file'];
}
if (strpos($filelink,"5movies.") !== false) {
$cookie=$base_cookie."5movies.dat";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
if (file_exists($cookie)) {
 $x=file_get_contents($cookie);
 if (preg_match("/5movies\.fm	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
  $cc=trim($m[1]);
 else
  $cc="";
} else {
  $cc="";
}
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: https://5movies.fm\r\n"
  )
);
//print_r ($opts);
$context = stream_context_create($opts);
$html=@file_get_contents($filelink,false,$context);
 //echo $html;
 $t1=explode('div id="media-player"',$html);
 $html=$t1[1];
 if (preg_match("/Base64\.decode/",$html)) {
 $t1=explode('Base64.decode("',$html);
 $t2=explode('"',$t1[1]);
 $h=base64_decode($t2[0]);
 //echo $h;
 $t1=explode('src="',$h);
 $t2=explode('"',$t1[1]);
 $filelink=$t2[0];
 //echo $filelink;
 } else {
  $t1=explode('href="',$html);
  $t2=explode('"',$t1[1]);
  $filelink=$t2[0];
 }
 
}
//if (strpos($filelink,"ling") !== false) {
if (preg_match("/ling(\-|\.)online/",$filelink)) {
//echo $filelink;
  $filelink=str_replace("/iframe/","/video/",$filelink);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //if (preg_match("/\/\/[\w\d\/\_\:\.\?\-]+\.mp4/",$h,$m)) {
  if (preg_match("/source src\=\"([^\"]+)\"/",$h,$m)) {
   $link=$m[1];
   if ($link && $flash <> "flash")
    $link=$link."|Origin=".urlencode("https://ling-online.net")."&Referer=".urlencode("https://ling-online.net/");
   if (preg_match("/\/\/[\w\d\/\_\:\.\?\-]+\.(vvt|srt|vtt)/",$h,$n))
    $srt="https:".$n[0];
  }
}
if (strpos($filelink,"yifymovies.") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:76.0) Gecko/20100101 Firefox/76.0";
  $cookie=$base_cookie."hdpopcorns.dat";
  $host=parse_url($filelink)['host'];
  $t1=explode("?",$filelink);
  $post=$t1[1];
  $ref="https://yifymovies.tv";
  $l="https://yifymovies.tv/wp-admin/admin-ajax.php";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Origin: https://'.$host.'',
  'Connection: keep-alive',
  'Referer: '.$ref.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  $t1=explode('iframe src="',$r['embed_url']);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  $host=parse_url($l)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_REFERER, $ref);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('FirePlayer(vhash,',$h);
  $t2=explode(", false);",$t1[1]);
  $x=$t2[0];

  $y=json_decode($x,1);

  $l1="https://".parse_url($l)['host'].$y["videoUrl"]."?s=".$y["videoServer"];
//echo $l1;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$l1);
  if ($link) $link=$link."/v.mp4";
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://".$host)."&Origin=".urlencode("https://".$host);
}
if (strpos($filelink,"apicdn.vip") !== false) {
//echo $filelink;
//die();
 $filelink=str_replace("+","%2B",$filelink);
 $filelink=str_replace(" ","%2B",$filelink);
 $filelink=str_replace("%20","%2B",$filelink);
 $link=$filelink;
}
if (strpos($filelink,"moviehdkh.com") !== false) {
$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
//$ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
//$ua = $_SERVER['HTTP_USER_AGENT'];
  //$h=file_get_contents($filelink);
  //echo $filelink;
$cookie=$base_cookie."moviehdkh.txt";
//$ua=file_get_contents($base_pass."firefox.txt");

if (true == true) {
if (file_exists($base_pass."moviehdkh.txt"))
 $ses=file_get_contents($base_pass."moviehdkh.txt");
else
 $ses="";
  $head=array('Cookie: _moviehdkh_session='.$ses);
  //print_r ($head);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://www.moviehdkh.com");
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);

  //$h = urldecode($h);
  //curl_close($ch);
  //die();
  //echo $h;
$x=file_get_contents($cookie);
if (preg_match("/moviehdkh\.com	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
 $cc=trim($m[1]);
else
 $cc="";
 $ms="djhjd1VQVUFwYVBlNWNxcmlFekpTQzJ2QTk4VzFPNysydTBCT3cyTlZmcUw2bjgzZVMydVBPUnlERzVLTG51ZTdiWlJ5THFicjllV0JKRzNCczRFUG42T3kzMnZ5SzFUWmRwSEkxVjRiTCt3dTh3Y2JBVnB0dG0wYmZyMFdScU1MMmZqcithMk84MWt5dW1zMGVqcStnPT0tLUR1aElvcE4vc2FtOXF3NTF1dHV4cEE9PQ%3D%3D--16b65e50511a8fe1dc0959f6523d27e54060a599";
//print_r ($m);
//////////////////////////////////////////////////
$cf="cf_clearance";
//$cc="f8fa32636d8645ce09865f61b855587751177be2-1599206311-0-1z1b336d43za7f96604z14ff514f-150";
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: _moviehdkh_session=".$ms."\r\n".
              "Referer: https://www.moviehdkh.com/"."\r\n"
  )
);
$context = stream_context_create($opts);
//$filelink="https://www.moviehdkh.com/movies/after-the-thin-man-1936/watching";
//$h=@file_get_contents($filelink,false,$context);
//echo $filelink."\n";
//echo $h;
  $t1=explode('<iframe',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
//echo $l."\n";
//echo urldecode($l)."\n";
//echo urldecode("f2DX%2BBkTVfYcyhTUtRuoluiW9rb9f63MwF9gUJJ7YKJXly3l2Mdsnf1mTdHBhNoOZyAM3cV0T6S4HKU5wLETxQ%3D%3D");
//$l="G5Ckh2E5SsyAc4GzT6U4LlUXwuidPievLKLcpmo1i0TI3AD5oLbZzxwAnCznJD3qmO1cYhf6fyRqw9WqelPCXw%3D%3D";
  //$h=@file_get_contents($l,false,$context);

   //$ch = curl_init();
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  ////echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
//$h=@file_get_contents($l,false,$context);
//echo $h;
//$t1=explode('var videos = [',$h);
//$t2=explode(']',$t1[1]);
//$x=json_decode($t2[0],1);
//print_r ($x);
  //echo $l;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', urldecode($h), $m))
   $srt=$m[1];
   
  // echo $srt;
  /*
  $t1=explode("<iframe",$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  */
  //$l="https://drive.google.com/file/d/1IORuYhS9hU9gKA4NU1qd_le10qMLyw7A/view";
  if (preg_match("/drive\.google\.com/",$l)) {
  $pat = '@google.+?([a-zA-Z0-9-_]{20,})@';
  preg_match($pat,$l,$m);
  $id=$m[1];
  $l="https://drive.google.com/file/d/".$id."/view";
  ///echo $l;
  //$l="https://drive.google.com/file/d/1IORuYhS9hU9gKA4NU1qd_le10qMLyw7A/view";
  if (file_exists($base_pass."google_drive.txt"))
    $cookie=file_get_contents($base_pass."google_drive.txt");
  else
    $cookie="";
  $head=array('Referer: https://www.moviehdkh.com/',
  'Connection: keep-alive',
  'Cookie: '.$cookie.'',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/NID\=(\S+)/",$h,$t);
  preg_match("/DRIVE_STREAM\=(\S+)/",$h,$t1);
  $head=array("Cookie: NID=".$t[1]."; DRIVE_STREAM=".$t1[1]);
  $ad="NID=".$t[1]." DRIVE_STREAM=".$t1[1];
  $sPattern = '@\["fmt_stream_map","([^"]+)"]@';
  preg_match($sPattern,$h,$m);
  $videos=explode(",",$m[1]);
  //print_r ($videos);
  $a_itags=array(37,22,18);
  foreach ($videos as $video) {
   preg_match("/(\d+)\|(\S+)/",$video,$m);
   $links[$m[1]] = $m[2];
  }
  if (isset($links[37]))
    $link=$links[37];
  elseif (isset($links[22]))
    $link=$links[22];
  elseif (isset($links[18]))
    $link=$links[18];
  else
    $link="";
  $link = utf8_decode(implode(json_decode('["'.$link.'"]')));
  if ($link && $flash != "flash")
     $link=$link."|Cookie=".urlencode($ad);
  } else {
  $t1=explode('var videos = [',$h);
  $t2=explode(']',$t1[1]);
  $s="[".$t2[0]."]";
  $w=json_decode($s,1);
  //print_r ($w);
  $links=array();
    for ($k=0;$k<count($w);$k++) {
      $links[$w[$k]['label']]=$w[$k]['src'];
    }
    //print_r ($links);
    if (isset($links['1080']))
      $link=$links['1080'];
    elseif (isset($links['720']))
      $link=$links['720'];
    elseif (isset($links['480']))
      $link=$links['480'];
    elseif (isset($links['360']))
      $link=$links['360'];
    else
      $link="";

}
}
/*
//echo $filelink."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://www.moviehdkh.com");
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  //die();
  if (preg_match("/location:\s*(http.+)/i",$h,$s))
  $link=trim($s[1]);
  else
  $link="";
*/
//$link="https://lh3.googleusercontent.com/Vy0TUiL1j1QqPmq_aBf7W3iEFUuBOo1myhFnyyFFtDxN0d6K1DeoyrNm4z8Fv6Lyy7g5JiNMglx8LgDdKYrj";
//$link="https://lh6.googleusercontent.com/xOdFCaXcBNCv-q3h1PtQgJnBu6KImIpQMdWFd3GyeTgcLF-ZGp8DUQ18Yuv7O38OnqHmOVhJEUqM2N6QTjfV/dw1600";
if (preg_match("/googleusercontent\.com/",$link)) {

   //$h=file_get_contents($srt);
   //file_put_contents($base_sub."sub_extern.srt",$h);

$head=array('Accept: video/webm,video/ogg,video/*;q=0.9,application/ogg;q=0.7,audio/*;q=0.6,*/*;q=0.5',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Referer: https://www.moviehdkh.com/',
'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $z=0;
  $now=time();
  while (true) {

  $h = curl_exec($ch);

  if (preg_match_all("/location:(.+)/i",$h,$m)) {
    $link=trim($m[1][count($m[1])-1]);
    break;
  }
  //sleep (1);
  //$z++;
  if ((time() - $now) > 25) break;
  }
curl_close($ch);

}
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://www.moviehdkh.com")."&User-Agent=".urlencode($ua);
}
if (strpos($filelink,"9movies.") !== false) {
 $t1=explode("movie_sources/",$filelink);
 $eid=$t1[1];
 $l=$t1[0]."movie_sources/";
 $post="eid=".$eid;
 $head=array('Accept: application/json, text/javascript, */*; q=0.01',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
 'X-Requested-With: XMLHttpRequest',
 'Content-Length: '.strlen($post).'',
 'Origin: https://ww2.9movies.yt',
 'Connection: keep-alive',
 'Referer: https://ww2.9movies.yt/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['playlist'][0]['sources'][0]['file']))
   $link=$x['playlist'][0]['sources'][0]['file'];
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://ww2.9movies.yt")."&Origin=".urlencode("https://ww2.9movies.yt");
  if (isset($x['tracks'][0]['file']))
   $srt= $x['tracks'][0]['file'];
 //////////////////////////////////////////////////////
 //echo $link;
  if ($link && $flash=="flash") {
  if (preg_match("/\.m3u8/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\.m3u8/",$h)) {
  if (preg_match ("/^(?!#).+/m",$h,$m))
  $l="https://p2p2.vzcdn.xyz".$m[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $h=preg_replace_callback(
    "/^(?!#).+/m",  // num + ""
    function ($m) {
      return "redirect.php?file=".urlencode("https://p2p2.vzcdn.xyz".$m[0]);
    },
    $h
  );
  file_put_contents("lava.m3u8",$h);
  if ($flash == "flash") {

  //$p = dirname($_SERVER['HTTP_REFERER']);
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
}
}
}
}
if (strpos($filelink,"bmovies.") !== false) {
//echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://bmovies.cloud/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  $l=$x['src'];
  //echo $l;
  //$l=str_replace("embed-player","ajax/getSources",$l);
  // https://sn8.fstream365.xyz/assets/js/embed.min.js?v=0.1
  $head1=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://bmovies.cloud/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("\/","/",$h);

  //echo $h;
  //die();
  if (preg_match("/iframe src\=\"(.*?)\"/",$h,$m)) {
    $filelink=$m[1];
  } else {
   if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\+]*(\.(srt|vtt)))/', $h, $s))
    $srt="https:".$s[1];
   $t1=explode('sources',$h);
   $t2=explode('tracks',$t1[1]);
   $h=$t2[0];
   //echo $h;
   if (preg_match_all("/file\"\s*\:\s*\"(.*?)\"/",$h,$m)) {
   if (count($m[1]) > 1)
     $link=$m[1][count($m[1])-1];
   else
     $link=$m[1][0];
   $host=parse_url($l)['host'];
   if (strpos($link,"m3u8") !== false) {
   $head=array('Origin: https://'.$host.'',
   'Referer: '.$l);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
    $base1=str_replace(strrchr($link, "/"),"/",$link);
    $base2=getSiteHost($link);
    if (preg_match("/\.m3u8/",$h)) {
     $pl=array();
     if (preg_match_all ("/^(?!#).+/m",$h,$m))
      $pl=$m[0];
     if ($pl[0][0] == "/")
      $base=$base2;
     elseif (preg_match("/http(s)?:/",$pl[0]))
      $base="";
     else
      $base=$base1;
     if (count($pl) > 1) {
      if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
       preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
      else
       preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
      $max_res=max($m[1]);
      $arr_max=array_keys($m[1], $max_res);
      $key_max=$arr_max[0];
      $link=$base.$pl[$key_max];
     }
    }
   }
   if ($link && $flash <> "flash")
     $link=$link."|Referer=".urlencode($l)."&Origin=".urlencode("https://".$host);
   }
  }
}
//echo $filelink;
//if (strpos($filelink,"solarmovie.") !== false) {
if (preg_match("/(solarmovie|yesmovies)\./",$filelink)) {
//echo $filelink;
  $host=parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";

  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Referer: https://'.$host.'/movie/stargate-sg1-season-10-11201/19-1/watching.html',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $filelink=json_decode($html,1)['src'];

  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("v=",$filelink);
  $id=$t1[1];
  //echo base64_decode($id);
  preg_match("/xhr\[\'setRequestHeader\'\]\(\_0x\w+\(\'0x\w+\'\)\,\'([^\']+)\'/",$h,$m);
  //print_r ($m);
  //preg_match("/x\-csrf\-token\'\s*\,\s*\'([^\']+)\'/",$h,$m);
  $token=$m[1];
  $l="https://moplay.org/data";
  $post='{"doc":"'.$id.'"}';
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: '.$filelink,
'x-csrf-token: '.$token,
'Content-Type: application/json',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://moplay.org');
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);

  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['url'])) {
   $r=base64_decode($x['url']);
   //echo $r;
   if ($r[0] == "/") {
    if ($x['url']) $link="https://moplay.org".base64_decode($x['url']);
    $filelink="";
   } else
    $filelink=$r;
  }
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://moplay.org");

}
if (strpos($filelink,"vidcloud.is") !== false) {
  // https://vidcloud.is/watch?v=gAAAAABfhJ5XihRN1rDm4XrnhWvFnJT7B2u9Sji0nsxKgPqYlvzhKn6d2GEYGMrec17zphS_7U-fiB6c2FTRqKdS3GtdbQG-x-cKhuQDbwmN76R8ezQDR4wXfzwyRtu5tcMwjc1y7BzCA0aTwj6ysLh1w9O2HXJd1g==
function decode_code($code){
    return preg_replace_callback(
        "@\\\(x)?([0-9a-f]{2,3})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'ISO-8859-1', 'UTF-8');
        },
        $code
    );
}
//echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://yesmovies.ag");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=decode_code($h);
  //echo $h;
  $id="";
  if (preg_match("/\[\'code\'\]\=\'/",$h)) {
   $t1=explode("['code']='",$h);
   $t2=explode("'",$t1[1]);
   $id=$t2[0];
  } elseif (preg_match("/code\':\s*\'([\w|\_\-\=]+)\'/",$h,$m)) {     // code':'
   //print_r ($m);
   //$t1=explode("code: '",$h);
   //$t2=explode("'",$t1[1]);
   $id=$m[1];
  }
  $post='{"code":"'.$id.'"}';
  //echo $post;
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/json',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Origin: https://vidcloud.is',
  'Connection: keep-alive',
  'Referer: '.$filelink);
  $l="https://vidcloud.is/data";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['url'])) {
   $r=base64_decode($x['url']);
   //echo $r;
   if ($r[0] == "/") {
    if ($x['url']) $link="https://vidcloud.is".base64_decode($x['url']);
    $filelink="";
   } else
    $filelink=$r;
  }
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://vidcloud.is");
  //echo $filelink;
}
if (strpos($filelink,"streamvid.co") !== false) {
  // https://streamvid.co/player/pi5Mk6un7tIap1k/
 $host=parse_url($filelink)['host'];

  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('id="video_player">',$h);
  $h=$t1[1];
  $t=unjuice($h);
  //echo $t;
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($t);
  //echo $out;
  if (preg_match("/\/\/[a-zA-Z0-9\/\_\:\.\?\-]+\.m3u8/",$out,$m)) {
    $subs=array();
    $link="https:".$m[0];
    if (preg_match_all("/file\"\:\"([a-zA-Z0-9\/\_\:\.]+)\"\,\"label\"\:\"([a-zA-Z0-9]+)\"\,\"kind\"\:\"captions\"/msi",$out,$m)) {
     for ($k=0;$k<count($m[0]);$k++) {
      $subs[$m[2][$k]]=$m[1][$k];
     }
     if (isset($subs['Romanian']))
      $srt=$subs['Romanian'];
     elseif (isset($subs['English']))
      $srt=$subs['English'];
    }
    if ($link && $flash <> "flash") {
      if (substr($link, -1) =="/") $link=substr($link, 0, -1);
      $link=$link."|Referer=".urlencode("https://".$host);
    } else {
      if (substr($link, -1) =="/") $link=substr($link, 0, -1);
      $link=$link;
    }
 } else
   $link="";
 }

if (strpos($filelink,"moonline.") !== false) {
//echo $filelink;
///die();
 $host=parse_url($filelink)['host'];

  $ua = $_SERVER['HTTP_USER_AGENT'];
  parse_str(parse_url($filelink)['query'],$output);
  $l="https://".$host."/wp-admin/admin-ajax.php";
  $post="action=doo_player_ajax&post=".$output['post']."&nume=".$output['nume']."&type=".$output['type'];
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post).'',
'Origin: https://'.$host.'',
'Connection: keep-alive',
'Referer: https://'.$host.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  //curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/src=(\'|\")(.*?)(\'|\")/",$h,$m);
  $l=$m[2];
  if (strpos($l,"moonline.") === false)
    $filelink=$l;
  else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t=unjuice($h);
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($t);
  //echo $out;
  if (preg_match_all("/file\"\:\"([a-zA-Z0-9\/\_\:\.]+)\"\,\"label\"\:\"(\d+)P\"/msi",$out,$m)) {
    $links=array();
    $subs=array();
    for ($k=0;$k<count($m[0]);$k++) {
      $links[$m[2][$k]]=$m[1][$k];
    }
    //print_r ($links);
    if (isset($links['1080']))
      $link=$links['1080'];
    elseif (isset($links['720']))
      $link=$links['720'];
    elseif (isset($links['480']))
      $link=$links['480'];
    elseif (isset($links['360']))
      $link=$links['360'];
    else
      $link="";
    if (preg_match_all("/file\"\:\"([a-zA-Z0-9\/\_\:\.]+)\"\,\"label\"\:\"([a-zA-Z0-9]+)\"\,\"kind\"\:\"captions\"/msi",$out,$m)) {
     for ($k=0;$k<count($m[0]);$k++) {
      $subs[$m[2][$k]]=$m[1][$k];
     }
     if (isset($subs['Romanian']))
      $srt=$subs['Romanian'];
     elseif (isset($subs['English']))
      $srt=$subs['English'];
    }
    if ($link && $flash <> "flash") {
      if (substr($link, -1) =="/") $link=substr($link, 0, -1);
      $link=$link."|Referer=".urlencode("https://".$host);
    } else {
      if (substr($link, -1) =="/") $link=substr($link, 0, -1);
      $link=$link;
    }
 } else
   $link="";
 }
}
//if (strpos($filelink,"watchstreamonline.xyz") !== false) {
if (preg_match("/watchstreamdownload|watchstreamonline/",$filelink)) {
//watchstreamdownload.xyz
 $link=$filelink;
}
if (strpos($filelink,"hdm.to") !== false) {
  //echo $filelink;
  $filelink=str_replace(" ","%20",$filelink);
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://hdm.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $t1=explode("playlist.m3u8?",$html);
  $t2=explode('"',$t1[1]);
 $t1=explode("v=",$filelink);
 $link=str_replace("1o.to/", "hls.1o.to/vod/",$t1[1])."/playlist.m3u8?".$t2[0];
 if ($link && $flash <> "flash")
  $link=$link."|Referer=".urlencode("https://hdm.to");
}
if (strpos($filelink,"player.voxzer.org") !== false) {
  //https://player.voxzer.org/view/aab3c1e7c333d79ff15b25e0
  $filelink=str_replace("/view","/list",$filelink);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/json',
  'X-Requested-With: XMLHttpRequest',
  'Alt-Used: player.voxzer.org',
  'Connection: keep-alive',
  'Referer: https://player.voxzer.org/view/aab3c1e7c333d79ff15b25e0',
  'Cookie: _videofx=1',
  'Sec-Fetch-Dest: empty',
  'Sec-Fetch-Mode: cors',
  'Sec-Fetch-Site: same-origin');
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($html,1);
  if (isset($x['link'])) {
    $link=$x['link'];
    $origin="https://player.voxzer.org";
    if ($flash <> "flash")
     $link=$link."|Referer=".urlencode($origin)."&Origin=".urlencode($origin);
  }
  $head=array("Origin: ".$origin);
  /*
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER,$origin);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  echo $html;
  */
} elseif (strpos($filelink,"voxzer.org") !== false) {  // check for "slug" else redirect
//echo $filelink;
  // from yesmovies.ag
  include ("obfJS.php");
  $origin="https://".parse_url($filelink)['host'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$origin);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  if (preg_match("/Location:\s*(.+)/i",$html,$m)) {
    $filelink=trim($m[1]);
    $origin="https://".parse_url($filelink)['host'];
  }
  //echo $html;
  $enc=$html;
  $dec=obfJS();
  $slug="";
  $key="";
  $code="";
  if (preg_match("/code\':\'(.*?)\'/",$dec,$m))
   $code=$m[1];
  elseif (preg_match("/\[\'code\'\]\='(.*?)\'/",$dec,$m))
   $code=$m[1];
  if (preg_match("/key\':\'(\w+)\'/",$dec,$m))
     $key=$m[1];
  elseif (preg_match("/\[\'key\'\]\='(.*?)\'/",$dec,$m))
     $key=$m[1];
  if ($code) {
    if (preg_match("/key\':\'(\w+)\'/",$dec,$m))
      $key=$m[1];
    if (preg_match("/code\':\'(.*?)\'/",$dec,$m))
      $code=$m[1];
    $l=$origin."/data";
    $post='{"code":"'.$code.'"}';
    $head = array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   //'x-csrf-token: '.$token.'',
   'Content-Type: application/json',
   'Content-Length: '.strlen($post).'',
   'Origin: '.$origin.'',
   'Connection: keep-alive',
   'Referer: '.$filelink.'');
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $r=json_decode($h,1);
   $l=base64_decode($r['url']);
   if (preg_match("/\/hls.*\.m3u8/",$l,$m)) {
     $link=$origin.$m[0]; // direct
     if ($link && $flash != "flash")
       $link=$link."|Referer=".urlencode($origin)."&Origin=".urlencode($origin);
   } elseif ($key) {
      $slug=$l;
      //echo $key;
      ///////////////////////////////////////////////////////////////////////////////////////
      $l="https://multi.idocdn.com/vip";
      $post="key=".$key."&type=slug&value=".$slug;
      $head=array('Accept: */*',
      'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
      'Accept-Encoding: deflate',
      'Content-Type: application/x-www-form-urlencoded',
      'Origin: '.$origin.'',
      'Content-Length: '.strlen($post).'',
      'Connection: keep-alive');
      $ch = curl_init($l);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt ($ch, CURLOPT_POST, 1);
      curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $html = curl_exec($ch);
      curl_close ($ch);
      //echo $html;
      $x=json_decode($html,1);
      //print_r ($x);
      //die();
      if (isset($x['servers'])) {
      if (isset($x['servers']['redirect']))
       $server=$x['servers']['redirect'][0];
      else
       $server=$x['servers'][0];
      if (isset($x['fullhd']))
      $r=$x['fullhd'];
      else if (isset($x['hd']))
      $r=$x['hd'];
      else if (isset($x['sd']))
      $r=$x['sd'];
      else
      $r=array();

      $sig=$r['sig'];
      $id=$r['id'];
      $duration=$r['duration'];
      $hash=$r['hash'];
      $iv=$r['iv'];
      file_put_contents("hash.key",base64_decode($hash));
      $out ="#EXTM3U"."\r\n";
      $out .="#EXT-X-VERSION:4"."\r\n";
      $out .="#EXT-X-PLAYLIST-TYPE:VOD"."\r\n";
      $out .="#EXT-X-TARGETDURATION:".$duration."\r\n";
      $out .="#EXT-X-MEDIA-SEQUENCE:0"."\r\n";
      //$out .="#EXT-X-HASH:".$hash."\r\n";
      $out .='#EXT-X-KEY:METHOD=AES-128,URI="'.$hash_path."/hash.key".'",IV='.$iv."\r\n";

      $tot_dur=0;
      $tot_dur1=0;
      for ($k=0;$k<count($r['extinfs']);$k++) {
        $tot_dur += $r['extinfs'][$k];
      }
      $z=0;
      for ($k=0;$k<count($r['ranges']);$k++) {
       $dur=0;

       if ($flash == "flash") {
        //$l="https://".$server."/html/".$sig."/".$id."/".$r['ids'][$k]."/".$r['ids'][$k].".html?domain=".parse_url($origin)['host'];
        $l="https://".$server."/redirect/".$sig."/".$id."/".$r['ids'][$k]."/".$r['ids'][$k];
        $l_redirect="hserver.php?file=".base64_encode("link=".urlencode($l)."&origin=".urlencode($origin));
       }
       for ($p=0;$p<count($r['ranges'][$k]);$p++) {
        if ($flash == "flash") {
        $dur += $r['extinfs'][$z];
        $out .="#EXTINF:".$r['extinfs'][$z].","."\r\n";
        if (count($r['ranges'][$k]) > 1)
         $out .="#EXT-X-BYTERANGE:".$r['ranges'][$k][$p]."\r\n";
        $out .=$l_redirect."\r\n";
        } else {
        $dur += $r['extinfs'][$z];
        }
        $z++;
       }
       $tot_dur1 += $dur;
       if ($flash <> "flash") {
        $out .="#EXTINF:".$dur.","."\r\n";
        $l="https://".$server."/redirect/".$sig."/".$id."/".$r['ids'][$k]."/".$r['ids'][$k];
        $out .=$l."\r\n";
       }
      }
      $out .="#EXT-X-ENDLIST";

      if ($out) {
       file_put_contents("lava.m3u8",$out);
       if ($flash == "flash") {
        $link = $hash_path."/lava.m3u8";
       } else
        $link = $hash_path."/lava.m3u8"; //$link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
      } else {
        $link="";
      }
    } else {
     $link="";
    }
    if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode($origin)."&Origin=".urlencode($origin);
    } else {
      $filelink=$l;
      if (strpos($filelink,"http") === false) $filelink="https:".$filelink;
   }
  }
}
if (strpos($filelink,"https://embed.iseek.to") !== false) {
   // from moviegaga
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/7";
   $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Connection: keep-alive',
    'Referer: https://moviegaga.to');

   $ch = curl_init($filelink);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   curl_setopt($ch, CURLOPT_HEADER,1);
   $h = curl_exec($ch);
   curl_close ($ch);
   //echo $h;
   $t1=explode("var json_data = '",$h);
   $t2=explode("'",$t1[1]);
   $x=json_decode($t2[0],1);
   $l=$x['sources']['RAW'];
   //echo $l."\n";
   if ($l[0]=="_") {
    $l=substr($l,1);
    $link=preg_replace_callback(
    "/[a-zA-Z]/",
    function ($a1) {
     return chr(($a1[0] <= 'Z' ? 90 : 122) >= ($a1 = ord($a1[0]) + 13) ? $a1 : $a1 - 26);
    },
    $l
    );
    if (strpos($link,"http") === false) $link="https:".$link;
    //echo $link;
    $head=array('Referer: https://embed.iseek.to',
     'Upgrade-Insecure-Requests: 1');
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close ($ch);
    //echo $h;
    $t1=explode("ifr.target_url='",$h);
    $t2=explode("'",$t1[1]);
    $link=$t2[0];  // mcloud/notfound
    //echo $link;
    if (preg_match("/notfound/",$link)) $link="";
    if (strpos($link,"http") === false && $link) $link="https:".$link;
    $filelink=$link;
   } else {
    $link=$l;
    //echo $l;
    if (strpos($link,"http") === false && $link) $link="https:".$link;
   }
}
if (preg_match("/fmovies\.co/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Age: 0',
  'X-Requested-With: XMLHttpRequest',
  'Alt-Used: fmovies.co',
  'Connection: keep-alive',
  'Referer: https://fmovies.co',
  'Cookie: user-info=null;');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  $filelink=$x['target'];
}
if (preg_match("/vidcloud9\.org/",$filelink)) {
//echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $t1=explode('watch?v=',$filelink);
  $id=$t1[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fmovies.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $token="";
  if (preg_match("/xhr\[\'setRequestHeader\'\]\(\_0x[0-9a-f]+\(\'0x[0-9a-f]+\'\)\,\'([^\']+)\'/",$h,$y)) {
  //print_r ($y);
  $token=$y[1];
  }
  /*
  $t1=explode("code': '",$h);
  $t2=explode("'",$t1[1]);
  if (!$t2[0]) {
  $t1=explode("code':'",$h);
  $t2=explode("'",$t1[1]);
  }
  */
  /*
  if (preg_match("/jwply\(data\.url, \'(\d+)\'\, \'(\d+)\'\)/",$h,$m)) {
  //print_r ($m);
  $srt="https://sub.vxdn.net/sub/" . $m[1] . '-' . $m[2] . ".vtt";
  }
  */
  //echo $srt;
  //die();
  //$t2[0]="gAAAAABjuUIZy8H46IOh0NaTLkLE-60Bk2blMqRRDlzQpFxxpED07cZTU-XAazaqShFHSdW7eFv6nKBiEXGMp2Qhh0csSMHU26Tnh1Vrg9fvAuqIK0_0CsftGpC4oYTPra-HXQCNrAa69ID03JN8rB32sCnf11D2BA==";
  $l="https://vidcloud9.org/data";
  $post='{"doc":"'.$id.'"}';
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://vidcloud9.org/watch?v=gAAAAABipHf9luqRMjnmjlvzjX3z6c0EgZ045Zg3yqPSp26CTFaP3iOnEZlYz8cwDach6ZZFc529_ZLDxD1AtkDrYPj28EKJL-rW9gTvs_K6UC9JW9HUPM0_LeDwxvy3uuxFdsGKdQKPL52GGMWq64wRY_HKD_UHtg==',
  'Content-Type: application/json',
  'X-Requested-With: XMLHttpRequest',
  'x-csrf-token: '.$token,
  'Content-Length: '.strlen($post),
  'Origin: https://vidcloud9.org',
  'Alt-Used: vidcloud9.org',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://fmovies.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $x=$r['url'];
  $tip=$r['type'];
  $mid=$r['mid'];
  $srt="https://sub.vxdn.net/sub/".$mid."-1.vtt";
  if (isset($x[0])) {
  if ($tip=="direct") {
  $y=base64_decode($x);
  //$y=$x;
  if ($y[0]=="/") {
  $link="https://vidcloud9.org".$y;
  } else
  $link=$y;
  $filelink="";
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fmovies.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  } else {
   $filelink=$r['url'];
  }
  //echo $filelink;
  //echo $y;
  if ($link && $flash != "flash")
   $link .="|Referer=".urlencode("https://vidcloud9.org");
  }
}
if (preg_match("/ff?movies1\./",$filelink) && !preg_match("/123fmovies1/",$filelink)) {  //123fmovies.best
//echo $filelink;
$t1=explode("?",$filelink);
parse_str($t1[1],$w);
$tip=$w['tip'];
$link=$w['link'];
$ep=$w['ep'];
$href=$w['href'];
$host="fmovies.solar";
$host="ffmovies.io";
$host="ffmovies.co";
$host=parse_url($t1[0])['host'];
//print_r ($w);
//die();
 $ua = $_SERVER['HTTP_USER_AGENT'];
 $cookie=$base_cookie."ffmovies.dat";
 $time=round(time()/100)*100;
if ($tip=="series")
$l1="https://".$host."/ajax/film/servers?id=".$link."&_=840&ts=".$time;
else
$l1="https://".$host."/ajax/film/servers?id=".$link."&_=840&ts=".$time;
//$l1="https://ffmovies.to/ajax/film/servers/".$link;
///////////////////////////////////////////////////////////////////
$l2="https://".$host.$href;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  if (preg_match("/https.+)\/key/",$h2,$z)) {
    print_r ($z);
  }
*/
$r=array();
$s=array();
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://'.$host.'/film/in-the-tall-grass.'.$link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
$x=json_decode($h,1);
$h=$x['html'];
//echo $h;
/////////////////////////////////////////////
$head2=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://'.$host.'/film/5g-zombies.'.$link.'');
// https://mcloud2.to/key -->>  e8b62
$l2="https://mcloud.to/key";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head2);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  //echo $h2;
  $t1=explode("mcloudKey='",$h2);
  $t2=explode("'",$t1[1]);
  $key=$t2[0];
/////////////////////////////////////////////////
//if ($tip == "movie") {

$videos=explode('<li><a',$h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('data-id="',$video);
  $t3=explode('"',$t1[1]);
  $id=$t3[0];
  $t1=explode('href="',$video);
  $t2=explode('"',$t1[1]);
  if ($t2[0] == $href) break;
  }
  //https://ffmovies.to/ajax/episode/info?id=e84e5ab7ddc1ce79a765790b29767a7c89f4e20d1da7b897b90873eac9626947&mcloud=4f7c5&_=887&ts=1589526000
  //https://ffmovies.to/ajax/episode/info?id=1cf6dfdf85e33b52a812f393127c051fca8056c283b082194d602df299cb7a97&mcloud=4f7c5&_=887&ts=1589526000
  $l="https://".$host."/ajax/episode/info?id=".$id."&mcloud=".$key."&_=888&ts=".$time;
  $l="https://".$host."/ajax/episode/info?id=".$id."&mcloud=".$key;
  $l="https://".$host."/ajax/episode/info?id=".$id;
  //echo $l;
  //https://www12.fmovies.to/ajax/episode/info?id=b791e29f32afb9d89127b1200a38587f70e93e86120e7541823b25ce8b9e77e2&mcloud=
  //https://www12.fmovies.to/ajax/episode/info?id=b791e29f32afb9d89127b1200a38587fc8920981852d68db7dc3aebb970e9dc6&mcloud=
  //die();
  //1589538107
  //1589526000
  //1589551200
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://'.$host.$href.'');
  //print_r ($head);
  //die();
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $filelink=urldecode($x['url']);
  //echo  "\n".$filelink;
//}
//echo $filelink;
///////////////////////////////////////////////////
 $srt=$x['subtitle'];
}
if (strpos($filelink,"vidcdn.co") !== false) {
  //echo $filelink;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://zoechip.org");
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location\:\s+(.+)/i",$h,$m))
   $filelink=trim($m[1]);
  else
   $filelink="";
}
if (strpos($filelink,"dmovies.vidcdn.co") !== false) {
  // https://dmovies.vidcdn.co/movies/iframe/d04xdksxRG5GTXZpRTRpM2lmTmhFZHZPZWd6WTFVTTUrOFpBaTkvQmhwb0JYbGR2MHViUFpNd0dWdz09
  $ua = $_SERVER['HTTP_USER_AGENT'];
  //echo $filelink;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location\:\s+(.+)/i",$h,$m))
   $filelink=trim($m[1]);
  else
   $filelink="";
}
if (strpos($filelink,"2embed.") !== false) {
  // https://www.2embed.ru/embed/imdb/tv?id=tt9737326&s=1&e=3
  // https://www.2embed.to/embed/tmdb/movie?id=964403
  //echo $filelink;
  $t1=explode("?",$filelink);
  $host=parse_url($t1[0])['host'];
  /*
  $ua = $_SERVER['HTTP_USER_AGENT'];
  require_once ("rec.php");
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('data-recaptcha-key="',$h);
  $t2=explode('"',$t1[1]);
  $key=$t2[0];
  $t1=explode('data-id="',$h);  // only first
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  //$key="6LdBfTkbAAAAAL25IFRzcJzGj9Q-DKcrQCbVX__t";
  //$key="6Lf2aYsgAAAAAFvU3-ybajmezOYy87U4fcEpWS4C"; // 24.06.2022
  $co="aHR0cHM6Ly93d3cuMmVtYmVkLnJ1OjQ0Mw..";
  $co="aHR0cHM6Ly93d3cuMmVtYmVkLnJ1OjQ0Mw..";
  $loc="https://".$host;
  $sa="get_link";
  $token=rec($key,$co,$sa,$loc);
  $l="https://".$host."/ajax/embed/play?id=".$id."&_token=".$token;
  */
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Alt-Used: '.$host,
  'Connection: keep-alive',
  'Referer: https://'.$host.'/embed/imdb/tv?id=tt9737326&s=1&e=3');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  $filelink=$x['link'];
  */
  //echo $filelink;
  //////////////////////////////////////////
  //$filelink="https://www.2embed.cc/imdb/tt5761544";
  //$filelink="https://www.2embed.cc/imdb/tt0088170";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://net-film.vercel.app/explore');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $link="https://www.2embed.cc".$t2[0];
  if ($flash <> "flash")
    $link=$link."|Referer=".urlencode("https://www.2embed.cc/imdb/tt5761544")."&Origin=".urlencode("https://www.2embed.cc");
}
//if (strpos($filelink,"zoechip.") !== false) {
if (preg_match("/zoechip|watchtoday/",$filelink)) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  require_once ("rec.php");
  $key="6LfHPLoUAAAAAO0Jylr8Bn5RptHLGDdGuDybODPA";
  $key="6LdmPaAaAAAAAAZ57otOc0kv9b0xK12VarX-9NW2";
  $key="6LdmPaAaAAAAAAZ57otOc0kv9b0xK12VarX-9NW2";
  $co="aHR0cHM6Ly93d3cxLnpvZWNoaXAuY29tOjQ0Mw..";
  $co="aHR0cHM6Ly93d3cxLnpvZWNoaXAuY29tOjQ0Mw..";
  $co="aHR0cHM6Ly93d3cyLnpvZWNoaXAuY29tOjQ0Mw..";
  $co="aHR0cHM6Ly93d3cyLnpvZWNoaXAuY29tOjQ0Mw..";
  $sa="get_link";
  $loc="https://zoechip.com";
  $token=rec($key,$co,$sa,$loc);
  $l=$filelink."?_token=".$token;
  //echo $l;
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://zoechip.com',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive');

  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $y=json_decode($h,1);
  $filelink=$y['link'];
  //echo $filelink;
}
if (strpos($filelink,"moviesjoy.") !== false) {
  //$filelink="https://www.moviesjoy.net/ajax/movie_sources/2203229-30";
  //echo $filelink;
///////// recaptcha ///////////////////////////
//echo $filelink;
$host=parse_url($filelink)['host'];
$ua = $_SERVER['HTTP_USER_AGENT'];
include ("rec.php");
$key="6LehVusUAAAAAGmm_XUVG_n8srcy4SwpNCGobo-7";
//$key="6LfHPLoUAAAAAO0Jylr8Bn5RptHLGDdGuDybODPA";
$co="aHR0cHM6Ly9tb3ZpZXNqb3kudG86NDQz";
//$co=base64_encode("https://".$host.":443");
//$co="aHR0cHM6Ly9tb3ZpZXNqb3kubmV0OjQ0Mw..";
$sa="get_link";
$loc="https://".$host;
$recaptcha=rec($key,$co,$sa,$loc);
///////////////////////////////////////////////
$id = substr(strrchr($filelink, "/"), 1);
//$id="2228820-55";
//$id="812876";
$l="https://www.moviesjoy.net/ajax/movie_sources";
$l="https://www1.moviesjoy.net/ajax/get_link/".$id."?_token=".$recaptcha;
$l="https://".$host."/ajax/get_link/".$id."?_token=".$recaptcha;
//echo $l;
$post="episode_id=".$id."&token=".$recaptcha;
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://www.moviesjoy.net/movie/marvels-agents-of-shield-season-6-pG9P/2228820-55/watching.html',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Cookie: view-28575=true; pop-share=1');

  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $y=json_decode($h,1);
  //print_r ($y);
  //die();
  if ($y['type'] == "direct") {
  $r=$y["sources"];
  if (isset($r[0]["file"])) {
  $bfound=false;
  $c="";
  $q=array("1080p","720p","480p","360p","240p","180p");
  foreach($q as $v) {
   for ($k=0;$k<count($r);$k++) {
   if ($r[$k]["label"] == $v) {
     $c=$r[$k]["label"];
     $link=$r[$k]["file"];
     $bfound=true;
     break;
   }
   if ($bfound) break;
   }
   if ($bfound) break;
  }
  } else
     $filelink="";
  } else {
    $filelink=$y['link'];
    //$filelink="https://embed2.megaxfer.ru/beststream/aea6655d6fd8a5679ff977fd9c4e6552";
    if (preg_match("/megaxfer\.ru/",$filelink)) {
     $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/80.0',
     'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'Connection: keep-alive',
     'Referer: https://moviesjoy.to/watch-movie/enola-holmes-63478.3360158',
     'Upgrade-Insecure-Requests: 1');
     //echo $filelink;
     $ch = curl_init($filelink);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
     //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     curl_setopt($ch, CURLOPT_HEADER,1);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
     if (preg_match("/iframe\s+src\=[\"|\'](.*?)[\'|\"]/",$h,$m))
      $filelink=$m[1];
      $filelink=str_replace("&amp;","&",$filelink);
      //echo $filelink;
      $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0',
      'Accept: */*',
      'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
      'Accept-Encoding: deflate',
      'Connection: keep-alive');
      $ch = curl_init($filelink);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $html = curl_exec($ch);
      curl_close ($ch);
      //echo $html;
      $t1=explode('var domain = "',$html);
      $t2=explode('"',$t1[1]);
      $dom=$t2[0];
      $t1=explode("var tracks = `",$html);
      $t2=explode("`;",$t1[1]);
      //echo $t2[0];
      $w=json_decode($t2[0],1);
      //print_r ($w);
      $srt="";
      for ($k=0;$k<count($w);$k++) {
       if (preg_match("/romania/i",$w[$k]['label'])) {
         $srt=$w[$k]['file'];
         break;
       }
      }
      if (!$srt) {
      for ($k=0;$k<count($w);$k++) {
       if (preg_match("/english/i",$w[$k]['label'])) {
         $srt=$w[$k]['file'];
         break;
       }
      }
      }
      //echo $srt;
      //echo $filelink;
      // https://beta.beststream.io/api/iframe?id=893f7f4376ef9cdf71571d636020853a&e=1151509
      // https://beta.beststream.io/hls/json/info/893f7f4376ef9cdf71571d636020853a
      // https://beta.beststream.io/hls/m3u8/893f7f4376ef9cdf71571d636020853a/master.m3u8
      // https://beststream.io/hls/p2p/792867f6ff8a0fb50dbb31ccd53b866d/index.m3u8
      if (preg_match("/beststream\.io/",$filelink)) {
        $t1=explode("id=",$filelink);
        $t2=explode("&",$t1[1]);
        //$link="https://beststream.io/hls/file/".$t2[0]."/index.m3u8";
        if (preg_match("/beta\.beststream\.io/",$dom))
        $link=$dom."/hls/m3u8/".$t2[0]."/master.m3u8";
        else
        $link=$dom."/hls/p2p/".$t2[0]."/index.m3u8";
        //echo $link;
      }
    }
  }
  if (count($y['tracks']) == 1) {
  if (isset($y["tracks"][0]["file"]))
     $srt=$y["tracks"][0]["file"];
  } else {
    for ($k=0;$k<count($y['tracks']);$k++) {
      if (preg_match("/english|romanian/i",$y['tracks'][$k]['label'])) {
       $srt=$srt=$y["tracks"][$k]["file"];
       break;
      }
    }
  }
//echo $filelink;
//die();
}
if (strpos($filelink,"flixtor.to") !== false) {
    //https://flixtor.to/ajax/v4/e/4467058/1/7
    //echo $filelink;
    //$filelink="https://flixtor.to/ajax/v4/e/4467058/1/7";
 $ua = $_SERVER['HTTP_USER_AGENT'];
 $cookie=$base_cookie."flixtor.dat";
$head=array('Accept: text/plain, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://flixtor.to/watch/tv/4467058/watchmen/season/1/episode/7',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=base64_decode(str_rot13($h));
  $out="";
  for ($k=0;$k<strlen($x);$k++) {
    $k1 = ord($x[$k]);
    if (($k1>=33)&&($k1<=126))
    $t = chr(33 + ($k1 + 14) % 94);
    else
    $t =chr($k1);
    $out .=$t;
  }
  $y=json_decode($out,1);
  //print_r ($y);
  $link=$y['file'];
  for ($k=0;$k<count($y['tracks']);$k++) {
    if ($y['tracks'][$k]['kind'] == "captions") {
      if ($y['tracks'][$k]['label'] == "Romanian") $srt= $y['tracks'][$k]['file'];
    }
  }
  if (!$srt) {
  for ($k=0;$k<count($y['tracks']);$k++) {
    if ($y['tracks'][$k]['kind'] == "captions") {
      if ($y['tracks'][$k]['label'] == "English") $srt= $y['tracks'][$k]['file'];
    }
  }
  }
  if ($srt) $srt="https://flixtor.to".$srt;
  //echo $link;
}
if (strpos($filelink,"stream.123downloads.today") !== false) {
   $link=$filelink;
}
if (strpos($filelink,"gomovies.") !== false) {
   $ua = $_SERVER['HTTP_USER_AGENT'];
   $ua='Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
   $head=array(
   'Accept: application/json, text/javascript, */*; q=0.01',
   'Accept-Language: en-US,en;q=0.5',
   'Accept-Encoding: deflate',
   'X-Requested-With: XMLHttpRequest'
   );
   /*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER, "https://gomovies.tube");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   */
   $h=@file_get_contents($filelink);
   $t1=explode('window.subtitles =',$h);
   $t2=explode('</script',$t1[1]);
   $h=trim($t2[0]);
   $r=json_decode($h,1);
   //print_r ($r);
   if (isset($r[0]["src"])) $srt=$r[0]["src"];
/*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER, "https://gomovies.tube");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
*/
////////////////////////////////////////
$options = array(
        'http' => array(
        'header'  => "X-Requested-With: XMLHttpRequest\r\n",
        'method'  => 'GET'
    )
);

$context  = stream_context_create($options);
$h = @file_get_contents($filelink, false, $context);
//echo $h;
$r=json_decode($h,1);
///////////////////////////////////////
   //print_r ($r);
   if ($r['type'] == "iframe")
     $filelink=$r["link"];
   else
     $link=$r[0]["file"];
   //echo $filelink;
   /*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   //curl_setopt($ch, CURLOPT_REFERER, "https://gomovies.tube");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   echo $h;
   */
   //$link=str_replace("https","http",$link);
}
if (strpos($filelink,"stareanatiei.ro") !== false) {
   $referer="https://www.stareanatiei.ro";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   //curl_setopt($ch, CURLOPT_HEADER, true);
   //curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $t1=explode('video-embed">',$h);
   $t2=explode('src="',$t1[1]);
   $t3=explode('"',$t2[1]);
   $filelink=$t3[0];
   //echo $filelink;
   //die();
}
if (strpos($filelink,"watchseries") !== false) {
//echo $filelink;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   //curl_setopt($ch, CURLOPT_HEADER, true);
   //curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $t1=explode('class="video_player',$h);
   $t2=explode('href="',$t1[1]);
   $t3=explode('"',$t2[1]);
   $filelink=trim($t3[0]);
   if (strpos($filelink,"external/") !== false) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   //curl_setopt($ch, CURLOPT_HEADER, true);
   //curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   $t1=explode('class="video_player',$h);
   $t2=explode('href="',$t1[1]);
   $t3=explode('"',$t2[1]);
   $filelink=trim($t3[0]);
   }
}
if (strpos($filelink,"putlocker.tl") !== false) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   //curl_setopt($ch, CURLOPT_HEADER, true);
   //curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $t1=explode('Base64.decode("',$h);
   $t2=explode('"',$t1[1]);
   $l2=base64_decode($t2[0]);
   $t1=explode('src="',$l2);
   $t2=explode('"',$t1[1]);
   $filelink=$t2[0];
}
if (strpos($filelink,"api.vidnode.net") !== false) {
  //https://api.vidnode.net/stream.php?type=openload&sid=8aAiJHQsojM&eid=269051
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h2 = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location\:\s*(.+)/i",$h2,$m))
    $filelink=trim($m[1]);
  //echo $filelink;
}
if (preg_match("/embedo\.xyz\/player\.php/",$filelink)) {
 $ua = $_SERVER['HTTP_USER_AGENT'];
   if (file_exists($base_pass."streamembed.txt")) {
    $l=trim(file_get_contents($base_pass."streamembed.txt"));
    $l=$l."embedo1.php?file=".urlencode($filelink);
    //echo $l;
    $cookie="078741a200cc28871c1a9e5c6286a9dd";
    // Cookie: __test=452387fd832ec6e6833f14a2cfea376a
    //$cookie="452387fd832ec6e6833f14a2cfea376a";
    $head=array('Cookie: __test='.$cookie);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    //if (preg_match("/document\.cookie\=\"\_test\=/",$h)) {
    if(preg_match_all('/toNumbers\(\"(\w+)\"/',$h,$m)) {
     require_once( 'cryptoHelpers.php');
     require_once( 'aes_small.php');
     //$t1=explode('document.cookie="_test=',$h);
     //$t2=explode(' ',$t1[1]);
     //$cookie=$t2[0];
     //print_r ($m);
     $a=cryptoHelpers::toNumbers($m[1][0]);
     $b=cryptoHelpers::toNumbers($m[1][1]);
     $c=cryptoHelpers::toNumbers($m[1][2]);
     $d=AES::decrypt($c,16,2,$a,16,$b);
     //print_r ($m);
     $cookie=cryptoHelpers::toHex($d);
     $head=array('Cookie: __test='.$cookie);
     //print_r ($head);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_ENCODING, "");
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
   }
   //echo $h;
   $filelink=$h;
  }
}
if (preg_match("/imwatchingmovies\.com|streambucket\.net/",$filelink)) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head=array('User-Agent: '.$ua,
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Upgrade-Insecure-Requests: 1',
  'Sec-Fetch-Dest: document',
  'Sec-Fetch-Mode: navigate',
  'Sec-Fetch-Site: none',
  'Sec-Fetch-User: ?1');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch,CURLOPT_REFERER,"https://seapi.link");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (!preg_match("/value\=\"([^\"]+)\"\s+name\=\"captcha_id/",$h,$m) && !preg_match("/function\(h\,u\,n\,t\,e\,r\)/",$h)) {
  $t1=explode("window.atob('",$h);
  $t2=explode("'",$t1[1]);
  $filelink=base64_decode($t2[0]);
  //echo $filelink;
  } elseif (preg_match("/function\(h\,u\,n\,t\,e\,r\)/",$h)) { // vipstream.php
  //echo $h;
  function hunter($h, $u, $n, $t, $e, $r) {
    $r = "";
    for($i = 0; $i < strlen($h);$i++) {
        $s = "";
        while($h[$i] !== $n[$e]) {
            $s .= $h[$i];
            $i++;
        }
        for($j = 0; $j < strlen($n);$j++) {
          $s=str_replace($n[$j],$j,$s);
        }
        $r .= chr(abc($s, $e, 10) - $t);
    }
    return $r;
  }
  function abc($d, $e, $f) {
    $g = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $h = substr($g,0, $e);
    $i = substr($g,0, $f);
    $x=strrev($d);
    $a=0;
    $j=0;
    for ($m=0;$m<strlen($x);$m++) {
      $j +=strpos($h,$x[$m])*pow($e,$m);
    }
    $k = '';
    while($j > 0) {
        $k = $i[$j % $f].$k;
        $j = ($j - ($j % $f)) / $f;
    }
    return $k;
  }
  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);
  //print_r ($m);
  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .=hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  //$out=str_replace("\\","",$out);
  //echo $out;
  //$t1=explode('setup(',$out);
  /*
  $t1=explode('new Playerjs(',$out);
  $t2=explode(');',$t1[1]);
  echo $t2[0];
  $xx=json_decode($t2[0],1);
  print_r ($xx);
  if (preg_match("/src\=\"([^\"]+)\"\s+kind\=\"captions\"/",$out,$s))
   $srt=$s[1];

  if (isset($xx['playlist'][0]['sources'][0]['file']))
   $link=$xx['playlist'][0]['sources'][0]['file'];
  //echo $link;
////////////////////////////////////////////////////////////////////
  $ss=$xx['playlist'][0]['tracks'];
  for ($k=0;$k<count($ss);$k++) {
   if (preg_match("/romanian/i",$ss[$k]['label'])) {
     $srt=$ss[$k]['file'];
     break;
   } elseif (preg_match("/english/i",$ss[$k]['label'])) {
     $srt=$ss[$k]['file'];
   }
  }
  */
  if (preg_match("/file\:\"([^\"]+)\"/",$out,$m))
   $link=$m[1];
  $sub=explode(",",$out);
  $s=preg_grep("/\[(english|romanian).*\](http[^\[]+\.vtt)/i",$sub);
  $srt="";
  //echo $out;
  foreach($s as $v) {
   if (preg_match("/\[romanian.*\](http.+)/i",$v,$ss)) {
    $srt=$ss[1];
    break;
   } elseif (preg_match("/\[english.*\](http.+)/i",$v,$ss)) $srt=$ss[1];
  }
  }
  //echo $link."\n".$srt;
}
if (preg_match("/streamembed\./",$filelink)) {
  //echo $filelink;
  //die();
  // https://imwatchingmovies.com/play/WUZoSklLYnRKZzlidnlLdXAyVU4xaTd1alV0YUFlQzhTWUZlQk93YXIxY3hlT2EzTnI1QkU0d09HaXpqemhvPQ==
  //$filelink="https://streamembed.net/playvideo.php?video_id=YUZwTElLYTRjUW9Pc1NhdTkyZFlpeTd1ajBwUkJ1TzJTWUZlQk8wYXIxMHhldUczTmJaQkVJd09HU3JxeXhnPQ==";
  //$filelink="https://streamembed.net/playvideo.php?video_id=YUYxTUlLYTRjbHhZdVNIMC9XRmVpaTd1ajBwUUFlVzJTWUZlQk8wYXIxMHhldUczTmJaQkZvME9HeTN2eWhzPQ==";
  // 'User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0',
  if (preg_match("/server\_id\=/",$filelink)) { // from embedo.xyz
   $ua = $_SERVER['HTTP_USER_AGENT'];
   $head=array('User-Agent: '.$ua,
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://streamembed.net',
   'Alt-Used: streamembed.net',
   'Connection: keep-alive',
   'Referer: https://streamembed.net',
   'Upgrade-Insecure-Requests: 1',
   'Sec-Fetch-Dest: document',
   'Sec-Fetch-Mode: navigate',
   'Sec-Fetch-Site: none',
   'Sec-Fetch-User: ?1');
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (!preg_match("/value\=\"([^\"]+)\"\s+name\=\"captcha_id/",$h,$m)) {
  $t1=explode('<iframe',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $filelink=$t3[0];
  } else {
   if (file_exists($base_pass."streamembed.txt")) {
    $l=trim(file_get_contents($base_pass."streamembed.txt"));
    $l=$l."embedo1.php?file=".urlencode($filelink);
    //echo $l;
    $cookie="078741a200cc28871c1a9e5c6286a9dd";
    // Cookie: __test=452387fd832ec6e6833f14a2cfea376a
    //$cookie="452387fd832ec6e6833f14a2cfea376a";
    $head=array('Cookie: __test='.$cookie);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    //if (preg_match("/document\.cookie\=\"\_test\=/",$h)) {
    if(preg_match_all('/toNumbers\(\"(\w+)\"/',$h,$m)) {
     require_once( 'cryptoHelpers.php');
     require_once( 'aes_small.php');
     //$t1=explode('document.cookie="_test=',$h);
     //$t2=explode(' ',$t1[1]);
     //$cookie=$t2[0];
     //print_r ($m);
     $a=cryptoHelpers::toNumbers($m[1][0]);
     $b=cryptoHelpers::toNumbers($m[1][1]);
     $c=cryptoHelpers::toNumbers($m[1][2]);
     $d=AES::decrypt($c,16,2,$a,16,$b);
     //print_r ($m);
     $cookie=cryptoHelpers::toHex($d);
     $head=array('Cookie: __test='.$cookie);
     //print_r ($head);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_ENCODING, "");
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
   }
   $filelink=$h;
  }
  }
  //echo $filelink;
  } else {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head=array('User-Agent: '.$ua,
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Upgrade-Insecure-Requests: 1',
  'Sec-Fetch-Dest: document',
  'Sec-Fetch-Mode: navigate',
  'Sec-Fetch-Site: none',
  'Sec-Fetch-User: ?1');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch,CURLOPT_REFERER,"https://seapi.link");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (!preg_match("/value\=\"([^\"]+)\"\s+name\=\"captcha_id/",$h,$m)) {
  $t1=explode("window.atob('",$h);
  $t2=explode("'",$t1[1]);
  $filelink=base64_decode($t2[0]);
  //echo $filelink;
  } else {
   if (file_exists($base_pass."streamembed.txt")) {
    $l=trim(file_get_contents($base_pass."streamembed.txt"));
    $l=$l."seapi1.php?file=".urlencode($filelink);
    //echo $l;
    $cookie="078741a200cc28871c1a9e5c6286a9dd";
    // Cookie: __test=452387fd832ec6e6833f14a2cfea376a
    //$cookie="452387fd832ec6e6833f14a2cfea376a";
    $head=array('Cookie: __test='.$cookie);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    //if (preg_match("/document\.cookie\=\"\_test\=/",$h)) {
    if(preg_match_all('/toNumbers\(\"(\w+)\"/',$h,$m)) {
     require_once( 'cryptoHelpers.php');
     require_once( 'aes_small.php');
     //$t1=explode('document.cookie="_test=',$h);
     //$t2=explode(' ',$t1[1]);
     //$cookie=$t2[0];
     //print_r ($m);
     $a=cryptoHelpers::toNumbers($m[1][0]);
     $b=cryptoHelpers::toNumbers($m[1][1]);
     $c=cryptoHelpers::toNumbers($m[1][2]);
     $d=AES::decrypt($c,16,2,$a,16,$b);
     //print_r ($m);
     $cookie=cryptoHelpers::toHex($d);
     $head=array('Cookie: __test='.$cookie);
     //print_r ($head);
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_ENCODING, "");
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
   }
   $filelink=$h;
  }
  }
  }
  //echo $filelink;
  //$filelink="https://dood.pm/e/77aspxz15g4m/";
  //die();
}
if (preg_match("/embedo\.xyz/",$filelink)) {
  //echo $filelink;
  // https://embedo.xyz/play/movie.php?imdb=tt5884796
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Alt-Used: embedo.xyz',
  'Connection: keep-alive',
  'Upgrade-Insecure-Requests: 1',
  'Sec-Fetch-Dest: document',
  'Sec-Fetch-Mode: navigate',
  'Sec-Fetch-Site: none',
  'Sec-Fetch-User: ?1');
  //die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/sub\.php?[^\"]+/",$h,$s))
    $srt="https://embedo.xyz/play/".$m[0];
  if (preg_match("/source\s+src\=\"([^\"]+)\"/",$h,$m)) {
  /*
  $filelink="https://embedo.xyz/play/".$m[1];
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location\:\s*(.+)/i",$h,$m)) {
    $link=trim($m[1]);
  */
  $link=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
    $link=get_max_res($h,$link);
  //}
  }
}
if (preg_match("/onemagia\.com/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $host=parse_url($filelink)['host'];
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive');
  //echo $link;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match("/tubeup\.xyz/",$h)) {
    if (preg_match("/http.+\.vtt/",$h,$s))
     $srt=$s[0];
     //echo $srt;
    $t1=explode("https://tubeup.xyz/efiles",$h);
    $t2=explode('.mp4',$t1[1]);
    $link="https://tubeup.xyz/efiles".$t2[0].".mp4";
    //echo $link;
    $filelink=$link;
  } elseif (preg_match("/\<iframe class\=\"responsive-embed-item\" src\=\"([^\"]+)\"/",$h,$m)) {
    $filelink=$m[1];
    //echo $filelink;
    // https://redload.co/e/0e97z5y2tima/Bullet.Train.2022.mp4
  } elseif (preg_match("/source\s+src\=\"([^\"]+)\"/",$h,$m)) {
    $link=$m[1];
    if (preg_match("/http.+\.vtt/",$h,$s))
     $srt=$s[0];
  }
}
if (!$pg) $pg = "play now...";
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

//$flash="chrome";
  $filelink=str_replace("hd=1","hd=2",$filelink);
  $filelink=str_replace("hd=3","hd=2",$filelink);
//echo $filelink;
if (strpos($filelink,"is.gd") !==false) {
 $a = @get_headers($filelink);
 //print_r ($a);
 $l=$a[6];
 $a1=explode("Location:",$l);
 $filelink=trim($a1[1]);
}
if (strpos($filelink,"moovie.cc") !== false) {
 $a = @get_headers($filelink);
 $l=$a[10];
 $a1=explode("Location:",$l);
$filelink=trim($a1[1]);
}
//echo $link;
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/ms',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/msi',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
function str_prep($string){
  $string = str_replace(' ','%20',$string);
  $string = str_replace('[','%5B',$string);
  $string = str_replace(']','%5D',$string);
  $string = str_replace('%3A',':',$string);
  $string = str_replace('%2F','/',$string);
  $string = str_replace('#038;','',$string);
  $string = str_replace('&amp;','&',$string);
  return $string;
}
//peteava
function r() {
$i=mt_rand(4096,0xffff);
$j=mt_rand(4096,0xffff);
return  dechex($i).dechex($j);
}
function zeroFill($a,$b) {
    if ($a >= 0) {
        return bindec(decbin($a>>$b)); //simply right shift for positive number
    }
    $bin = decbin($a>>$b);
    $bin = substr($bin, $b); // zero fill on the left side
    $o = bindec($bin);
    return $o;
}
function crunch($arg1,$arg2) {
  $local4 = strlen($arg2);
  while ($local5 < $local4) {
   $local3 = ord(substr($arg2,$local5));
   $arg1=$arg1^$local3;
   $local3=$local3%32;
   $arg1 = ((($arg1 << $local3) & 0xFFFFFFFF) | zeroFill($arg1,(32 - $local3)));
   $local5++;
  }
  return $arg1;
}
function peteava($movie) {
  $seedfile=file_get_contents("http://content.peteava.ro/seed/seed.txt");
  $t1=explode("=",$seedfile);
  $seed=$t1[1];
  if ($seed == "") {
     return "";
  }
  $r=r();
  $s = hexdec($seed);
  $local3 = crunch($s,$movie);
  $local3 = crunch($local3,"0");
  $local3 = crunch($local3,$r);
  return strtolower(dechex($local3)).$r;
}
/** end peteava **/
include ("youtube.php");

//***************Here we start**************************************
$filelink=str_prep($filelink);
//echo $filelink;
//die();
$host_filelink=parse_url($filelink)['host'];
if (strpos($filelink,".googlevideo.com") !== false) {
  $link=$filelink;
} elseif (preg_match("/olgply\./",$filelink)) {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Alt-Used: olgply.xyz',
'Connection: keep-alive',
'Referer: https://olgply.com/',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: iframe',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: cross-site');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  $h = curl_exec($ch);
  curl_close($ch);
  $h = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $h);
  //echo $h;
  //die();
  preg_match("/var (_0x\w+)\=\[([^\]]+)/",$h,$m);
  //print_r ($m);
  $c=array();
  $code="\$c=[".$m[2]."];";
  eval ($code);
  for ($k=0;$k<count($c);$k++) {
   if (substr($c[$k], 0, 4)=="http") {
    $id=$k;
    break;
    }
   }
  $cc=count($c);
  //die();
  //print_r ($c);
  $t1=explode("var hls=new Hls();hls[",$h);
  $t2=explode("](",$t1[1]);
  $t3=explode("),",$t2[1]);
  $xx=$t3[0];
  preg_match("/(_0x\w+)\((0x[a-f0-9]+)\)/",$xx,$m);

  $id=$m[2]-$id;
  $xx=preg_replace_callback("/(_0x\w+)\((0x[a-f0-9]+)\)/",
   function($m) {
    global $id;
    global $cc;
    if (($m[2]-$id)<0)
    return $m[1]."[".($m[2]-$id+$cc)."]";
    elseif (($m[2]-$id)<$cc)
    return $m[1]."[".($m[2]-$id)."]";
    else
    return $m[1]."[".($m[2]-$id-$cc)."]";
   }
  ,
  $xx);
  //echo $xx;
  preg_match("/_0x\w+/",$xx,$m);
  $xx=str_replace($m[0],"\$c",$xx);
  $xx=str_replace("(","[",$xx);
  $xx=str_replace(")","]",$xx);
  $code="\$xx=".$xx.";";
  $code=str_replace("+",".",$code);
  //echo $code;
  eval ($code);
  //echo $xx;
  if (substr($xx, 0, 4)=="http") {
   $link=$xx;
  }
  if ($flash <> "flash")
   $link .="|Referer=".urlencode("https://olgply.xyz")."&Origin=".urlencode("https://olgply.xyz");
  $filelink="";
} elseif (preg_match("/streamdav\.com/",$filelink)) {
  //echo $filelink;
  // http://streamdav.com/e/kctFKdlCXV99
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://www.filmeserialeonline.org',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/source src\=\"([^\"]+)\"/",$h,$m))
    $link=html_entity_decode($m[1]);
  if (preg_match("/captions\" src\=\"([^\"]+)\"/",$h,$s))
    $srt=html_entity_decode($s[1]);
} elseif (preg_match($vidguard,$filelink)) {
  //https://fslinks.org/e/gqM1VOPXwMOo4kY?&c1_file=http://filmeserialeonline.org/srt/tt5264838.srt&c1_label=RO&c2_file=http://filmeserialeonline.org/srt/tt5264838_EN.srt&c2_label=EN
  //echo $filelink;
  //https://i.guardstorage.net/subtitles/3xwFGsfv8N20M6ik4VE.vtt
  require_once("AADecoder1.php");
  function decode_code1($code){
    return preg_replace_callback(
        "@\\\\(u)([0-9a-fA-F]{4})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'UTF-8');
        },
        $code
    );
  }
  function kk($p,$q,$s) {   // from /assets/js/main.js?id=
   $u="";
   for ($v=0;$v<strlen($p);$v +=2) {
    $u .=chr(intval(substr($p,$v,2),16) ^ $q);
   }
   return $u;
  }
  function  getTechName($e) {  // from /assets/videojs/video.min.js?id=
   $t=array();
   for ($i=strlen($e)-1,$n=0;0<=$i;$i--,$n++) {
    $t[$n]=$e[$i];
   }
   for ($i=0;$i<count($t)-1;$i +=2) {
     $r=[$t[$i+1],$t[$i]];
     $t[$i]=$r[0];
     $t[$i+1]=$r[1];
   }
  return implode($t,"");
  }
  $host="https://".parse_url($filelink)['host'];
  parse_str(parse_url($filelink)['query'],$s);
  //print_r ($s);
  if (key($s)) $srt=$s[key($s)];
  //die();
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$filelink,
  'Origin: '.$host,
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //$h=str_replace("\u0022",'"',$h);
  //echo $h;
  $h=decode_code1($h);
  if (preg_match("/\w+\.vtt/",$h,$m))
    $srt="https://i.guardstorage.net/subtitles/".$m[0];
  //echo $srt;
  //if (preg_match("/videojs\/ad\/plugin\.js\?id\=\w+/",$h)) {
  /*
  $t1=explode('videojs/ad/plugin.js?id=',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $l=$host."/assets/videojs/ad/plugin.js?id=".$id;

  curl_setopt($ch, CURLOPT_URL,$l);
  $h1 = curl_exec($ch);
  */
  //echo $h;
  $h=str_replace(" ","",$h);
  $h=str_replace("\/","/",$h);
  $h=str_replace("'\\\\\\\\'","'\\\\'",$h);
  $h=str_replace("'\\\\\"'","'\\\"'",$h);
  //echo $h;
  $jsu=new AADecoder();
  $out = $jsu->decode($h);
  //echo $out;
  $t1=explode('window.svg=',$out);
  $t2=explode(';")',$t1[1]);
  $rest=$t2[0];
  //$rest = substr($t1[1], 0, -1);
  $r=json_decode($rest,1);
  //print_r ($r);
  /*
  $q=array();
  for ($k=0;$k<count($r['stream']);$k++) {
   $q[$r['stream'][$k]['Label']]=$r['stream'][$k]['URL'];
  }
  if (isset($q['1080p']))
   $l=$q['1080p'];
  elseif (isset($q['720p']))
   $l=$q['720p'];
  elseif (isset($q['480p']))
   $l=$q['480p'];
  elseif (isset($q['360p']))
   $l=$q['360p'];
  elseif (isset($q['auto']))
   $l=$q['auto'];
  */
  if (isset($r['stream'][0]['URL'])) {
  $q=array();
  for ($k=0;$k<count($r['stream']);$k++) {
   $q[$r['stream'][$k]['Label']]=$r['stream'][$k]['URL'];
  }
  if (isset($q['1080p']))
   $l=$q['1080p'];
  elseif (isset($q['720p']))
   $l=$q['720p'];
  elseif (isset($q['480p']))
   $l=$q['480p'];
  elseif (isset($q['360p']))
   $l=$q['360p'];
  elseif (isset($q['auto']))
   $l=$q['auto'];
  } else {
  $l=$r['stream'];
  }
  $t1=explode("sig=",$l);
  $t2=explode("&",$t1[1]);
  $token=$t2[0];
  $token1=substr(base64_decode(kk($token,2,16)),5);
  $x=substr($token1,0,strlen($token1)-5);
  $token2=getTechName($x);
  $link=str_replace($token,$token2,$l);
  //$link=$l;
  if (!preg_match("/https\:\/\//",$link)) $link=str_replace("https:/","https://",$link);
  //}
  if ($link && preg_match("/\.m3u8/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  }
  if ($link && $flash <> "flash")
  $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  //}
} elseif (preg_match("/vidhidevip|vidhidepre\./",$filelink)) {
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $srt="";
  if (preg_match_all("/file:\s*\"([^\"]+)\",\s*label:\s*\"(Romanian|English)/i",$h,$m)) {
   for ($k=0;$k<count($m[2]);$k++) {
    if (preg_match("/romanian/i",$m[2][$k])) {
      $srt=$m[1][$k];
      break;
    }
   }
  if (!$srt) $srt=$m[1][0];
  }
  //echo $srt;
  if (preg_match("/sources:\s*\[\{file\:\"([^\"]+)\"/",$h,$m)) {
   $link=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }

   
} elseif (preg_match("/luluvdo\.com/",$filelink)) {
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, "https://luluvdo.com");
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //$out .=$h;
  if (preg_match("/file:\"([^\"]+)\"/",$out,$m)) {
   $link=$m[1];
   //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, "https://luluvdo.com");
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0";
  if ($link && $flash <> "flash")
    $link .="|Referer=".urlencode($host."/")."&Origin=".urlencode($host)."&User-Agent=".urlencode($ua);
  }
} elseif (preg_match("/upload\.do/",$filelink)) {
  //https://upload.do/embed-oql9w36hdhwx.html
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, "https://upmovies.to");
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  //echo $out;
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  //sources:[{src:"
  if (preg_match('/sources\:\s*\[\{src\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
} elseif (preg_match("/vtbe\.to/",$filelink)) {
  //https://vtbe.to/embed-m2zrnw8a3jta.html
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_REFERER, "https://upmovies.to");
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  //echo $out;
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  //sources:[{src:"
  if (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
} elseif (preg_match("/dropload\.io/",$filelink)) {
//echo $filelink;
  //https://dropload.io/e/ogx6yri42g2c
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  //echo $out;
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  //sources:[{src:"
  if (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
} elseif (preg_match("/streamvid\.net|lylxan\.com/",$filelink)) {
//echo $filelink;
  $host="https://".parse_url($filelink)['host'];

  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  //sources:[{src:"
  //sources:[{file:"
  //echo $out;
  if (preg_match('/sources\:\s*\[\{src\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } elseif (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
} elseif (preg_match("/swhoi\.|streamhide\.|movhide\.pro|filelions\.to|streamwish\.to|guccihide\.com|lonfils\.xyz|uqloads\.xyz/",$filelink)) {
//echo $filelink;
  //https://uqloads.xyz/e/y1i43ovz9r2y
  $host="https://".parse_url($filelink)['host'];
  $head=array('Referer: https://stream.2embed.cc',
  'Origin: https://stream.2embed.cc');
  $head=array('Referer: https://streamsrcs.2embed.cc/');
  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  $out .=$h;
  //echo $out;
  //die();
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  if (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
  if ($link && $flash <> "flash")  {
   $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
  //."&User-Agent=".urlencode($ua);
} elseif (preg_match("/wishfast\./",$filelink)) {
  $host="https://".parse_url($filelink)['host'];
  $head=array('Referer: https://2embed.cc',
  'Origin: https://2embed.cc');
  $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $out = curl_exec($ch);
  curl_close($ch);
  //echo $out;
  if (preg_match("/file\:\"([^\"]+)\",label\:\"\w+\"\,kind\:\"captions\"/",$out,$m))
   $srt=$m[1];
  //sources:[{file:"
  if (preg_match('/sources\:\s*\[\{file\:\"([^\"]+)\"/', $out, $m)) {
   $link=$m[1];
  } else
   $link="";
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
  if ($link && $flash <> "flash")  {
   $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
} elseif (preg_match("/vtplay\./",$filelink)) {
  // https://vtplay.net/embed-g1jc7lv7aoaw.html
  //$filelink="https://vtube.to/embed-nqt5gqcbg124.html";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidcloud9.org/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/<iframe/",$h)) {
    $t1=explode("<iframe",$h);
    $t2=explode('src="',$t1[1]);
    $t3=explode('"',$t2[1]);
    $l=$t3[0];
    //echo $l."\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
  }
  //die();
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $h = $jsu->Unpack($h);
  //echo $h;
  if (preg_match("/sources\:\[\{file\:\"([^\"]+)\"/",$h,$m)) {
    $link=$m[1];
  }
} elseif (preg_match("/kembed\./",$filelink)) {
//echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://fastmovies.to/',
  'Cookie: PHPSESSID=');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/var\s+master\s*\=\s*\"([^\"]+)\"/",$h,$m))
   $link=$m[1];
  if (preg_match("/vtt\.php\?url\=([^\"]+)/",$h,$m))
   $srt=str_replace("\\","",$m[1]);
  //echo $srt;
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $ref="kembed.net";
  $link=get_max_res($h,$link);

  }
} elseif (strpos($filelink,"storage.googleapis.com") !== false) {
  $link=$filelink;
} elseif (preg_match("/master\.m3u8/",$filelink)) { // from cartoonhd (https://rbfq1m.svid.li)
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $l=$filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  // get max res
$base1=str_replace(strrchr($l, "/"),"/",$l);
$base2=getSiteHost($l);
if (preg_match("/\.m3u8/",$h)) {
$a1=explode("\n",$h);
for ($k=0;$k<count($a1);$k++) {
  if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
}
if ($pl[0][0] == "/")
  $base=$base2;
elseif (preg_match("/http(s)?:/",$pl[0]))
  $base="";
else
  $base=$base1;
if (count($pl) > 1) {
  if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
    preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
  else
    preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $link=$base.$pl[$key_max];
}
} else {
  $link=$l;
}
} elseif (strpos($filelink,"ourmovie.net") !== false) {
  $link=$filelink;
  $filelink="";
//} elseif (strpos($filelink,"embed4free.com") !== false) {
} elseif (preg_match("/embed4free\.\w+|gdrvplayer\.com|gdrivestream\.com/",$filelink)) {
  require_once("JavaScriptUnpacker1.php");
  $host=parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:96.0) Gecko/20100101 Firefox/96.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://moviesnipipay.me/");
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $h=JavaScriptUnpacker1::unpack($html);
  
  $r=explode(",",$h);
  //print_r ($r);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  if ($host == "embed4free.com") {
  if (preg_match("/(\/\/embed4free\.\w+\/api\/.*?)\,/",$h,$m)) {
  $l="https:".$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);

  $r=json_decode($h,1);
  //print_r ($r);
  $link="https:".$r['sources'][count($r['sources'])-1]['file'];
  $srt=$r['tracks'][0]['file'];
  }
  } elseif ($host == "gdrivestream.com") {
  if (preg_match("/(\/\/gdrivestream\.com\/api\/.*?)\,/",$h,$m)) {
  $l="https:".$m[1];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $link="https:".$r['sources'][count($r['sources'])-1]['file'];
  $srt=$r['tracks'][0]['file'];
  }
  } elseif ($host == "gdrvplayer.com") {
  if (preg_match("/(\/\/gdrvplayer\.com\/api\/.*?)\,/",$h,$m)) {
  $l="https:".$m[1];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $link="https:".$r['sources'][count($r['sources'])-1]['file'];
  $srt=$r['tracks'][0]['file'];
  }
  }
   //echo $srt;
} elseif (preg_match("/o2tvseries\./",$filelink)) {
  $t1=explode("?",$filelink);
  $filelink=$t1[0];
  $post=$t1[1];
  $post .="&token=0.24XGoVgyCdMVaHxxCCgs9r06z0BaPgUaXLKEoTD50IiE1lJx3DYO6ot7K6vMFqSUdGNVcU1JSs87DEmMun2aZvuz6Dq1nwtRXq9veSf3VMH90pyiY0svbm3vfhnC_3MF3w5M29LKTeSdbd2a8RqbsxbrRvtf4o1W3_Maf2rubE_Mxhqno78s3hStLq76r5fmKVjRJ-Tp7oQXyUjnBxhZWX30ylxcItiLDL0M8jrtjc5q6_3OxYEbLT75-0EN5STYKHFZ-2JIlFPlFdHh4hCS7lFU_3eC7gO4RyXhdpZkNgmx0vkPBn4B5qj9VXcs3ikGq3_Zjs4Ab4HhQI6ZeKygFPgM-LDpu7nApa88d8u-QRb_B6Q--Q-8IwqPGSBmRYfliTd6egjx2Vw_QEgFWcz2BG8xZoJxQf19QqxhGKQ6iO5Ie29ZuUgbhEXhvaONlHck.e0LvY1sI45hfIUzfSRdVqQ.09874829b5405af143e71643e082abc8c9a2fac15ef6c6cde7d29f93badb18fc";
//echo $post;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:101.0) Gecko/20100101 Firefox/101.0";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: https://o2tvseries.co',
  'Alt-Used: o2tvseries.co',
  'Connection: keep-alive',
  'Referer: https://o2tvseries.co');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
} elseif (preg_match("/bestx\.stream|moviesapi\.club|watchx\.php/",$filelink)) {
  // $l="https://bestx.stream/v/D6Rj6gBwm42V/";
  //https://embed.smashystream.com/watchx.php?tmdb=447365 ???/
  //https://w1.moviesapi.club
  //echo $filelink;
  //$filelink="https://moviesapi.club/movie/958196";
    function def($d, $e, $f) {
        $x="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/";
        $h=substr($x,0,$e);

        $i=substr($x,0,$f);

        $d=strrev($d);
        $a=0;
        for ($z=0;$z<strlen($d);$z++) {
         $b=$d[$z];
         if (strpos($h,$b) !== false) $a +=strpos($h,$b) * pow($e,$z);
        }
        $k = "";
        $j=$a;
        while ($j > 0) {
            $k = $i[$j % $f] . $k;
            $j = ($j - ($j % $f)) / $f;
        }
        return $k;
    }
    function player($p, $l, $a, $y, $e, $r) {
        $r = "";
        $len = strlen($p);
        for ($i = 0;  $i < $len; $i++) {
            $s = "";
            while ($p[$i] !== $a[$e]) {
                $s .= $p[$i];
                $i++;
            }
            for ($j = 0; $j < strlen($a); $j++) {
              $s=str_replace($a[$j],$j,$s);
            }

            $r .= chr(def($s, $e, 10) - $y);
        }
        return $r;
    };
  $host="https://".parse_url($filelink)['host'];

  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$host,
  'Origin: '.$host,
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/class\=\"vidframe\"\s+src\=\"([^\"]+)\"/",$h,$m)) {   //https://moviesapi.club/movie/447365
  curl_setopt($ch, CURLOPT_URL,$m[1]);
  $h = curl_exec($ch);
  }
  curl_close($ch);
  //echo $m[1];
  //echo $h;
  //preg_match("/\=\s+\'([^\']+)\'/",$h,$m);
  //print_r ($m);
  //echo $h."\n";
  //MasterJS = '{"
  //if (preg_match("/MasterJS\s*\=\s*[\'\"]([^\'\"]+)[\'\"]/i",$h,$m)) {
  if (preg_match("/\=\s+\'([^\']+)\'/",$h,$m)) {
  $enc=$m[1];
  //echo $enc;
  //$t1=explode("MasterJS = '",$h);
  //$t2=explode("';</",$t1[1]);
  //$enc=$t2[0];
  //$x=json_decode($enc,1);
  //echo base64_decode($x['ct']);
  require_once("cryptoJsAesDecrypt.php");
  $js=new cryptoJsAesDecrypt();
  //$pass="4VqE3#N7zt&HEP^a";
  $pass="11x&W5UBrcqn\$9Yl";
  //$pass="m4H6D9%0\$N&F6rQ&";
  $pass="2ihHoN6ZmSq3XeOy";
  $pass="sZX7Rhncw6mlbL8j";
  $pass="233KvCBGckBuCn";
///////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
  $out="";
  if (preg_match("/eval\(_0x[a-z0-9]+\(([^\)]+)\)/",$h,$n)) {
    $x=trim(str_replace('"',"",$n[1]));
    $z=explode(",",$x);

    $mm=player($z[0],$z[1],$z[2],$z[3],$z[4],$z[5]);
    //echo $mm;
    // (function () { return eval(CryptoJSAesJson.decrypt(JScript, 'tSIsE8FgpRkv3QQQ')); })()
    if (preg_match("/\'([^\']+)\'/",$mm,$m))
      $pass=$m[1];
    else
      $pass="";
    //print_r ($m);
  $out = $js->decrypt1($pass,$enc);
  //echo $out;
  } elseif (preg_match("/\}\}\}\(_0x\w+,([^\)]+)\)\,/",$h,$m)) {
  $a="\$b=".$m[1].";";
  eval ($a);
  preg_match("/var _0x\w+\=\[([^\]]+)\]/",$h,$m);
  $a="\$c=array(".$m[1].");";
  eval ($a);
    for ($k = 0; $k < $b; $k++) {
      array_push($c, array_shift($c));
    }
  preg_match("/\'\w+\':_0x\w+\((\w+)\)\+_0x\w+\((\w+)\)/",$h,$m);
  $cc=count($c);
  //print_r ($c);
  for ($k=0;$k<$cc;$k++) {
    //if (strlen($c[($m[1]+$k) % $cc].$c[($m[2]+$k) % $cc])==15)
    $p[]=$c[($m[1]+$k) % $cc].$c[($m[2]+$k) % $cc];
  }
  //print_r ($p);
  $out="";
  for ($k=0;$k<count($p);$k++) {
  $pass=$p[$k];

  $out .= $js->decrypt1($pass,$enc);
  }
  }
  //echo $out;

  //$pass="tSIsE8FgpRkv3QQQ";
///////////////////////////////////////////////////////////

  $link="";
  $srt="";
  $srt1=array();
  if (preg_match("/sources\:\s*\[\{\"file\"\:\"([^\"]+)\"/",$out,$m))
   $link=$m[1];
  if (preg_match_all("/file\"\:\"([^\"]+)\"\,\"label\"\:\"(English|Romanian)/i",$out,$s)) {
    for ($k=0;$k<count($s[2]);$k++) {
     if (preg_match("/English/i",$s[2][$k]))
      if (!isset($srt1["English"])) $srt1["English"]=$s[1][$k];
     if (preg_match("/Romanian/i",$s[2][$k]))
      if (!isset($srt1["Romanian"])) $srt1["Romanian"]=$s[1][$k];
    }
    if (isset($srt1["Romanian"]))
     $srt=$srt1["Romanian"];
    elseif (isset($srt1["English"]))
     $srt=$srt1["English"];
  }
  if ($link && $flash <> "flash") {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$host,
  'Origin: '.$host,
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $link."\n".$h."\n";
  $link=get_max_res($h,$link);
  //echo $link;
  //die();
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0";
  $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host)."&User-Agent=".urlencode($ua);;
  //echo $link;
  }
  }
} elseif (preg_match("/dulu\.to/",$filelink)) {
  function cryptoJsAesDecrypt($passphrase, $jsonString){
    $jsondata = json_decode($jsonString, true);
    try {
        $salt = hex2bin($jsondata["s"]);
        $iv  = hex2bin($jsondata["iv"]);
    } catch(Exception $e) { return null; }
    $ct = base64_decode($jsondata["ct"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
  }

  $host="https://".parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:101.0) Gecko/20100101 Firefox/101.0";
  //$l="https://server1.dulu.to/v/APTTduUx9XEa/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("MasterJS = '",$h);
  $t2=explode("';</script",$t1[1]);
  $x=$t2[0];
  $p=json_decode($x,1);
  $pass="XOmurdOgNnjMwYah";
  $y=cryptoJsAesDecrypt($pass,$x);
  //echo $y;
  if (preg_match_all("/file\"\:\"([^\"]+)\"\,\"label\"\:\"(\w+)\"\,\"kind\"\:\"captions\"/",$y,$m)) {
   $s=array_combine($m[2],$m[1]);
   if (isset($s['Romanian']))
    $srt=$s['Romanian'];
   else if (isset($s['English']))
    $srt=$s['English'];
   else
    $srt="";
  }
  if (preg_match("/\"file\"\:\"([^\"]+)/",$y,$m))
    $link=$m[1];
  else
    $link="";
  if ($link && $flash <> "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  /*
  if (preg_match_all ("/^(?!#).+/m",$h,$m)) {
   print_r ($m);
  }
  */
  $link=get_max_res($h,$link);
  //echo $link."\n";
  //$link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
  //echo $link;
  // curious case of MX Player.......
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  file_put_contents("lava.m3u8",$h);
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  $link .="|Referer=".urlencode($host)."&Origin=".urlencode($host);
  }
} elseif (strpos($filelink,"msmoviesbd.com") !== false) {
  if (file_exists($base_pass."gdtot.txt"))
    $crypt=trim(file_get_contents($base_pass."gdtot.txt"));
  else
    $crypt="";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://msmoviesbd.com");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  */
  $h=file_get_contents($filelink);
  $t1=explode('"https://new.gdtot.com/file/',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $head=array('Cookie: crypt='.$crypt);
  $l="https://new.gdtot.com/file/".$id;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/PHPSESSID\=(\w+)/",$h,$m);
  $ses=trim($m[1]);
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://new.gdtot.com/',
  'Cookie: crypt='.$crypt.'; PHPSESSID='.$ses.';',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $l="https://new.gdtot.com/dld?id=".$id;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  $t1=explode('URL=',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  parse_str(parse_url($l)['query'],$rec);
  $id=$rec['id'];
  $gd=$rec['gd'];
  $l="https://new.gdtot.com/play.php?id=".$id."&gd=".$gd;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  $t1=explode('sources" : {"file":"',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  curl_close($ch);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://new.gdtot.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/location:\s+(.+)/i",$h,$m);
  $link=trim($m[1]);
} elseif (preg_match("/c1ne\.co/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://c1ne.co/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/juicycodes\(/",$h)) {
  $t1=explode('juicycodes(',$h);
  $t2=explode(')',$t1[1]);
  $e=preg_replace('/\"\s*\+\s*\"/',"",$t2[0]);
  $h1=preg_replace('/[^A-Za-z0-9+\\/=]/',"",$e);

  $symbolMap = array_flip(array("`", "%", "-", "+", "*", "$", "!", "_", "^", "="));
  $obfuscated = str_rot13(str_rot13(base64_decode(substr($h1, 0, -3))));
  $ordSalt = substr($h1,-3);
  $t="";
  for ($k=0;$k<strlen($ordSalt);$k++) {
   $t .=ord($ordSalt[$k]) - 100;
  }
  $ordSalt = $t;
  $ordString="";
  for ($k=0;$k<strlen($obfuscated);$k++) {
   $ordString .=$symbolMap[$obfuscated[$k]];
  }
  $splittedOrd=str_split($ordString,4);
  $deobfuscated = "";
  foreach ($splittedOrd as $key => $value) {
   $v = ($value % 1e3) - $ordSalt;
   $deobfuscated .= chr($v);
  }
  $t1=explode('var config =',$deobfuscated);
  $t2=explode('jwplayer.key',$t1[1]);
  $x=json_decode(substr(trim($t2[0]),0,-1),1);
  $r=array();
  if (isset($x['sources'])) {
   for ($k=0;$k<count($x['sources']); $k++) {
   $r[$x['sources'][$k]['label']]=$x['sources'][$k]['file'];
   }
  }
  if (isset($r['1080P']))
    $link=$r['1080P'];
  elseif (isset($r['720P']))
    $link=$r['720P'];
  elseif (isset($r['480P']))
    $link=$r['480P'];
  elseif (isset($r['360P']))
    $link=$r['360P'];
  else
    $link="";
  if ($link && $flash <> "flash")
  $link=$link."|Referer=".urlencode("https://v1.c1ne.co")."&User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0");
  }
} elseif(preg_match("/utbrgebzvhfa\./",$filelink)) {
//echo $filelink;
  function hunter($h, $u, $n, $t, $e, $r) {
    $r = "";
    for($i = 0; $i < strlen($h);$i++) {
        $s = "";
        while($h[$i] !== $n[$e]) {
            $s .= $h[$i];
            $i++;
        }
        for($j = 0; $j < strlen($n);$j++) {
          $s=str_replace($n[$j],$j,$s);
        }
        $r .= chr(abc($s, $e, 10) - $t);
    }
    return $r;
  }
  function abc($d, $e, $f) {
    $g = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $h = substr($g,0, $e);
    $i = substr($g,0, $f);
    $x=strrev($d);
    $a=0;
    $j=0;
    for ($m=0;$m<strlen($x);$m++) {
      $j +=strpos($h,$x[$m])*pow($e,$m);
    }
    $k = '';
    while($j > 0) {
        $k = $i[$j % $f].$k;
        $j = ($j - ($j % $f)) / $f;
    }
    return $k;
  }
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  curl_close($ch) ;
  //echo $h;
  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);

  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .="\n".hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  $out=str_replace("\\","",$out);
  //echo $out;
  if (preg_match("/src\=\"([^\"]+)\"\s+kind\=\"captions\"/",$out,$s))
   $srt=$s[1];

  preg_match("/var\s*res\s*\=\s*(\w+)\.replace\(\"([\w\=]+)\"/",$out,$m);

  $find=$m[1];
  $rep1=$m[2];
  preg_match("/res\.replace\(\"([\w\=]+)\"/",$out,$m);
  $rep2=$m[1];
  $a="/".$find."\s*\=\s*\"([\w\=]+)\"/";
  //echo $out;
  preg_match($a,$out,$m);
  $res=$m[1];
  $res=str_replace($rep1,"",$res);
  $res=str_replace($rep2,"",$res);
  $link=base64_decode($res);

} elseif(preg_match("/tubeload\.co|embedo\.co|redload\./",$filelink)) {
  // http://tubeload.co/e/z1rbloyecxw0/The_Batman_022_hd.mp4
  // https://embedo.co/e/9qrpnmupnbuj/Top_Gun_Maverick_022_kor.mp4
  // https://redload.co/e/0e97z5y2tima/Bullet.Train.2022.mp4
  //echo $filelink;
  $t1=explode("?",$filelink);
  $host=parse_url($t1[0])['host'];
  function hunter($h, $u, $n, $t, $e, $r) {
    $r = "";
    for($i = 0; $i < strlen($h);$i++) {
        $s = "";
        while($h[$i] !== $n[$e]) {
            $s .= $h[$i];
            $i++;
        }
        for($j = 0; $j < strlen($n);$j++) {
          $s=str_replace($n[$j],$j,$s);
        }
        $r .= chr(abc($s, $e, 10) - $t);
    }
    return $r;
  }
  function abc($d, $e, $f) {
    $g = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $h = substr($g,0, $e);
    $i = substr($g,0, $f);
    $x=strrev($d);
    $a=0;
    $j=0;
    for ($m=0;$m<strlen($x);$m++) {
      $j +=strpos($h,$x[$m])*pow($e,$m);
    }
    $k = '';
    while($j > 0) {
        $k = $i[$j % $f].$k;
        $j = ($j - ($j % $f)) / $f;
    }
    return $k;
  }

  if (preg_match("/tubeload/",$host))
  $l="https://tubeload.co/assets/js/main.min.js";
  elseif (preg_match("/embedo/",$host))
  $l="https://embedo.co/assets/js/master.js";
  elseif (preg_match("/redload/",$host))
  $l="https://redload.co/assets/js/main.min.js";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);

  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .="\n".hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  //echo $out."\n"."===================="."\n";
  preg_match("/var\s*res\s*\=\s*(\w+)\.replace\(\"([\w\=]+)\"/",$out,$m);

  $find=$m[1];
  $rep1=$m[2];
  preg_match("/res\.replace\(\"([\w\=]+)\"/",$out,$m);
  $rep2=$m[1];

  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  curl_close($ch);

  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);

  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .="\n".hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  $out=str_replace("\\","",$out);
  if (preg_match("/src\=\"([^\"]+)\" kind\=\"captions/",$out,$s))
    $srt=$s[1];
  $a="/".$find."\s*\=\s*\"([\w\=]+)\"/";
  //echo $out;
  preg_match($a,$out,$m);
  $res=$m[1];
  $res=str_replace($rep1,"",$res);
  $res=str_replace($rep2,"",$res);
  $link=base64_decode($res);
} elseif(preg_match("/highload\.to|upvideo\.to/",$filelink)) {
  // https://highload.to/e/ftmwkj1ab3gp
  function hunter($h, $u, $n, $t, $e, $r) {
    $r = "";
    for($i = 0; $i < strlen($h);$i++) {
        $s = "";
        while($h[$i] !== $n[$e]) {
            $s .= $h[$i];
            $i++;
        }
        for($j = 0; $j < strlen($n);$j++) {
          $s=str_replace($n[$j],$j,$s);
        }
        $r .= chr(abc($s, $e, 10) - $t);
    }
    return $r;
  }
  function abc($d, $e, $f) {
    $g = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $h = substr($g,0, $e);
    $i = substr($g,0, $f);
    $x=strrev($d);
    $a=0;
    $j=0;
    for ($m=0;$m<strlen($x);$m++) {
      $j +=strpos($h,$x[$m])*pow($e,$m);
    }
    $k = '';
    while($j > 0) {
        $k = $i[$j % $f].$k;
        $j = ($j - ($j % $f)) / $f;
    }
    return $k;
  }
  
  $host=parse_url($filelink)['host'];
  if (preg_match("/highload/",$host))
   $l="https://highload.to/assets/js/master.js";
  else
   $l="https://upvideo.to/assets/js/tabber.js";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);

  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .="\n".hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  preg_match("/var\s*res\s*\=\s*(\w+)\.replace\(\"([\w\=]+)\"/",$out,$m);

  $find=$m[1];
  $rep1=$m[2];
  preg_match("/res\.replace\(\"([\w\=]+)\"/",$out,$m);
  $rep2=$m[1];

  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match_all("/decodeURIComponent\(escape\(r\)\)\}\((.*?)\)\)/",$h,$m);

  $out="";
  for ($k=0;$k<count($m[1]);$k++) {
   $c=str_replace('"',"",$m[1][$k]);
   $t1=explode(",",$c);
   $out .="\n".hunter($t1[0],$t1[1],$t1[2],$t1[3],$t1[4],$t1[5]);
  }
  $a="/".$find."\s*\=\s*\"([\w\=]+)\"/";

  preg_match($a,$out,$m);
  $res=$m[1];
  $res=str_replace($rep1,"",$res);
  $res=str_replace($rep2,"",$res);
  $link=base64_decode($res);
} elseif (strpos($filelink,"moviesroot.club") !== false) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("<iframe",$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  /*
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if (!$link) {
   $t1=explode("file: '",$h);
   $t2=explode("'",$t1[1]);
   $link=$t2[0];
  }
  if (!$link) {
   $t1=explode('file:"',$h);
   $t2=explode('"',$t1[1]);
   $link=$t2[0];
  }
  */
  if (preg_match("/[\"\'](http[^\"\']+\.mp4)[\"\']/",$h,$m))
   $link=$m[1];
} elseif (strpos($filelink,"streamhub.") !== false) {
  // https://streamhub.to/e/9s9yqt4qy9yr
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $h .= $jsu->Unpack($h);
  //echo $h;
  //sources: ["
  if (preg_match("/sources\:\s*\[\"([^\"]+)\"/",$h,$m))
    $link=$m[1];
  else {
  $t1=explode('sources:[{src:"',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  }
  //echo $link;
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  */
  if ($link && $flash <> "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
    //$link=$link."|Referer=".urlencode("streamhub.to")."&Origin=".urlencode("streamhub.to");
  }
} elseif (strpos($filelink,"streamacb.") !== false) {
  // https://streamacb.com/embed-4EApT5.html
  if (preg_match("/(embed\-|vid\=)(\w+)/",$filelink,$m))
  $id=$m[2];
  $filelink="https://streamacb.com/sources/?vid=".$id."&s=1";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://streamacb.com/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $server=$x['data']['server'];
  $hash=$x['data']['hash'];
  $y=$x['data']['m3u8'][0]['hls']['segments'];
  $q=$x['data']['m3u8'][0]['label'];
  $out = "#EXTM3U"."\n";
  $out .= "#EXT-X-VERSION:3"."\n";
  $out .="#EXT-X-TARGETDURATION:5"."\n";
  $out .="#EXT-X-PLAYLIST-TYPE:VOD"."\n";
  for($i = 0; $i < count($y); $i++) {
   $out .="#EXTINF:".$y[$i]['inf']['duration'].","."\n";
   //$out .="https://".$DOMAIN_LIST_RD[$i % $numdm_rd]."/rdv".$v."/".$x["quaity"]."/".$user."/".$x['data'][1][$i].".rd"."\n";
   $out .=$server."/uploads/hls/".$hash."/r/".$q."-".$y[$i]['byteRange']['length']."@".$y[$i]['byteRange']['offset'].".txt"."\n";
  }
  $out .="#EXT-X-ENDLIST";
  file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  $link .="|Referer=".urlencode("https://streamacb.com")."&Origin=".urlencode("https://streamacb.com");
  //$link=$link."&User-Agent=".urlencode($ua);
  }
  //print_r ($x);
} elseif (preg_match("/rcp\.vidsrc\.me11/",$filelink)) {
  function deobfstr ($hash,$index) {
  $result = "";
  for ($i=0;$i<strlen($hash);$i +=2) {
   $j=substr($hash,$i,2);
   $result .=chr(hexdec($j) ^ ord($index[($i/2) % strlen($index)]));
  }
  return $result;
  }
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
  // https://vidsrc.me/embed/tt13055982/
  //https://vidsrc.me/embed/tt3291150/
  // https://rcp.vidsrc.me/rcp/NTBjN2QwY2JiNmRiNWM4MTNkNWU3YzA3YmJjYzc2NTg6TVRWR01qbHFaVGMyU3pKWmFtbE9SRlJ0VkhKdk5GUnVMMUp0WnpCMlNrSlJiVVU1TDIxMlpWUTFSRk5CWmtsV2FEQmlWMGhOT0VRMGR6bEJWbFpyZURadVRGbHJPVk5JYjNBeFZrSkRRMlZDWlRGQk5FMTZhSGx0U1d0dFp6SlNibXhVT1dOVlNIVkNhMGhWV2xWV01FRk9XVlJhV2xwRU1sSjRkMXBoUmtnMVpVWlVXalV4VW5KVVJqWmlOVXhpYWtSVFpWVkhhM294TW5KSWVWQjZjMHRTZWpKRmRsWjZjaTl5VUZsMk0wUjNXRFkwT0RGTFJVOUJZaTkxVEcxcFpYbDJLMFl3ZEdSTlIzWktNRE5ZZWtsS09HSlFNalUzUldwdlVDOW1NM2hXWlhoTlJYbHVaM2RoUVd4aFJsaEZUa1o2Ym1vMVVYZERXRk0wUW1RemRuQktieTlUZVdOVWEyRTNiM1pTY214bk5TdEpRM2s1UzJwcE56aFhLMnhhZVZSSGEwcFZhMHRKVVUxMlYxa3dkMEpVVXpWdlZqWlFXVEYyZWl0RFNFRnVURFUzZVdWTFNXc3JaMlZOYjBGVGQwNUhUMVI2TTNWR1QwWm9kVWM1YkVOVFVtUTJlSHBVUVZSMGNuVlZNeTl1THl0U1lURlVORXBQVG1KS2FuWmhZM2xaYzFOdFJqTlJWaXRETUhGTU1uSkZLMDU2VTJ4T1ZqSlpSbGxoYTB0WFFsUkxaVEZQYmpFeFVrNUpNMk01UjA5WlNVNXFkMjR6Y1hGNE9YSkZlWEZVWTNoTlNFTkpNa3BqYUVkeFFrRjJaV05pTWpseVExQlJRWFl2UzBGRU5uUXJZMk4wYVhGYVR6WTBUMWhPYzNKMmMwUkdSVXQ0TVhoSmJHRkpTM000ZEZwbFJYVjBMekJEZW0xTGFsQk1lV0pqZEUxSU16WkhlQzlPTDJaa2JuZEtNbmRJTlVGUmVubzBSRFE1ZFhWelFVZGtjRVJPVGpkRVowSkhSMGt3
  // https://v2.vidsrc.me/srcrcp/NTBjN2QwY2JiNmRiNWM4MTNkNWU3YzA3YmJjYzc2NTg6TVRWR01qbHFaVGMyU3pKWmFtbE9SRlJ0VkhKdk5GUnVMMUp0WnpCMlNrSlJiVVU1TDIxMlpWUTFSRk5CWmtsV2FEQmlWMGhOT0VRMGR6bEJWbFpyZURadVRGbHJPVk5JYjNBeFZrSkRRMlZDWlRGQk5FMTZhSGx0U1d0dFp6SlNibXhVT1dOVlNIVkNhMGhWV2xWV01FRk9XVlJhV2xwRU1sSjRkMXBoUmtnMVpVWlVXalV4VW5KVVJqWmlOVXhpYWtSVFpWVkhhM294TW5KSWVWQjZjMHRTZWpKRmRsWjZjaTl5VUZsMk0wUjNXRFkwT0RGTFJVOUJZaTkxVEcxcFpYbDJLMFl3ZEdSTlIzWktNRE5ZZWtsS09HSlFNalUzUldwdlVDOW1NM2hXWlhoTlJYbHVaM2RoUVd4aFJsaEZUa1o2Ym1vMVVYZERXRk0wUW1RemRuQktieTlUZVdOVWEyRTNiM1pTY214bk5TdEpRM2s1UzJwcE56aFhLMnhhZVZSSGEwcFZhMHRKVVUxMlYxa3dkMEpVVXpWdlZqWlFXVEYyZWl0RFNFRnVURFUzZVdWTFNXc3JaMlZOYjBGVGQwNUhUMVI2TTNWR1QwWm9kVWM1YkVOVFVtUTJlSHBVUVZSMGNuVlZNeTl1THl0U1lURlVORXBQVG1KS2FuWmhZM2xaYzFOdFJqTlJWaXRETUhGTU1uSkZLMDU2VTJ4T1ZqSlpSbGxoYTB0WFFsUkxaVEZQYmpFeFVrNUpNMk01UjA5WlNVNXFkMjR6Y1hGNE9YSkZlWEZVWTNoTlNFTkpNa3BqYUVkeFFrRjJaV05pTWpseVExQlJRWFl2UzBGRU5uUXJZMk4wYVhGYVR6WTBUMWhPYzNKMmMwUkdSVXQ0TVhoSmJHRkpTM000ZEZwbFJYVjBMekJEZW0xTGFsQk1lV0pqZEUxSU16WkhlQzlPTDJaa2JuZEtNbmRJTlVGUmVubzBSRFE1ZFhWelFVZGtjRVJPVGpkRVowSkhSMGt3
  //require_once("rec.php");
  //$key="6LdHhC0kAAAAAOBtLZ5TTZ-84eOVztbnNkpm9I0g";
  //$co="aHR0cHM6Ly9yY3Audmlkc3JjLm1lOjQ0Mw..";
  //echo base64_decode($co);
  //$sa="onSubmit";
  //$sa="";
  //$loc="https://rcp.vidsrc.me";
  //$token=rec($key,$co,$sa,$loc);
  //echo $token;
  //die();
  //$post="g-recaptcha-response=".$token;
  //echo $filelink."\n";
  //$filelink=str_replace("https://rcp.vidsrc.me/rcp/","https://v2.vidsrc.me/srcrcp/",$filelink);
  //echo $filelink."\n";
//https://v2.vidsrc.me/srcrcp/MGVhNzA0MGVmNTBhNWZhNDhhMWNkOGIxNTYwZjNjMjA6U0RkbE0wVlZOM0Y1Y0VFMWVHbHFaa1JDZVd4NmFtNHJjbGR0WVV0M016UjJlVUowY1c0MlZFOHZjMFJNUzI5WlpYWXdlV2hyY0RjdmRuRmpTa3hvWW14V1ZFbzBTRlpJYVVGSmVqaDJiVVEyY1ROeFJrWTVVWFpKY25CTldHNVpTMnRQVHpKd1MxbGhaVmN4VEdwdWFIbGlXVzFoVG5GcFRtZHNlRWxSYUhoalN5dGFTMEUyYlRWUGVtZE1lbE5STTFwSVQyczRXbVZVUVhZdllXaG1hVTF4ZFZFNFRIRkVPVEZWUkZkcFRUaEhRV0ZEYzFKa1RVdEhZa1pOTkZkSWFGSkNRbFJUVjNaTVZUUkNPVzUzVVdSNVltOWlZMUZsWWtjM1NFcHFZVFJQYVZkS2IwczNSbGQyWkVweGMxaHVVM05qWTBGQ0wyUnRXa2RyWVZoeFJYUjRNVWRZY2padlZVTlRaMGM1UlROVVlrUlZNSEJwYjNKVU9Gb3Zhalo1TVhadE56Rk1ORlI0V0hOcVRWTldVa3NyVldSUWJsSXplVll3VVVoNFRYVk5Va2g1ZGpFNFJrZHNVVTFLZEZWU05tcFBaMHd3YlV4WmRVaFlSa0U0V2pWbWMyaEplWEUwVEVwWWFFVTRVMVZ2YlRGVmFXbEZOWGhaVEVoa1ZWaE9OR3A0Y1VseVlUUlVhMnRyTDNsclZFNTRURkJVVkZSMlREbHJja2xRU2xaSFMycG1ZWGt6TjJkbmVYaDZTVEJ5VURSWlpXSnRVelExWjBWTGVsRjVRMUZtTVZreFdFUjVVMkZTVTNORFZEUkdjSEpxTVZVMWJUZHdXa2N3VVV4dk1GQTRjbGsyTjNSQldtaDZTV3d3Y2pseVNEazFSR1pQVWxscU4xbG1UVEIzUkZsUWVEVjZaVzlaYTNkUVFXVXZURGQwWkVRclQzbzJaMWRFTVdKbE5UWTJTRVJEY1V0cWEwMVpjMW94V0d0S1RXSnVTRlpSUXpKa05uRjRTa2RE
//https://vidsrc.stream/prorcp/NWNhYzk4ZDcyNWZlMWJkMGJhNDBiNTY0YjRiY2YzZjE6ZDJGNFFuUlFjR3BRT0ZoclVsRTBSbXR4UkVab2NrMTRSemxUUzBWT01GRjRZVGxWWkZsd1ZXZHBOMkpUTkZVdlIwZGhkVVp2TlRCS2RYSkNNSFJyT0RkNU9TOHphM28yU1c0M1dFTXhNbTlZY1U1NFEzQTBlbmRyWjB4SlJuUnpkbHBIV2l0UlkyaFJPRmR3YjNKWVYwMXdkMDgxZEdSaWJVcHFhRTkwU21kRFdIa3djMDlRV1RJeFpVdFpXaXRNTmtObVpVVktkV05aUTJsMlRWRnNWbEphTkd0ck1tTTJVMmc1U1VoUll6STFhbE5XUjBoV2NHazNjbEkwUkVSNFJrZHRZa2gwVldaNFoxVlJUSGhHY0Uxd1ZtMHlablJ3Tkdwb1UweHJaWEZHVFcxSVYwdFZZekJQUVd4aGVqUTFLMnh3TldwWWMxQjZhMVJzWTNOUVlUVjFPVzVTY0daTVFta3hUa2hLY0hReVQyNTVRV2xWYldoVkwxcHFlWHBJYTBsUVdqUXdTRlozTVZKQk56bDBlWGxMZDFGS05uQnhWalJvYW5GSmRrRTVkMVZ1TmxwVEwwaHNZbTlVYUdWSlFWUkxabXBUTmxSU1pUSlpNU3RXVmsxSVluQlRRM1JxUTFaTk9YSkRhR1pHUXk5eVNrSlZVVk5MUmxORFVsVnFPV2czVm5GSFpHdzBaVzR4YWxKR1FrMW5NbXB6YVVKMmNFNXFNVXhrYWsxMGJHWnBiemszYzBSaGIwMHhPVzluTkRCdWJtcE9aV1ZUUm5kRVdUTlBXRXBVYVV4R1lrVnFiekZtUTBwM1JrSnpNVmczV1RSNU5XdEhPWGRzYTA5VEszWllVM0JtU1VsdGFYQnhhMHRPT0VOSU56Y3dOVlZHTVdSRFRHZHFhbG8wVFhkU1pIYzRkUzl2VGxGdlJqVjRWVlIwTWk5NGFHWmxWV2hXUlRkMU5tOU5aMmxVVTFWeVpVVlVkRVpFZWxWdllUbHlRMGhvTmtSYWRHc3pORWd5
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
//'Content-Type: application/x-www-form-urlencoded',
//'Content-Length: '.strlen($post),
//'Origin: https://rcp.vidsrc.me',
'Connection: keep-alive',
'Referer: '.$filelink);
//print_r ($head);
//$filelink="https://vidsrc.stream/prorcp/ODkzNWM2OThjMTg3ZGE1YTBhOGVlMmExZmE5OWVjYmI6TDJkcWRqZHZOMGhNTTAxWWNraFBWVmx3YUhsS1puWm9XbmxWVTNkTlJscFdiWEZOUVRoR2NXZDFSemw0WW5GV1ZGRXZSRlJXZGtweE5GaEdhRWx5ZVZOM1pIWXZhVGsyT0hWMGFVcFlTbHBLZDJ0UGJIZDVWMk5TU1ZaUFlURnVla2h0YVdZNVlVb3hSRkF2YzFOSWJXRmFjbFJFUzNKRU5rSXhjMnR3U25SYWIyMWxhVXRSWTFKU01tcFBNMjV6WldsdmQyNDBlRU5NT1RSaVpHZDJPVWhtV2xwT1dtVlVOMUk0T0Rac2VVOUhlVE0zVkhSeVRYSk1VWGs0UzBZcmJtWmxiM2xvUTI1UGJHSlFVVVJrWVcxdVlUSjRkVlJGZEZWdlV5OXZZbnBCZWtKbGIyNUpkbVJ4V2xkeWN6ZDRZaTlXZURGaUsycGpUM295YzFkbGNUUnVha2xoUjNoRVVFbExibVUxWkdwVFkwMHZRbEl2VFVKUFZVTlVTa0p4YTBkcGVFbGpjRWdyVjFGS1UyTm5VSE5NZVc5SFZ6WjJjak0wTWs1UlUzbDNiV3BOVUZWeFlWTlpjVmRXYjNsc1ZUVlNTR1l2ZDNwa1kxVk5TV1Z2TUVSdFUyNHdTbnBwTVRRM1prZHBibTlwSzNrM1JFSlBPRTVuY0U4eWNWVTNaM2N2Y0RoaGR6QlJVVWsyUkVoR1NsZ3piRGxSTUV0YWNFeERVR3hGTTA5alR5OWhjbFZ3WmxWMlZIQXpSMnB6YjB0NlNFRnVaRGt2YzFCdmFGaFFkRll3VHpaUFNtcHZaaTlXUkVWdFdVZERLMUJUVkVONlJUbERjV0ZvWkZsT2JrZFhUVmx6VXpJeFZUaEJWMkpITDFOUU1tOVFhR2s0UjJoeFltTlFVa1pSVTA1b2JYbFhjQzlOV0RoeVkzUkRVMFZRY0ZCU1EwbHhlbXBZYTFwbVRtcGpWV2huUjJoTFZ5ODFUbEE0TkVkSllXVkZSbGsyYURoRVRXOUtNRzFVV1dzd2RVOUNLMDl3ZEc1VmIwNWtTVzR3VTBOV2R6MDk-";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://rcp.vidsrc.me/',
  'Origin: https://rcp.vidsrc.me/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POST_FIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  //echo $h;
  //die();
  $t1=explode('data-i="',$h);
  $t2=explode('"',$t1[1]);
  $index=$t2[0];
  $t1=explode('data-h="',$h);
  $t2=explode('"',$t1[1]);
  $hash=$t2[0];
  $x=deobfstr ($hash,$index);

  curl_setopt($ch, CURLOPT_URL, fixurl($x));
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt=$s[1];

/////////////////////////////
  if (preg_match("/file\:\"\#2([^\"]+)\"/",$h,$m)) {
  $x=$m[1];
  //echo $x."\n";
  $x = preg_replace("/[^A-Za-z0-9\+\/\=]/", "",$x);
  $v=array("//KjMkKzg=", "//Nz0hODY=", "//NjYlNiM=", "//MSYxNTg=" ,"//Kj0kOTI="); // from playerjs.js eval(decode(....
  for ($k=0;$k<5;$k++) {
   $x=str_replace($v[$k],"",$x);
  }
  $link=base64_decode($x);
  }
  //echo $z;
////////////////////////////
  $filelink="";
  if ($flash == "flash1") {
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc.stream/',
  'Origin: https://vidsrc.stream/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  $out=preg_replace_callback(
    "/http.+/",
    function ($m) {
      return "vidsrc.php?file=".urlencode($m[0]);
    },
    $h
  );
  //echo $out;
  //die();
  file_put_contents("lava.m3u8",$out);
  $link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  //$link="http://127.0.0.1/mobile1/scripts/filme/lava.m3u8";
  }
  if ($link && $flash <> "flash") {
      $link .="&Origin=".urlencode("https://vidsrc.stream");
      $link .="&Referer=".urlencode("https://vidsrc.stream/");
  }
} elseif (strpos($filelink,"vidsrc.stream") !== false) {      // what ?????
  //echo $filelink;

  $ua="Mozilla/5.0 (Windows NT 10.0; rv:104.0) Gecko/20100101 Firefox/104.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc.stream/',
  'Origin: https://vidsrc.stream/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  //echo $h;

  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt=$s[1];
  $t1=explode('var path = "',$h);
  $t2=explode('"',$t1[2]);
  $l="https:".$t2[0];

  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  file_put_contents("vidsrc.txt",$l);
  file_put_contents("vidsrc_time.txt",time());
  curl_setopt($ch, CURLOPT_URL, $filelink);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('hls.loadSource("',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  $filelink="";
  if ($flash <> "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $out=preg_replace_callback(
    "/http.+/",
    function ($m) {
      return "vidsrc.php?file=".urlencode($m[0]);
    },
    $h
  );
  //echo $out;
  file_put_contents("lava.m3u8",$out);
  $link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
  if ($link && $flash <> "flash")
  $link=$link."|Referer=".urlencode("https://vidsrc.stream")."&Origin=".urlencode("https://vidsrc.stream");
} elseif (strpos($filelink,"jumpeg.to") !== false) {
  // https://jumpeg.to/embed-8srdms9bslgg.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://azm.to/',
  'Cookie: file_id=5583; aff=76; ref_url=https%3A%2F%2Fazm.to%2F',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  require_once("JavaScriptUnpacker.php");
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  $h = $out.$h;
  //echo $h;
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt=$s[1];
  $t1=explode('sources:[{file:"',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if ($link && $flash <> "flash") {
   $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://jumpeg.to',
   'Connection: keep-alive',
   'Referer: https://jumpeg.to/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  $link=$link."|Referer=".urlencode("https://jumpeg.to")."&Origin=".urlencode("https://jumpeg.to");
  }
} elseif (strpos($filelink,"protonvideo.to") !== false) {
  // https://protonvideo.to/iframe/112b3d490cefa95fd11d7c042332e776/
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $t1=explode("/",$filelink);
  $l="https://api.svh-api.ch/api/v4/player";
  $post='{"idi":"'.$t1[4].'"}';
  //echo $post;
  //die();
	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => $post ,
		)
	);
	$context = stream_context_create($options);
	$h = @file_get_contents($l, false, $context);

  $x=json_decode($h,1);
  //print_r ($x);
  $link1=$x['file'];
  if (preg_match("/http.+/",$link1,$m)) {
    $link=trim($m[0]);
    if ($flash <> "flash")
      $link=$link."|Referer=".urlencode("https://protonvideo.to")."&Origin=".urlencode("https://protonvideo.to");
  }
} elseif (strpos($filelink,"tezfiles.com") !== false) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $t1=explode("file/",$filelink);
  $t2=explode("/",$t1[1]);
  $id=$t2[0];
  $l="https://api.tezfiles.com/v1/files/".$id;
  //echo $l;
  $head=array('Origin: https://tezfiles.com',
  'Cookie: accessToken=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1YmQyYTQ4NDhkZmFlMzVhYTRlYTQ4NTkiLCJhdWQiOiJjbGllbnQiLCJ0eXBlIjoiYWNjZXNzVG9rZW4iLCJpc3MiOiJ0eiIsImNJZCI6IjViZDJhNDg0OGRmYWUzNWFhNGVhNDg1OSIsImp0aSI6ImYyZjE1MzQwOTlkMGYiLCJpYXQiOjE2MTc2OTQ3OTEsImV4cCI6MTYxODI5OTU5MX0.usKkOKrx5oaOBaU6iE46ICztxSt6NaieVw6e4ITM4hM; refreshToken=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1YmQyYTQ4NDhkZmFlMzVhYTRlYTQ4NTkiLCJhdWQiOiJjbGllbnQiLCJ0eXBlIjoicmVmcmVzaFRva2VuIiwiaXNzIjoidHoiLCJjSWQiOiI1YmQyYTQ4NDhkZmFlMzVhYTRlYTQ4NTkiLCJqdGkiOiI3MWYzOWIyZjQzYjliIiwiaWF0IjoxNjE3Njk0NzkxLCJleHAiOjE2MjAyODY3OTF9.gj3TLzNjub83M8duk751QOFQ4lVcVpL2B49rTxyRRpg');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $link=$x['videoPreview']['video'];
  $link= $x['videoPreview']['alternativeResolutions'][0]['url'];
} elseif (strpos($filelink,"publish2.me") !== false) {
  //echo $filelink;
} elseif (strpos($filelink,"k2s.cc") !== false) {
  //echo $filelink;
  // https://k2s.cc/file/8268d115cd824/SAS.Red.Notice.2021.1080p.WEBRip.mp4&size=2.3%20%20GB
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $t1=explode("file/",$filelink);
  $t2=explode("/",$t1[1]);
  $id=$t2[0];
  $l="https://api.k2s.cc/v1/files/".$id."?referer=https%3A%2F%2Fflixgo.me";
  //echo $l;
  $head=array('Origin: https://k2s.cc',
  'Cookie: accessToken=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1YWNkOWZhMGZjNGUwNzFjNzE1NzFhNDAiLCJhdWQiOiJjbGllbnQiLCJ0eXBlIjoiYWNjZXNzVG9rZW4iLCJpc3MiOiJrMnMiLCJjSWQiOiI1YWNkOWZhMGZjNGUwNzFjNzE1NzFhNDAiLCJqdGkiOiI1YTU3MzYyMWQ2ZTFmIiwiaWF0IjoxNjE3Njk2MzQ0LCJleHAiOjE2MTgzMDExNDR9.6UrLyK7VGiAFRlnSe30K377Qze73X-DBgp75llfIndY; refreshToken=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1YWNkOWZhMGZjNGUwNzFjNzE1NzFhNDAiLCJhdWQiOiJjbGllbnQiLCJ0eXBlIjoicmVmcmVzaFRva2VuIiwiaXNzIjoiazJzIiwiY0lkIjoiNWFjZDlmYTBmYzRlMDcxYzcxNTcxYTQwIiwianRpIjoiMDI2YTc1ZjExNThmMyIsImlhdCI6MTYxNzY5NjM0NCwiZXhwIjoxNjIwMjg4MzQ0fQ.Ls39N9lHkGjjinwYroZ8b2GmiTHCMKU7ranZpWIpZ4w;');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $link=$x['videoPreview']['video'];
  $link= $x['videoPreview']['alternativeResolutions'][0]['url'];
//} elseif (strpos($filelink,"cloudemb.") !== false) {
} elseif (strpos($filelink,"watchonline.ag") !== false) {
//echo $filelink;
  $t1=explode("link=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $link=$t2[0];
  $srt=$t2[1];
  //echo $srt;
  //echo $link."\n";
  //$link="https://blectionoud.site/html/aes/746bba909998c44909a3785b710aefed/1642900119/lookmovie144.xyz/storage6/movies/0409301-east-broadway-2006-1642855067/5f4c81809085d1ea1f4f4cbb839ec95d.mp4/index.m3u8";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

  $ref="watchonline.ag";
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;
  $x =  dirname($link);
  if (preg_match("/\/aes\//",$link) && $flash <> "flash") {
  $hoo=parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$ref);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $h3=str_replace('URI="/keys','URI="https://'.$hoo.'/keys',$h3);
  //echo $h3;
  //die();
  $out=preg_replace_callback(
    "/seg\-.+/",
    function ($m) {
      global $x;
      return $x."/".$m[0];
    },
    $h3
  );
 //echo $out;
 //die();
 file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
  }
  if ($link && $flash != "flash")
   $link=$link."|Origin=".urlencode("https://".$ref)."&Referer=".urlencode("https://".$ref)."&User-Agent=".urlencode($ua);

} elseif (strpos($filelink,"lookmovie.ag") !== false) {
//echo $filelink;
  $t1=explode("link=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $link=$t2[0];
  $srt=$t2[1];
  //echo $srt;
  //echo $link."\n";
  //$link="https://blectionoud.site/html/aes/746bba909998c44909a3785b710aefed/1642900119/lookmovie144.xyz/storage6/movies/0409301-east-broadway-2006-1642855067/5f4c81809085d1ea1f4f4cbb839ec95d.mp4/index.m3u8";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
  $ref=file_get_contents($base_cookie."lookmovie_ref1.txt");
  $t1=explode("|",$ref);
  $ref=$t1[1];
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;
  $x =  dirname($link);
  if (preg_match("/\/aes\//",$link) && $flash <> "flash") {
  $hoo=parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$ref);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $h3=str_replace('URI="/keys','URI="https://'.$hoo.'/keys',$h3);
  //echo $h3;
  //die();
  $out=preg_replace_callback(
    "/seg\-.+/",
    function ($m) {
      global $x;
      return $x."/".$m[0];
    },
    $h3
  );
 //echo $out;
 //die();
 file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
  }
  if ($link && $flash != "flash")
   $link=$link."|Origin=".urlencode("https://".$ref)."&Referer=".urlencode("https://".$ref)."&User-Agent=".urlencode($ua);

} else if (preg_match("/\w+ssb\.|vidmoviesb\.|vidmovie\.|lvturbo\.|gdpress\.|watchsb\.|sb\w+\.|sbl?anh\.|sbbrisk\.|sbthe\.|sblongvu\.|sbfast\.|sbchill\.|sbfull\.|sbspeed\.|sbembed\.com|ssbstream|streamsss|sbembed1\.com|sbplay\.|sbvideo\.net|s?streamsb\.net|sbplay\.one|cloudemb\.com|playersb\.com|tubesb\.com|sbplay\d\.|embedsb\.com/",$host_filelink)) {
  // https://cloudemb.com/e/snlcjicyu49f.html
  // ssbstream.net
  // sblongvu.com
  // vidmoviesb
  // https://sbplay2.xyz/e/zxeqa7oa68o4?caption_1=https://msubload.com/sub/the-adam-project/the-adam-project.v1.vtt
  // https://sbfast.com/e/uqykbpajgp67?caption_1=https://seriale-online.net/subtitrarifilme/tt0082340.vtt&sub_1=Romana
  // https://watchsb.com/e/nk92su1bjavj?poster=https%3A%2F%2Fhdmoviesb.com%2Fupload%2Fhow-to-please-a-woman-cover.png&caption_1=https%3A%2F%2Fhdmoviesb.com%2Fsub%2FHow-To-Please-A-Woman-2022-WEBRip_English-CP.srt&sub_1=English
  //echo $filelink;
  //die();
  //$filelink=str_replace("/https://sbfast.com","https://sblongvu.com",$filelink);
  //$filelink="https://sbchill.com/e/evbarypddclb?caption_1=https://seriale-online.net/subtitrarifilme/tt6685538.vtt&sub_1=Romana";
  //(?:\/\/|\.)((?:tube|player|sbl?anh|sb\w+|lvturbo|vidmoviesb|sbfast|sbchill|sbbrisk|sblongvu|gdpress|vidmovie|sbfull|sbspeed|sbthe|watchsb|cloudemb|ssbstream|streamsss|s?stream)?s?b?(?:embed\d?|embedsb\d?|play\d?|video)?\.(?:com|net|org|one|\w+))
  $host=parse_url($filelink)['host'];
  $pattern = "/".preg_quote($host)."\/(embed\-|e|play|d)\/([0-9a-zA-Z]+)/";
  preg_match($pattern,$filelink,$m);
  //print_r ($m);
  //die();
  //$host=$m[1];

  $id=$m[2];
  $l="https://".$host."/e/".$id.".html";
  //echo $l;
  //if (preg_match("/[e|f]\/([\w\_\-]+)\./",$filelink,$m))
  // $id=$m[1];
  //$l="https://cloudemb.com/play/".$id."?auto=1&referer=&";

  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink." ".$h, $s)) {
    $srt="https:".$s[1];
  }

    function enc($a) {
     $b="";
     for ($k=0;$k<strlen($a);$k++) {
      $b .=dechex(ord($a[$k]));
     }
    return $b;
    }
    function makeid($a) {
     $b="";
     $c="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
     for ($k=0;$k<$a;$k++) {
      $b .=$c[rand(0,61)];
     }
     return $b;
     }
     //echo $id;
     $x=makeid(12)."||".$id."||".makeid(12)."||"."streamsb";
     $x=makeid(12)."||".$id;
     //$x="7Vd5jIEF2lKy||nuewwgxb1qs";
     $c1=enc($x);
     $x=makeid(12)."||".makeid(12)."||".makeid(12)."||"."streamsb";
     //$x=makeid(12)."||".$id."||".makeid(12)."||"."streamsb";
     //$x="j3CI2dNwO0Ti||".$id."||wQp2cHEjpyzX||streamsb";
     $c2=enc($x);
     $x=makeid(12)."||".$c2."||".makeid(12)."||"."streamsb";
     $c3=enc($x);

     // cesx3
     $l="https://".$host."/".$alias."/".$c1."/".$c3;

     $c1="375664356a494546326c4b797c7c6e756577776778623171737";
     $c3=enc("91a9MQzmQu7T||".$id."||HSsvhTZGLdhX||streamsb");
     $l="https://".$host."/".$c1."/".$c3;

     //$l="https://sbchill.com/375664356a494546326c4b797c7c6e756577776778623171737/6a33434932644e774f3054697c7c656238786d627171386333397c7c775170326348456a70797a587c7c73747265616d7362";
     
     //echo $l;
     $ua="Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0";
     $head=array('Accept: application/json, text/plain, */*',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'watchsb: sbstream',
     'Connection: keep-alive',
     'Referer: https://'.$host);

     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_USERAGENT, $ua);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     //curl_setopt($ch, CURLOPT_HEADER,1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
     curl_setopt($ch, CURLOPT_TIMEOUT, 25);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
     //die();
     $x=json_decode($h,1);
     //print_r ($x);
     $link=$x['stream_data']['file'];
     //echo $link;
     //$link="https://delivery302.akamai-cdn-content.com/hls2/01/04267/zxeqa7oa68o4_,l,n,.urlset/master.m3u8?t=XYkhWjAOJKAee9WBXmtquSPRqx0moN-Yq4582ZY5ls0&s=1647166170&e=21600&f=21338537&srv=stopremium020-TOPDL&client=40.68.27.60";
     if ($link) {
      $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0',
      'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
      'Referer: https://'.$host."/",
      'Origin: Referer: https://'.$host
      );
      //print_r ($head);
      //echo $link;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      $link=get_max_res($h,$link);
      //die();
      if ($flash == "flash") {
      $t1=explode("?",$_SERVER['HTTP_REFERER']);
      $p=dirname($t1[0]);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);
      $out=preg_replace_callback(
      "/https\:\/\/.+/",
      function ($m) {
        return "vv.php?file=".urlencode($m[0]);
      //return $m[0];
      },
      $h
      );
      file_put_contents("lava.m3u8",$out);
      $link = $p."/lava.m3u8";
      }

      if ($link && $flash <> "flash") {
      if ($flash=="mpc") {
      $link=$link."|User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0");
      //$link=$link."&Origin=https://sbchill.com";
      $link=$link."&Accept=*/*";
      $link=$link."&Accept-Language=".urlencode("ro-RO\,ro;q\=0.8\,en-US;q\=0.6\,en-GB;q\=0.4\,en;q\=0.2");
      $link=$link."&Accept-Encoding=deflate";
      $link=$link."&Origin=https://sbchill.com";
      $link=$link."&Connection=keep-alive";
      $link=$link."&Referer=https://sbchill.com/";
      //$link=$link."&Sec-Fetch-Dest=empty";
      //$link=$link."&Sec-Fetch-Mode=cors";
      //$link=$link."&Sec-Fetch-Site=cross-site";

      } else {
      $link=$link."|User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0");
      $link .="&Accept-Language=".urlencode("ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2");
      $link .="&Origin=".urlencode("https://".$host);
      $link .="&Referer=".urlencode("https://".$host."/");
      }
      }
      }

} elseif (preg_match("/(voe|reputationsheriffkennethsand|v\-o\-e\-unblock|valeronevijao)\./",$filelink)) {
  // https://voe.sx/e/abx2no5yos5s
  // https://reputationsheriffkennethsand.com/ejy2dyea4jrk
  // https://valeronevijao.com/e/djn3gtcqfykb
  // v-o-e-unblock.com
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://voe.sx");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //'hls': '
  //$t1=explode('hls": "',$h);
  //$t2=explode('"',$t1[1]);
  //$link=$t2[0];
  if (preg_match("/[\"\']hls[\'\"]\:\s*[\'\"]([^\"\']+)/",$h,$m))
   $link=$m[1];
  else
   $link="";
  if ($link && $flash != "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://voe.sx");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  $link=$link."|Referer=".urlencode("https://voe.sx")."&Origin=".urlencode("https://voe.sx");
  }
} elseif (preg_match("/streamzz?\.(to|ws)/",$filelink)) {
  //$filelink="https://streamzz.to/fZ200bWlzamw4Y2Rwc3hk";
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_NOBODY,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  /*
  if (preg_match_all("/location:\s+(.+)/i",$h,$m)) {
   $l=trim($m[1][count($m[1])-1]);
   $host=parse_url($l)['host'];
   preg_match("/\/x(\w+)/",$m[1][1],$n);
   $link="https://".$host."/getlink-".$n[1].".dll";
  }
  */
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match("/src\:\'([^\']+)/",$out,$m))
   $link=$m[1];
//} elseif (strpos($filelink,"hls.ronemo.com") !== false) {
} elseif (preg_match("/hls\.ronemo\.com|rocdn\.net|rocdn\.org/",$filelink)) {
  $t2=explode("?sub=",$filelink);
  $link=$t2[0];
  $srt=$t2[1];
  //$link="https://rocdn.org/yTH5GR_Ao60/f/playlist.m3u8";
  //$filelink="";
  //$link="https://rocdn.net/s1aM64QBVEJ/f/playlist.m3u8";
  // https://hls.ronemo.com/Wr-mdZwxtla/f/playlist.m3u8
  // https://hls.ronemo.com/Wr-mdZwxtla/f/480.jpg
  if ($flash <> "flash") {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://ronemo.com");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  /*
  //$link=str_replace(".jpg",".m3u8",$link);
  //$link="https://rocdn.net/Wr-mdZwxtla/f/1080.jpg";
  //$link="https://rocdn.net/Wr-mdZwxtla/f/1080000000.png";
  $x =  dirname($link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://ronemo.com");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $out=preg_replace_callback(
    "/\d+\.png/",
    function ($m) {
      global $x;
      return $x."/".$m[0];
    },
    $h
  );
  //echo $h;
 //echo $out;
 //die();
 file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
  */
  //$link="https://rocdn.net/Wr-mdZwxtla/f/1080000000.png";
  $link=$link."|Referer=".urlencode("https://ronemo.com")."&Origin=".urlencode("https://ronemo.com");
  $link .="&User-Agent=".urlencode($ua);
  }
} elseif (strpos($filelink,"ronemo.com") !== false && strpos($filelink,"hls.ronemo.com") === false) {
  // https://ronemo.com/embed/ZwGt07_hpYI
  // https://hls.ronemo.com/ZwGt07_hpYI/f/playlist.m3u8
  // https://ronemo.com/video/s1aM64QBVEJ/mDdk31pjMQE
  //echo $filelink;
  preg_match("/embed\/([\w|\_\-]+)/",$filelink,$m);
  $link="https://hls.ronemo.com/".$m[1]."/f/playlist.m3u8";
  if ($m[1] && $flash != "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://ronemo.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
//} elseif (strpos($filelink,"ninjastream.to") !== false) {
} elseif (preg_match("/ninjastream\.to|streamlare\.com|slmaxed\.com|slwatch\.co|sltube\.org/",$filelink)) {
  // https://ninjastream.to/watch/74GA0J8brZYBk
  //echo $filelink;
  // https://slwatch.co/
  // https://ninjastream.to/watch/GeLZzzEo1ZyOn/La.bonne.epouse.720p.mp4
  // https://streamlare.com/e/10rwmD7oYJqDRaZ8
  // https://streamlare.com/v/oLvgezwJoEPDbp8E
  // https://streamlare.com/e/oMqN9nGG87ongxeA
function xor_string($string, $key) {
    for($i = 0; $i < strlen($string); $i++)
        $string[$i] = ($string[$i] ^ $key[$i % strlen($key)]);
    return $string;
}
 if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s)) {
    $srt="https:".$s[1];
  }
// app.js
// return String.fromCharCode(t.charCodeAt() ^ e.charCodeAt(n % e.length))
// return null==e&&(e="2")
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0";
  $host=parse_url($filelink)['host'];
  //$filelink=str_replace($host,"slwatch.co",$filelink);
  $cookie=$base_cookie."streamlare.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);

  $html=htmlspecialchars_decode($html,ENT_QUOTES);
  //echo $html;
  if (preg_match("/Location\:\s*(.+)/i",$html,$m3))
   $host=parse_url(trim($m3[1]))['host'];
  else
   $host=parse_url($filelink)['host'];
  //$host="slwatch.co";
  //$t1=explode('v-bind:stream="',$html);
  //$t2=explode('">',$t1[1]);
  //$r=json_decode(trim($t2[0]),1);
  //print_r ($r);
  $t1=explode('hashid":"',$html);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $t1=explode('csrf-token" content="',$html);
  $t2=explode('"',$t1[1]);
  $csrf=$t2[0];
  $l="https://".$host."/api/video/get";
  $l="https://".$host."/api/video/stream/get";
  $l="https://slmaxed.com/api/video/stream/get";
  $l="https://".$host."/api/video/stream/get";
  $post='{"id":"'.$id.'"}';
  //$post="id=".$id;
  //    'Cookie: '.$m[1][0].'='.urldecode($m[2][0])."; ".$m[1][1].'='.urldecode($m[2][1])."; ".$m[1][2].'='.urldecode($m[2][2]).";",
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0',
  'Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Referer: '.$filelink,
   'X-Requested-With: XMLHttpRequest',
   'X-CSRF-TOKEN: '.$csrf,
   'Content-Type: application/json;charset=utf-8',
   'Content-Length: '.strlen($post),
   'Origin: https://'.$host,
   'Connection: keep-alive');
   //print_r ($head);

  
  $ch = curl_init($l);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
//print_r ($head);

  //echo $h;
  $x=json_decode($h,1);
   //print_r ($x);
  if (isset($x['result']['playlist']))
   $link= $x['result']['playlist'];
  elseif (isset($x['result']['Original']['src']))
   $link=$x['result']['Original']['src'];
  elseif (isset($x['result']['Original']['file']))
   $link=$x['result']['Original']['file'];
  elseif (isset($x['result']['file']))
   $link=$x['result']['file'];
  elseif (isset($x['result']['1080p']['file']))
   $link=$x['result']['1080p']['file'];
  elseif (isset($x['result']['720p']['file']))
   $link=$x['result']['720p']['file'];
  elseif (isset($x['result']['480p']['file']))
   $link=$x['result']['480p']['file'];
  elseif (isset($x['result']['360p']['file']))
   $link=$x['result']['360p']['file'];
  /*
  if (isset($r['host'])) {
   $y = xor_string($r['host'],"2");
   $link=$y.$r['hash']."/index.m3u8";
  }
  */
  // wss://www-6i665829.ssl0d.com/E_mExyP85IVp1rV8-vbrrw/1647198693
  /*
  $id=$x['result']['720p']['hash'];

  //echo $zz;
  $z=base64_decode($x['result']['720p']['signal']);
  $zz=xor_string($z,"3");
  //echo $zz;
  $link="https".$zz."/".$id."/playlist.m3u8";
  echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  //echo $zz;
  //if (preg_match("/streamlare/",$host))
  //$link = xor_string(base64_decode($x['result']['Original']['src']),"3");
  //$link="https://larecontent.com/video?token=SBFGQV8RCRFbR0dDQAlvHG8cREREHkMLWAoLBwsFHUBAXwNXHVBcXm8cYEYHQHx4anV-Y2RaZkJkdn1RVWt_cm8cAgUGCwAHAAIFBW8cAVIDBwkBBwICbxx6BnZrZFhpXllmSn5JaXQeR0l1e11DQ0dsBWNrdF1KfHJlWgBHV1BHUmEBUQBWeVtwUV5EBEUedFF5ZUZZUXRDQEQKXnt5a2NnB1UDawFKVF0LeGACBmNWRFt8ZVIeBXIeVlFjfEsDZApfbFsCamN6aWR9YUJ6VH0AUUBYZUB2VmMAA1dVZwpwen5DBn1ZBklydgZXaWJJXkR-fANpdEZlHnt3Z15wdXdASndaXFt9BEBVQwdbWGBGQVoAa1x7bFZKcgBdC35yHn9kYkJQcmJUZUprRkdAWHEGZHZ7Z0NyYHlUHgRBYUF-Vl9lA313ZFFwR1AAQHx4ZWR-R2p_X1YLBFcGAldSQ1Z8en5hckAHW2dgeUpdR2x6X2p7WAtpBkR5UHV_UF5BRFQCZm8cCwQAAgALAh1eQwcMQEdBVlJeDgIRHxFaQxEJEQFSAwcJAQcCAhFO";
  if ($link && $flash <> "flash" && preg_match("/(index|master)\.m3u8/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  //echo $link;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $base=dirname($link);
  if (preg_match("/\#EXT\-X\-BYTERANGE\:\s*\d+\@\d+/",$h)) {
  preg_match_all("/\#EXTINF\:(\d+\.\d+)/",$h,$m);
  //print_r ($m[1]);
  preg_match_all("/.+\.ts/",$h,$n);
  //print_r ($n[0]);
  $out="#EXTM3U"."\n";
  $out .="#EXT-X-VERSION:4"."\n";
  $out .="#EXT-X-TARGETDURATION:10"."\n";
  $ts=array_unique($n[0]);
  $ts[count($m[1])]=$n[0][count($n[0])-1];
  $t=array_keys($ts);
  //print_r ($ts);
  //print_r ($t);
  for ($k=0;$k<count($t)-1;$k++) {
   $time=0;
   for ($p=$t[$k];$p<$t[$k+1];$p++) {
     $time +=$m[1][$p];
   }
   $out .= "#EXTINF:".$time.","."\n";
   $out .= $base."/".$ts[$t[$k]]."\n";
  }
  $out .="#EXT-X-ENDLIST";
  //echo $out;
  file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $p = dirname($_SERVER['HTTP_REFERER']);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
  }
  } elseif ($link && preg_match("/video\?token/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;

  if (preg_match("/location\:\s*(.+)/i",$h,$m))
    $link=trim($m[1]);


    if ($flash <> "flash") {
     $link .="|Referer=".urlencode("https://".$host."/");
     $link .="&User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0");
    }
 }
} elseif (strpos($filelink,"wootly.ch") !== false) {
//echo $filelink;
  preg_match("/\/(\?v\=|e\/)([a-zA-Z0-9]+)/",$filelink,$m);
  $id=$m[2];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  //$l="https://www.wootly.ch/e/".$id;
  $l="https://www.wootly.ch?v=".$id;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('iframe src="',$html);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  $post="qdf=1";
  $cookie=$base_cookie."wootly.dat";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://www.wootly.ch',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).'',
  'Origin: https://www.wootly.ch',
  'Connection: keep-alive');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $t1=explode('tk="',$html);
  $t2=explode('"',$t1[1]);
  $tk=$t2[0];
  $t1=explode('vd="',$html);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $l="https://www.wootly.ch/ajax?t=".$tk."&id=".$id;
  $l="https://www.wootly.ch/grabd?t=".$tk."&id=".$id;
  //echo $l;
  //$l="https://www.wootly.ch/ajax?t=JDEkOG9oaFFKWkgkZUR0S0tZc2l5bEhpVzN3Rkoxdlh0MQ&id=1311431";
  //$l="https://www.wootly.ch/ajax?t=JDEkalJTcHYwWEYkMWlGNUlJMFI5YkRWSUR0cjc1RFpZLw&id=1257246";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://www.wootly.ch');
  //echo $l;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (strpos($h,"http") === false)
   $l="https:".$h;
  else
   $l=$h;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $l."\n".$h;
  if (preg_match("/location\:\s*(.+)/i",$h,$r))
   $link=trim($r[1]);
} else if (preg_match("/megaxfer\.ru/",$filelink)) {
     $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/80.0',
     'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'Connection: keep-alive',
     'Referer: https://moviesjoy.to/watch-movie/enola-holmes-63478.3360158',
     'Upgrade-Insecure-Requests: 1');
     //echo $filelink;
     // megaxfer.ru
     $ch = curl_init($filelink);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
     //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     curl_setopt($ch, CURLOPT_HEADER,1);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
     if (preg_match("/iframe\s+src\=[\"|\'](.*?)[\'|\"]/",$h,$m))
      $filelink=$m[1];
      $filelink=str_replace("&amp;","&",$filelink);
      //echo $filelink;
      $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0',
      'Accept: */*',
      'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
      'Accept-Encoding: deflate',
      'Connection: keep-alive');
      $ch = curl_init($filelink);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_HEADER,1);
      $html = curl_exec($ch);
      curl_close ($ch);
      //echo $html;
      $t1=explode('var domain = "',$html);
      $t2=explode('"',$t1[1]);
      $dom=$t2[0];
      $t1=explode("var tracks = `",$html);
      $t2=explode("`;",$t1[1]);
      //echo $t2[0];
      $w=json_decode($t2[0],1);
      //print_r ($w);
      $srt="";
      for ($k=0;$k<count($w);$k++) {
       if (preg_match("/romania/i",$w[$k]['label'])) {
         $srt=$w[$k]['file'];
         break;
       }
      }
      if (!$srt) {
      for ($k=0;$k<count($w);$k++) {
       if (preg_match("/english/i",$w[$k]['label'])) {
         $srt=$w[$k]['file'];
         break;
       }
      }
      }
      //echo $srt;
      //echo $filelink;
      // https://beta.beststream.io/api/iframe?id=893f7f4376ef9cdf71571d636020853a&e=1151509
      // https://beta.beststream.io/hls/json/info/893f7f4376ef9cdf71571d636020853a
      // https://beta.beststream.io/hls/m3u8/893f7f4376ef9cdf71571d636020853a/master.m3u8
      // https://beststream.io/hls/p2p/792867f6ff8a0fb50dbb31ccd53b866d/index.m3u8
      if (preg_match("/beststream\.io/",$filelink)) {
        $t1=explode("id=",$filelink);
        $t2=explode("&",$t1[1]);
        //$link="https://beststream.io/hls/file/".$t2[0]."/index.m3u8";
        if (preg_match("/beta\.beststream\.io/",$dom))
        $link=$dom."/hls/m3u8/".$t2[0]."/master.m3u8";
        else
        $link=$dom."/hls/p2p/".$t2[0]."/index.m3u8";
        //echo $link;
      }
//} elseif (strpos($filelink,"ihls.cc") !== false) {
} elseif (preg_match("/ihls\.cc|onionflix\.me|onionflix\.cc/",$filelink)) {
  // https://ihls.cc/p/hls/537d9b6c927223c796cac288cced29df
  // https://onionflix.me/p/hls/5a4b25aaed25c2ee1b74de72dc03c14e
  // 'https://m3.onionflix.cc/p/hls/e4da3b7fbbce2345d7772b0674a318d5
  $host=parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://'.$host);
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  $h=str_replace("\/","/",$h);
  //echo $h;
  if (preg_match("/videoServer\"\:\"/",$h)) {
  $t1=explode('videoServer":"',$h);
  $t2=explode('"',$t1[1]);
  $sv=$t2[0];
  $t1=explode('videoUrl":"',$h);
  $t2=explode('"',$t1[1]);
  $link="https://".$host.$t2[0]."?s=".$sv."&d=";
  } else {
    $t1=explode('file: "',$h);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
  }
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://".$host);
} elseif (strpos($filelink,"hlscloud.stream") !== false) {
  // https://hlscloud.stream/player/1nKJmeK4IycpqNz/
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://onionplay.co/');
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $t1=explode('sources: [{"file":"',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://hlscloud.stream");
} elseif (strpos($filelink,"embed.streamvn.com") !== false) {
  //echo $filelink;
  // $l="https://embed.streamvn.com/embed-autostart-ajax.php?id=56406";
  $t1=explode('id=',$filelink);
  $t2=explode('&',$t1[1]);
  $id=$t2[0];
  $l="https://embed.streamvn.com/embed-autostart-ajax.php?id=".$id;
  $head= array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://embed.streamvn.com/vip-autostart-embed.php?id=59569&key=d2978c8f19e3jgi893af5ee9556513269');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  //print_r ($r);
  $h=$r[0]['player_jw'];
  //echo $h;
  $t1=explode('sources:',$h);
  $t2=explode('tracks:',$t1[1]);
  $h1=$t2[0];
  //echo $h1;
  if (preg_match_all("/file\:\"(\S+)\"/",$h1,$m))
    $link=$m[1][count($m[1])-1];
} elseif (strpos($filelink,".videocdn.pw") !== false) {
  //$l="http://4163.videocdn.pw/IAF0wWTdNYZm/movie/19132?translation=381";
  $ua = $_SERVER['HTTP_USER_AGENT'];
    function decodeUN($a) {
        $a=substr($a, 1);
        //echo $a;
        $s2 = "";
        $s3="";
        $i = 0;
        while ($i < strlen($a)) {
            //$s2 += ('\u0' + $a[i:i+3])  // substr('abcdef', 1, 3);
            $s2 = $s2.'\u0'.substr($a, $i, 3);
            $s3 = $s3.chr(intval(substr($a, $i, 3),16));
            $i = $i + 3;
       }
       return $s3;
   }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("&quot;","'",$h);
  preg_match_all("/:\'(#\w+)\'/",$h,$m);
  //print_r ($m);
  $z="";
  for ($p=0;$p<count($m[1]);$p++) {
  $z .=decodeUN($m[1][$p]);
  }
  //echo $z;
  preg_match_all("/\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]+(240|360|480|720|1080)\.mp4/",$z,$m);
  //print_r ($m);
  $r=array();
  for ($k=0;$k<count($m[1]);$k++) {
   $r[$m[1][$k]]=$m[0][$k];
  }
  //print_r ($r);
  if (isset($r['1080']))
   $link="https:".$r['1080'];
  elseif (isset($r['720']))
   $link="https:".$r['720'];
  elseif (isset($r['480']))
   $link="https:".$r['360'];
  elseif (isset($r['360']))
   $link="https:".$r['360'];
  elseif (isset($r['240']))
   $link="https:".$r['240'];
} elseif (strpos($filelink,"hydrax.net") !== false) {
    //https://hydrax.net/watch?v=fZUPUFcqYIz
    //echo $filelink;
    $cookie=$base_cookie."hydrax.dat";
    if (preg_match("/watch\?v\=([a-zA-Z0-9_\-]+)/",$filelink,$m)) {
      $slug=$m[1];
    }
    $host="hydrax.net";
    $l="https://ping.idocdn.com/";
    $post="slug=".$slug;
    $host="hydrax.net";
    $head=array('Accept: */*',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://'.$host.'',
    'Referer: '.$filelink.'',
    'Content-Length: '.strlen($post).'',
    'Connection: keep-alive');
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    curl_close ($ch);
    $x=json_decode($html,1);
    //print_r ($x);
    $serv=$x['url'];
    $l="https://".$serv."/";
    //echo $l;
    $l1=$l."ping.gif";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,$filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/hx_stream\=(.*?)\;/",$h,$m)) {
      $link="https://".$serv."/#st=".(1000*time())."/v.mp4";
      //$link="https://".$serv."/1";
      if ($flash <> "flash")
       $link=$link."|Cookie=".urlencode("hx_stream=".$m[1])."&Referer=".urlencode($filelink);
  $head=array('Cookie: hx_stream='.$m[1].'');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */

 }
} elseif (strpos($filelink,"archive.org") !== false) {
  $link=$filelink;
//} elseif (strpos($filelink,"rustream.") !== false) {
} elseif (preg_match("/rustream\.(\w+)\/vid/",$filelink)) {
  // $l="https://rustream.xyz/vid/lady-driver-2020";

  $ua = $_SERVER['HTTP_USER_AGENT'];
  //$l="https://rustream.xyz/vid/lady-driver-2020";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: https://europixhd.pro/',
   'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/type=\"video\/mp4\" src\=\"(\.\.)?(.*?)\"/",$h,$m)) {
   //print_r ($m);
   if (preg_match("/http/",$m[2]))
    $link=$m[2];
   else
    $link= "https://rustream.xyz".$m[2];
   if ($flash <> "flash") $link=$link."|Referer=".urlencode("https://rustream.xyz");
   if (preg_match("/kind\=\"captions\" src=\"\.\.(.*?)\"/",$h,$s))
    $srt="https://rustream.xyz".$s[1];
  }
} elseif (strpos($filelink,"easyload.io") !== false) {
  // https://easyload.io/e/K0NW5BAJdJ
  $filelink=str_replace("/f/","/e/",$filelink);
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://vidcloud9.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('exdata="',$h);
  $t2=explode('"',$t1[1]);
  $e=$t2[0];
  $x=base64_decode(base64_decode($e));
  $y=json_decode($x,1);
  //print_r ($y);
  $src=$y['streams'][0]['src'];
  $out="";
  $t="15";
  for ($i=0;$i<strlen($src);$i++) {
     $out .=chr(ord($src[$i]) ^ ord($t[$i% strlen($t)]));
  }
  if (strpos($out,"http") !== false)
    $link=$out;
  if ($link && strpos($link,".m3u8") === false)
    $link=$link."/v.mp4";
  if ($link && $flash <> "flash") $link=$link."|Referer=".urlencode("https://easyload.io");
  if (isset ($y['subtitles'][0]['src']))   // ???????
    $srt = $y['subtitles'][0]['src'];
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://easyload.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
} elseif (strpos($filelink,"userload.") !== false) {
  //https://userload.co/embed/42f856c1b175/tt0115697.mp4?c1_file=https://seriale-online.net/subtitrarifilme/tt0115697.vtt&c1_label=Romana
  //echo $filelink;
  //$filelink=str_replace("?autoplay=yes","",$filelink);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0";
  $filelink=str_replace("http:","https:",$filelink);
  include ("AADecoder.php");
  require_once("JavaScriptUnpacker.php");
  $l="https://userload.co/api/assets/userload/js/videojs.js";
  // https://userload.co/api/assets/userload/js/form.framework.js
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu=new AADecoder();
  $out = $jsu->decode($h);
  //echo $out;
  $morocco1="";
  $mycountry1="";
  if (preg_match("/.send\(\"morocco\=\"\+(\w+)\+\"\&mycountry\=\"\+(\w+)/",$out,$m)) {
   $morocco1=$m[1];
   $mycountry1=$m[2];
  }
  //echo $filelink;
  //$filelink="https://userload.co/embed/106015fbfb09/The.Man.in.the.Hat.2020.1080p.AMZN.WEB-DL.DDP5.1.H264-CMRG.mp4";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://userload.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //$out = $jsu->decode($h);
  //echo $out;
  // track kind="captions" src="https://userload.co/api/srt2vtt/?convert=aHR0cHM6Ly9zZXJpYWxlLW9ubGluZS5uZXQvc3VidGl0cmFyaWZpbG1lL3R0MDExNTY5Ny52dHQ=/"
  if (preg_match("/kind\=\"captions\" src\=\"(.*?)\"/si",$h,$s))
    $srt=$s[1];
  $t1=explode('div class="video-div"',$h);
  $h=$t1[1];
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;

  $pat="/var\s*".$morocco1."\s*\=\"(\w+)/";
  if (preg_match($pat,$out,$m))
   $morocco=$m[1];
  $pat="/var\s*".$mycountry1."\s*\=\"(\w+)/";
  if (preg_match($pat,$out,$m))
   $mycountry=$m[1];
  if (!$morocco) {
  if (preg_match_all("/var\s+\w+\=\"([^\"]+)/",$out,$x)) {
  $morocco=$x[1][1];
  $mycountry=$x[1][2];
  //print_r ($x);
  }
  }
  // bcdcbafe
  // morocco="+efefdaeb+"&mycountry="+aaffafedbdba
  // morocco="+adddebbf+"&mycountry="+eefcfcccdeef
  if ($morocco) {
  $l="https://userload.co/api/request/";
  $post="morocco=".$morocco."&mycountry=".$mycountry;
  //echo $post;
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded',
   'Content-Length: '.strlen($post).'',
   'Origin: https://userload.co',
   'Connection: keep-alive',
   'Referer: '.$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $post;
  //echo $h;
  if (strpos($h,"http") === false && !preg_match("/File has been removed or does not exist/",$h))
    $link="https://userload.co".trim($h);
  elseif (substr($h, 0, 4)=="http")
    $link=trim($h);
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://userload.co");
  }
} elseif (strpos($filelink,"okstream.") !== false) {
  // https://www.okstream.cc/e/6d7198848112/tt0396652.mp4?c1_file=https://serialeonline.to/subtitrarifilme/tt0396652.vtt&c1_label=Romana
  $filelink=str_replace("/f/","/e/",$filelink);
  //include ("jj.php");
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  $t1=explode('var keys="',$h);
  $t2=explode('"',$t1[1]);
  $morocco=$t2[0];
  $t1=explode('var protection="',$h);
  $t2=explode('"',$t1[1]);
  $mycountry=$t2[0];
  $l="https://www.okstream.cc/request/";
  $post="morocco=".$morocco."&mycountry=".$mycountry;
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded',
   'Content-Length: '.strlen($post).'',
   'Origin: https://www.okstream.cc',
   'Connection: keep-alive',
   'Referer: '.$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  if (strpos($h,"http") === false)
    $link="https://www.okstream.cc".trim($h);
  else
    $link=trim($h);
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://www.okstream.cc");
} elseif (strpos($filelink,"evoload.") !== false) {
  // https://evoload.io/e/wEZkuDhnkURe5j
  //$filelink="https://evoload.io/e/dUZJu6qjQQsqZ3";
  //echo $filelink;
  $filelink=str_replace("/f/","/e/",$filelink);
  if (preg_match("/\/e\/(\w+)/",$filelink,$m))
   $code=$m[1];
  else
   $code="";
  //include ("rec.php");
  $cookie=$base_cookie."evoload.dat";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://evoload.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  if (preg_match("/captcha\_pass\"\s+value\=\"([^\"]+)\"/",$h,$m)) {
   $pass=$m[1];
   $l="https://csrv.evosrv.com/captcha";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://evoload.io',
   'Connection: keep-alive',
   'Referer: https://evoload.io/');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $csrv_token = curl_exec($ch);
   curl_close($ch);
   // {"code":"1nT1LqinMiOTj3","token":"ok","csrv_token":"xoh068m1fqn","pass":"7dczpuzsmak","reff":""}
   $a=array(
   'code' => $code,
   'token' => 'ok',
   'csrv_token' => $csrv_token,
   'pass' => $pass,
   "reff" => '');
   $post=json_encode($a);
   $l="https://evoload.io/SecurePlayer";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/json;charset=utf-8',
   'Content-Length: '.strlen($post),
   'Origin: https://evoload.io',
   'Connection: keep-alive',
   'Referer: '.$filelink);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);

   if (isset ($r['subtitles'])) {
    $srt = $r['subtitles'][0]['system_name'];
   }
   if (isset($r['stream'])) {
    if (isset($r['stream']['backup']))
     $link=$r['stream']['backup'];
    elseif (isset($r['stream']['src']))
     $link=$r['stream']['src'];
   }
  }
  //}
} elseif (strpos($filelink,"segavid.") !== false) {
   //echo $filelink;
   include ("AADecoder.php");
  // https://segavid.com/embed-pcqorra827hk.html?Drivename=Garrows.Law.S02E03.DVDRip.XviD-HAGGIS.[123fmovies.best].mp4
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   $out="";
   $jsu=new AADecoder();
   $out = $jsu->decode($h);
   //echo $out;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h.$out, $s))
    $srt="https:".$s[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h.$out, $m))
  $link=$m[1];
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://segavid.com");
//} elseif (strpos($filelink,"streamsb.net") !== false) {
} elseif (preg_match("/streamsb\.net|sbplay\.org/",$filelink)) {
   // https://streamsb.net/embed-la2ckqreqxzt.html
   // https://sbplay.org/embed-eonaxhc1ji2f.html
   //echo $filelink;
   $host=parse_url($filelink)['host'];
   //$filelink=str_replace("sbplay.org","streamsb.net",$filelink);
   if (preg_match("/embed\-(\w+)/",$filelink,$m))
    $id=$m[1];
   $filelink="https://".$host."/play/".$id;
   require_once("JavaScriptUnpacker.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER,"https://hdmovie8.com");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  //echo $out;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
    $srt="https:".$s[1];
  if (preg_match('/file\:\"([^\"]+)\"/', $out, $m))
  $link=trim($m[1]);
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://".$host);
} elseif (strpos($filelink,"playtube.") !== false) {
  /* using tear.js
  $t1=explode("https://playtube.ws",$filelink);
  $a1=urldecode(base64_decode($t1[1]));
  $t1=explode("?out=",$a1);
  $t2=explode("&sub=",$t1[1]);
  $link=urldecode($t2[0]);
  $srt=base64_decode(urldecode($t2[1]));
  */
   require_once("JavaScriptUnpacker.php");
   include ("tear.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER,"https://seriale-online.net/");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  //echo $out;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h.$out, $s))
    $srt="https:".$s[1];
  $link=""; // new version
  $t1=explode("file_code:'",$out);
  $t2=explode("'",$t1[1]);
  $file_code=$t2[0];
  $t1=explode("hash:'",$out);
  $t2=explode("'",$t1[1]);
  $hash=$t2[0];
  $l="https://playtube.ws/dl";
  $post="op=playerddl&file_code=".$file_code."&hash=".$hash;
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'X-Requested-With: XMLHttpRequest',
   'Content-Length: '.strlen($post),
   'Origin: https://playtube.ws',
   'Connection: keep-alive',
   'Referer: https://playtube.ws');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $w=0;
  do {
   $h = curl_exec($ch);
   $y=json_decode($h,1);
   $w++;
  } while (isset($y[0]['error']) && $w < 10);
  curl_close($ch);
  //echo $h;
  //print_r ($y);
  $link=$y[0]['file'];
  $seed=$y[0]['seed'];
  if ($link) {
   $out=str_replace("'",'"',$out);
   $t1=explode('var chars=',$out);
   $t2=explode(';',$t1[1]);
   $e="\$chars='".$t2[0]."';";
   eval ($e);
   $t1=explode('replace(/[',$out);
   $t2=explode("]",$t1[1]);
   $rep="/[".$t2[0]."]/";
   $x=json_decode($chars,1);
   $seed=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $seed
   );
   $link = decrypt($link,$seed);
   $link=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $link
   );
  }
   if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://playtube.ws")."&Origin=".urlencode("https://playtube.ws");
} elseif (preg_match("/filemoon\.|moonmov\.pro|furher\.|truepoweroflove\.|kerapoxy\.|c4qhk0je\./",$filelink)) {
   //echo $filelink;
   //c4qhk0je.xyz
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
  $y = parse_url($filelink)['query'];
  parse_str($y,$r);
  //print_r ($r);
  if (isset($r['sub_info'])) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $r['sub_info']);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $x=json_decode($h,1);
   //print_r ($x);
   for ($k=0;$k<count($x);$k++) {
    if (preg_match("/romanian/i",$x[$k]['label'])) {
    $srt=$x[$k]['file'];
    break;
    } elseif (preg_match("/english/i",$x[$k]['label'])) {
    $srt=$x[$k]['file'];
    }
   }
  }
  //echo $srt;
  // filemoon.to
  // https://filemoon.sx/e/re09uiwgwgve?c1_file=https://seriale-online.net/subtitrarifilme/tt11291274.vtt&c1_label=Romana
  // https://filemoon.to/e/dk86vk0iouf2?sub.info=https://fmovies.to/ajax/episode/subtitles/0b5d2788859f1833c7f39eb5f5b02122?&autostart=true
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\(\)]*(\.(srt|vtt)))/', $filelink, $s))
    $srt="https:".$s[1];
   require_once("JavaScriptUnpacker.php");
   require_once ("tear.php");
   $t1=explode("?",$filelink);
   $host="https://".parse_url($t1[0])['host'];

   //echo $filelink;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);

   //echo $h;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer : '.$filelink,
  'Connection: keep-alive');
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );
  //$context  = stream_context_create($options);
  //$h = @file_get_contents($filelink, false, $context);
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  preg_match_all("/eval\(function.*?\<\/script/s",$h,$m);
  for ($k=0;$k<count($m[0]);$k++) {
  $out .= $jsu->Unpack($m[0][$k]);
  }
  }
  //echo $out;
  if (preg_match("/file_code\:\'/",$out)) {
  $t1=explode("file_code:'",$out);
  $t2=explode("'",$t1[1]);
  $id=$t2[0];
  $t1=explode("hash:'",$out);
  $t2=explode("'",$t1[1]);
  $hash=$t2[0];
  $l=$host."/dl";
  $post="b=playerddl&file_code=".$id."&hash=".$hash;
  $post="b=stream_data&file_code=".$id."&hash=".$hash; //&c1_file=https://seriale-online.net/subtitrarifilme/tt18083578.vtt&c1_label=Romana";

  //echo $post;
  //$post="";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'Content-Cache: no-cache',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: '.$host,
  'Connection: keep-alive',
  'Referer: '.$host."/");
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'POST',
        'content' => $post,
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );

  //$context  = stream_context_create($options);
  //$h = @file_get_contents($l, false, $context);
  /////////////////////////////
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  /////////////////////////////
  $y=json_decode($h,1);
  //print_r ($y);
  if (isset($y[0]['file'])) {
  $src=$y[0]['file'];
  $seed=$y[0]['seed'];
  // {'0':'5','1':'6','2':'7','5':'0','6':'1','7':'2'}
  $out='var chars={"0":"5","1":"6","2":"7","5":"0","6":"1","7":"2"};';
  $out=str_replace("'",'"',$out);
  $t1=explode('var chars=',$out);
  $t2=explode(';',$t1[1]);
  $e="\$chars='".$t2[0]."';";
  eval ($e);
  $rep="/[012567]/";
  $x=json_decode($chars,1);
  $seed=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $seed
  );
  $link = decrypt($src,$seed);
  //$link=$y[0]['file'];
  //echo $link;
  $link=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $link
  );
  }
  } else {
/////////////////////////
  //echo $out;

  $out = $h." ".$out;
  //echo $out;
  //sources: [{file:"
  //sources:[{file:"
  if (preg_match("/sources\:\s*\[\{file\:\"([^\"]+)\"/",$out,$m))
    $link=$m[1];
  }
  //echo $link;
  //$link=$y[0]['file'];
   if ($link && $flash <> "flash") {
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'Origin: '.$host,
   'Connection: keep-alive',
   'Referer: '.$host."/");
   /*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   echo $h;
   */
   //$link=get_max_res($h,$link);
    $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
    $link .="&User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0");
   }


   // https://moon-storage-delivery-s05.filemoon.to/hls2/01/00021/re09uiwgwgve_x/master.m3u8?t=1QAuFako9vgEMFTW0IDiWSBeZNLnyWfM99gHRGnvK9k&s=1655205176&e=-100&f=107259&srv=127.0.0.1&asn=12302
   // https://moon-storage-delivery-s05.filemoon.to/hls2/01/00021/re09uiwgwgve_x/master.m3u8?t=sqqFodyexNeY4i-RgKmJBQTRPs6-Qp2n2qNkgIW_SSI&s=1655205189&e=43200&f=107259&srv=127.0.0.1&asn=12302
//} elseif (strpos($filelink,"videovard.") !== false) {
} elseif (preg_match("/videovard\./",$filelink)) {
//echo $filelink;
  // https://videovard.sx/e/m97g3y0q9xcs?c1_file=https://seriale-online.net/subtitrari/7482-5-2.vtt&c1_label=Romana
  $host="https://".parse_url($filelink)['host'];
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
    $srt="https:".$s[1];
  $id="";
   if (preg_match("/\/[ef]\/([0-9a-zA-Z\_\-]+)/",$filelink,$m)) {
   $id=$m[1];
   }
   if ($id) {
   include ("tear.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:90.0) Gecko/20100101 Firefox/90.0";
   //$ua = $_SERVER['HTTP_USER_AGENT'];
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Cookie: auth.strategy=cookie',
   'Referer: '.$host);
   $l=$host."/api/make/hash/".$id;
   //echo $l;
   /*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   echo $h;
   */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );
  $context  = stream_context_create($options);
  $h = @file_get_contents($l, false, $context);
  //echo $h;
   $x=json_decode($h,1);
   //print_r ($x);
   if (isset($x['hash'])) {
   $hash=$x['hash'];
   $l=$host."/api/player/setup";
   $q=array("cmd" => "get_stream",
      "file_code" => $id,
      "hash" => $hash);
   $post=http_build_query($q);
//$post="cmd=get_stream&file_code=vkv14eabmq7z&hash=830844-148-65-1635016625-bcfb720a4f47053d372564c0f3621182";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Length: '.strlen($post),
   'Origin: '.$host,
   'Connection: keep-alive',
   'Referer: '.$host);
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'POST',
        'content' => $post,
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );
  $context  = stream_context_create($options);
  $h = @file_get_contents($l, false, $context);
  $y=json_decode($h,1);
  //print_r ($y);
  if (isset($y['src'])) {
  $src=$y['src'];
  $seed=$y['seed'];
  $out='var chars={"0":"5","1":"6","2":"7","5":"0","6":"1","7":"2"};';
  $out=str_replace("'",'"',$out);
  $t1=explode('var chars=',$out);
  $t2=explode(';',$t1[1]);
  $e="\$chars='".$t2[0]."';";
  eval ($e);
  $rep="/[012567]/";
  $x=json_decode($chars,1);
  $seed=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $seed
  );
  $link = decrypt($src,$seed);
  $link=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $link
  );
  }
  }
  }
  $head=array('Origin: '.$host,
  'Referer: '.$host);


  /*
  $link="https://content-videovard-delivery-s27.videovard.to/hls2/01/00166/vkv14eabmq7z_n/index-v1-a1.m3u8?t=m6YopQkyqruQ0UxYTBTVKUr1aLkGb9FydB_tnd-OzMg&s=1635018699&e=720&f=830844&srv=127.0.0.1";
  $link="https://content-videovard-delivery-s27.videovard.to/hls2/01/00166/vkv14eabmq7z_n/index-v1-a1.m3u8?t=cUJrMNABq0zG2Tmr-agTZHLbvC4IREnUqGb4w0SdZpQ&s=1635018819&e=720&f=830844&srv=127.0.0.1";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
 */
  //$link="https://content-videovard-delivery-s27.videovard.to/hls2/01/00166/vkv14eabmq7z_n/seg-1-v1-a1.ts?t=NIaoh9--6y6x2aNkrIfRpsHWakue_9y5u95pbRpxCY4&s=1635018367&e=720&f=830844&srv=127.0.0.1";
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
  // https://videovard.sx/js/tear.js
  // https://videovard.sx/_nuxt/daa1d94.js
  // return r={0:"5",1:"6",2:"7",5:"0",6:"1",7:"2"},t.next=3,e.seed.replace(/[012567]/g


} elseif (strpos($filelink,"streamtape.") !== false) {
  // https://streamtape.com/e/Jq2V9jmvyrT9Ja
  //https://streamtape.com/e/jOdpLk1kBQhJ7v/tt0061389.mp4?c1_file=https://serialeonline.io/subtitrarifilme/tt0061389.vtt&c1_label=Romanahttps://serialeonline.io/subtitrarifilme/tt0061389.vtt
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:83.0) Gecko/20100101 Firefox/83.0";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  if (preg_match("/subtitle_json\=(.+)/",$filelink,$s)) {
    $srt_json=trim($s[1]);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt_json);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   //curl_setopt($ch,CURLOPT_REFERER,$filelink);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   //curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h1 = curl_exec($ch);
   curl_close($ch);
   //echo $h1;
   $s1=json_decode($h1,1);
   $srt_arr=array();
   for ($k=0;$k<count($s1);$k++) {
    if (preg_match("/(English|Romanian)\s*(\(verified\))?/",$s1[$k]['label'],$m)) $srt_arr[$m[0]]=$s1[$k]['src'];
   }
   if (isset($srt_arr['Romanian (verified)']))
    $srt=$srt_arr['Romanian (verified)'];
   elseif (isset($srt_arr['Romanian']))
    $srt=$srt_arr['Romanian'];
   elseif (isset($srt_arr['English (verified)']))
    $srt=$srt_arr['English (verified)'];
   elseif (isset($srt_arr['English']))
    $srt=$srt_arr['English'];
  } elseif (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s)) {
    $srt="https:".$s[1];
  }
  //$head=array('Cookie: _ym_uid=1589208140640745946; _ym_d=1589208140; _b=kube12; _ym_visorc_61426822=b; _ym_isad=2');
 $cookie=$base_cookie."streamtape.dat";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://serialeonline.io/episoade/magnum-p-i-sezonul-3-episodul-2/',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $filelink);
 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
 curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_USERAGENT, $ua);
 curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
 curl_setopt($ch, CURLOPT_ENCODING, "");
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
 curl_setopt($ch, CURLOPT_TIMEOUT, 25);
 curl_setopt($ch, CURLINFO_HEADER_OUT, true);
 $h = curl_exec($ch);
 $info = curl_getinfo($ch);
 curl_close($ch);
 //print_r ($info);
  $h=str_replace("\\","",$h);
  //echo $h;
  //$h=preg_replace("/(\'|\")\s*\+\s*(\'|\")/","",$h);
  //echo $h;
  //if (preg_match_all("/\?id\=([\w\_\&\=\-]+)[\'|\"|\<]/",$h,$m)) {
  //print_r ($m);
  // $link="https://streamtape.com/get_video?id=".$m[1][count($m[1])-1]."&stream=1";
  //}
  if (preg_match_all("/\(\'\w+\'\)\.innerHTML\s*\=\s*(.*?)\;/",$h,$m)) {
  //print_r ($m);
  $e1=$m[1][count($m[1])-1];
  $e1=str_replace("'",'"',$e1);
  $d=explode("+",$e1);
  $out="";
  for ($k=0;$k<count($d);$k++) {
   $s=trim($d[$k]);
   preg_match("/\(?\"([^\"]+)\"\)?(\.substring\((\d+)\))?(\.substring\((\d+)\))?/",$s,$p);
   //print_r ($p);
   //echo $s."\n";
   if (isset($p[3]) && isset($p[5]))
    $out .=substr(substr($p[1],$p[3]),$p[5]);
   elseif (isset($p[3]))
    $out .=substr($p[1],$p[3]);
   else
    $out .=$p[1];
  }
  $link=$out;
  /*
  if (preg_match_all("/\(\'\w+\'\)\.innerHTML\s*\=((\s*\(?[\'|\"]([^\'|\"]*)\)?[\'|\"]\s*\+\s*)+)\(?[\'|\"]([^\'|\"]+)[\'|\"]\)?\.substring\((\d+)\)(\.substring\((\d+)\))?/i",$h,$m)) {
   //$link=$m[1].substr($m[2],$m[3])."&stream=1";
   print_r ($m);
   $z=count($m[0])-1;
   //die();
   $rest=substr($m[4][$z],$m[5][$z]);
   $rest=substr($rest,$m[7][$z]);
   $e="\$link=".str_replace("+",".",$m[1][$z])."'".$rest."';";
   //echo $e;
   eval ($e);
   */
   $link .= "&stream=1";
   if ($link[0]=="/") $link="https:".$link;
  }
  //print_r ($m);
  //echo $h;
  // file_put_contents($base_cookie."st.txt",$filelink."\n".$h);
  // die();
  /*
  if ($srt) {
  //if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  //$srt="https:".$s[1];
  if (preg_match("/cors\"\:\"([\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=]+)\"/",$h,$m))
    $srt=$m[1];
    //echo $srt;
  }
  */
  //echo $srt;
  /*
  $t1=explode('div id="videolink',$h);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[1]);
  $t1=explode('document.getElementById("videolink").innerHTML = "',$h);
  $t3=explode('"',$t1[1]);
  $t1=explode("elem['innerHTML']='",$h);
  $t3=explode("'",$t1[1]);
  
  if (strpos($t3[0],"http") === false && $t3[0])
  $link="https:".$t3[0];
  else
  $link=$t3[0];
  if ($link) $link=$link."&stream=1";
  */
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  die();
  */
} elseif (preg_match("/vidb(o|e)?m\.com/",$filelink)) {
  // https://vidbm.com/embed-601enbg9vbn7.html
  // https://www.vidbem.com/embed-iuq5di0a4h66.html
  //echo $filelink;
  $host=parse_url($filelink)['host'];
  preg_match("/\.com\/(embed\-)?(\w+)/",$filelink,$m);
  $id=$m[2];
  //print_r ($m);
  $filelink="https://".$host."/".$id.".html";
  //echo $filelink;
  require_once ("AADecoder.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Cookie: glx_pp_8613_1974993464={"fl":1,"loaded_time":1591948692,"unload_time":1591948819}; file_id=2037415; aff=3975',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $jsu = new AADecoder();
  $out = $jsu->decode($h);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $out, $m))
  $link=$m[1];
} elseif (strpos($filelink,"samaup.co") !== false) {
  //echo $filelink;
  // https://www.samaup.co/embed-fa4lz4tlks5d.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $out, $m))
  $link=$m[1];
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode($filelink)."&Origin=".urlencode("https://www.samaup.co");
} elseif (strpos($filelink,"vidmoly.") !== false) {
  //https://vidmoly.to/embed-x92cku9tbojr.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
  $link=$m[1];
  //if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  //$srt="https:".$s[1];
  if (preg_match("/\/srt\/.+\.(srt|vtt)/",$h,$s))
    $srt="https://vidmoly.to".$s[0];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  if ($link && $flash <> "flash") {
   $link .="|Origin=".urlencode("https://vidmoly.to")."&Referer=".urlencode("https://vidmoly.to/");
  }
} elseif (strpos($filelink,"oogly.") !== false) {
  // https://oogly.io/embed-fbhc7jxnzod1.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
  $link=$m[1];
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
} elseif (strpos($filelink,"vup.to") !== false || strpos($filelink,"vupload.com") !== false) {
  // https://vup.to/embed-peat9pqubzyb.html
  // https://vup.to/embed-gvjx8wczsudh.html
  // https://vupload.com/embed-gvjx8wczsudh.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  require_once("JavaScriptUnpacker.php");
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  $h =$out.$h;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
  $link=$m[1];
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  //echo $srt;
  if (strpos($srt,"empty.srt") !== false) $srt="";
} elseif (strpos($filelink,"uqload.com") !== false) {
  // https://uqload.com/embed-pqsfjdem6sv6.html
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
  $link=$m[1];
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
} elseif (strpos($filelink,"arabramadan.") !== false) {
   // https://arabramadan.com/embed/n885aDWi80UbMzf/
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=unjuice($h);
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$x)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($x);
  }
  $links=array();
  if (preg_match_all("/src\":\"(.*?)\"\,\"label\":\"(\d+)P\"/i",$out,$s)) {
   $links = array_combine($s[2], $s[1]);
   if (isset($links['1080']))
    $link=$links['1080'];
   elseif (isset($links['720']))
    $link=$links['720'];
   elseif (isset($links['480']))
    $link=$links['480'];
   elseif (isset($links['360']))
    $link=$links['360'];
  }
  if (substr($link, -1) =="/") $link=substr($link, 0, -1);
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode($filelink);
//} elseif (strpos($filelink,"aflamyz.") !== false) {
} elseif (preg_match("/aflamyz\.|movs4u\.club/",$filelink)) {
  //echo $filelink;
  // https://aflamyz.net/embed/0nseL76vQcFBr/
  // https://movs4u.club/embed/adG3xsgSWDmX8/
  function decrypt($jsonStr, $passphrase)
    {
        $json = json_decode($jsonStr, true);
        $salt = hex2bin($json["s"]);
        $iv = hex2bin($json["iv"]);
        $ct = base64_decode($json["ct"]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = [];
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  if ($flash=="flash")
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $ua = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('data-en="',$h);
  $t2=explode('"',$t1[1]);
  $enc=urldecode($t2[0]);

  $t1=explode('data-p="',$h);
  $t2=explode('"',$t1[1]);
  $pass=$t2[0];
  $out = decrypt($enc,$pass);
  if ($out) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $out);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  $l1=$x['sources'][0]['file'];
  //echo $l1."\n";
  if (preg_match("/\.m3u81/",$l1)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$l1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$l1);
  } else {
   $link=$l1;
  }
  //https://sv2.ibrahimrashidacademy.com/sv/VG52eW8xVFdDM0R1NDlTVmp1SnZudEdWY3pqZXdLZ0loWllvSm1hVzNncz0=/HLS/weblink/media/1080p_0_1/QmdDdC8zanBMUVlmZzE%3D.m3u8?double_encode=1
  //https://sv2.ibrahimrashidacademy.com/sv/VG52eW8xVFdDM0R1NDlTVmp1SnZudEdWY3pqZXdLZ0loWllvSm1hVzNncz0=/HLS/weblink/media/1080p_0_1/QmdDdC8zanBMUVlmZzE%3D.ts?double_encode=1

  if ($link && $flash <> "flash")
    $link=$link."|Origin=".urlencode("https://aflamyz.net");
  }
} elseif (strpos($filelink,"youdbox.") !== false) {
  // https://youdbox.com/embed-m5xu5j7dcsiq-658x400.html
  // https://youdbox.net/embed-sqj3egw532z3.html
  // https://stog2.youdbox.net/d/jow6u2bfxm3qv3jsbgnmelmjhyng64ui3qxz2nroiyx4v6bzvpm67ys52d5gucia5lz57afv/video.mp4
function decode_code($code){
    return preg_replace_callback(
        "@\\\(x)?([0-9a-fA-Z]{2,3})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'ISO-8859-1', 'UTF-8');
        },
        $code
    );
}
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=decode_code($h);
  if (preg_match("@var\s*[^\s]+\s*=\s*(\[[^\]]+])@",$h,$m)) {
  $code = "\$a=".$m[1].";";
  $code=str_replace('"""','"\'"',$code);
  //echo $code;
  eval ($code);
  //print_r ($a);
  $x=strrev(join($a,""));
  //echo $x;
  if (preg_match('/source src\=\'(.*?)\'/', $x, $m))
    $link=$m[1];
  }
} elseif (strpos($filelink,"sendvid.") !== false) {
  // https://sendvid.com/embed/aoj09kac
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/source src\=\"(.*?)\"/', $h, $m))
    $link=$m[1];
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
//} elseif (strpos($filelink,"jawcloud.") !== false) {
} elseif (preg_match("/jawcloud\.|viphdvid\./",$filelink)) {
//echo $filelink;
  // https://viphdvid.com/embed-k0bhjtommps1.html?cap&c1_file=https://upvtt.com/uploads/The.Humanity.Bureau.2017.NORDiC.1080p.BluRay.x265-EGEN.sv.vtt&c1_label=Svenska&c2_file=https://upvtt.com/uploads/The.Humanity.Bureau.2017.NORDiC.1080p.BluRay.x265-EGEN.da.vtt&c2_label=Dansk&c3_file=https://upvtt.com/uploads/The.Humanity.Bureau.2017.NORDiC.1080p.BluRay.x265-EGEN.no.vtt&c3_label=Norsk&c4_file=https://upvtt.com/uploads/The.Humanity.Bureau.2017.NORDiC.1080p.BluRay.x265-EGEN.fi.vtt&c4_label=Suomi
  // https://jawcloud.co/embed-7ezp8ikxy7f8.html?cap&c1_file=https://upvtt.com/uploads/Blindspot-05x01-ICametoSleigh.POKE.English.C.orig.Addic7ed.com.vtt&c1_label=English
  $host=parse_url($filelink)['host'];
  $filelink=str_replace($host,"viphdvid.com",$filelink);
  $host="viphdvid.com";
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '."https://".$host,
  'Connection: keep-alive');
  $ch = curl_init($filelink);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $filelink;
  //echo $h;
  if (preg_match("/c\d?_file\=(http[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]+)\&c\d?_label\=English/i",$filelink,$s))
    $srt=$s[1];
  if (preg_match('/source src\=\"(.*?)\"/', $h, $m))
    $l=$m[1];
  if (strpos($l,".m3u8") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  // get max res
$base1=str_replace(strrchr($l, "/"),"/",$l);
$base2=getSiteHost($l);
if (preg_match("/\.m3u8/",$h)) {
$a1=explode("\n",$h);
for ($k=0;$k<count($a1);$k++) {
  if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
}
if ($pl[0][0] == "/")
  $base=$base2;
elseif (preg_match("/http(s)?:/",$pl[0]))
  $base="";
else
  $base=$base1;
if (count($pl) > 1) {
  if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
    preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
  else
    preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $link=$base.$pl[$key_max];
}
} else {
  $link=$l;
}
} else {
 $link=$l;
}
if ($link && $flash <> "flash")
  $link=$link."|Referer=".urlencode("https://".$host);
} elseif (strpos($filelink,"dogestream.") !== false) {
  // https://dogestream.me/player/index.php?data=d3802b1dc0d80d8a3c8ccc6ccc068e7c
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("\\","",$h);
  //echo $h;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
  $l=$m[1];
  if (strpos($l,".m3u8") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  // get max res
$base1=str_replace(strrchr($l, "/"),"/",$l);
$base2=getSiteHost($l);
if (preg_match("/\.m3u8/",$h)) {
$a1=explode("\n",$h);
for ($k=0;$k<count($a1);$k++) {
  if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
}
if ($pl[0][0] == "/")
  $base=$base2;
elseif (preg_match("/http(s)?:/",$pl[0]))
  $base="";
else
  $base=$base1;
if (count($pl) > 1) {
  if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
    preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
  else
    preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $link=$base.$pl[$key_max];
}
} else {
  $link=$l;
}
} else {
  $link=$l;
}
if ($link && $flash<>"flash")
  $link=$link."|Referer=".urlencode($filelink);
///////////////////////////////////////
} elseif (strpos($filelink,"uptostream.com") !== false) {
  // https://uptostream.com/iframe/dx8ksj9svoy0
  include ("obfJS.php");
  $t1=explode("iframe/",$filelink);
  $id=$t1[1];
  $l="https://uptostream.com/api/streaming/source/get?token=null&file_code=".$id;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://uptostream.com/iframe/'.$id.'',
  'Cookie: video='.$id);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  $enc=$r['data']['sources'];
  $x=obfJS();
  $t1=explode('setTheLink(){',$x);
  $h= $t1[1];
  preg_match("/https\:\/\/www\d+\.uptostream\.com/",$h,$m);
  preg_match("/\'(360|480|720|1080)\'/",$h,$n);
  preg_match("/\'([a-zA-Z0-9]{11})\'/",$h,$p);
  if ($m[0] && $n[1] && $p[1])
  $link=$m[0]."/".$p[1]."/".$n[1]."/0/video.mp4";
//} elseif (strpos($filelink,"dood.") !== false) {
} elseif (preg_match($dood,$filelink)) {
  // https://www.doodstream.com/d/sot4bb1da0rq
  //echo $filelink;
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  //$ua="Mozilla/5.0";
  //$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36";
  $host=parse_url($filelink)['host'];
  $srt="";

  //https://dood.watch/e/gd93oog2e3vq?c1_file=https://serialeonline.to/subtitrarifilme/tt4619908.vtt&c1_label=Romana
  function makePlay() {
   $a="";
   $t = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
   $n = strlen($t) - 1;
   for ($o = 0; 10>$o; $o++) {
    $a .= $t[rand(0,$n)];
   }
   return $a;
  }
  $filelink=str_replace("/f/","/e/",$filelink);
  $filelink=str_replace("/d/","/e/",$filelink);
  //echo $filelink;
  /* prevent cloudflare captcha (PHP 7.x > */
 $DEFAULT_CIPHERS =array(
            "ECDHE+AESGCM",
            "ECDHE+CHACHA20",
            "DHE+AESGCM",
            "DHE+CHACHA20",
            "ECDH+AESGCM",
            "DH+AESGCM",
            "ECDH+AES",
            "DH+AES",
            "RSA+AESGCM",
            "RSA+AES",
            "!aNULL",
            "!eNULL",
            "!MD5",
            "!DSS",
            "!ECDHE+SHA",
            "!AES128-SHA",
            "!DHE"
        );
 if (defined('CURL_SSLVERSION_TLSv1_3'))
  $ssl_version=7;
 else
  $ssl_version=0;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  //$ua="Mozilla/5.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match_all("/location\:\s+(http.+)/i",$h,$m)) {
    $filelink=trim($m[1][count($m[1])-1]);
    $host=parse_url(trim($m[1][count($m[1])-1]))['host'];
  }
  //echo $h;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  //echo $srt."\n";
  if (preg_match("/pass_md5/",$h)) {
  $t1=explode("pass_md5/",$h);
  $t2=explode("'",$t1[1]);
  //$l="https://".$host.$t2[0].$token;
  //$host="dood.to";
  $l="https://".$host."/pass_md5/".$t2[0];
  //echo $l;
  $t1=explode('token=',$h);
  $t2=explode('&',$t1[1]);
  $tok=$t2[0];
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: '.$filelink);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
 // curl_setopt($ch, CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h1 = curl_exec($ch);
  curl_close($ch);

  //echo $h1;
  //die();
  if (preg_match("/http/",$h1) && substr($h1, 0, 4)=="http")
   $link=$h1."?token=".$tok."&expiry=".(time()*1000);
  else
   $link="";
  } else {
   $link="";
  }
  //$link="https://mir44lo.dood.video/hls/u5kj7c2tf3hlsdgge7ygeoshiv7zu7b2nlcy7ig6sfirx4dzevc2ltmeie5q/master.m3u8";
   if ($flash <> "flash" && $link) $link =$link."|Referer=".urlencode("https://".$host."/")."&User-Agent=".urlencode($ua);
} elseif (strpos($filelink,"movcloud.net") !== false) {
  // https://movcloud.net/embed/ns-9qmdfjZfB
  //echo $filelink;
  if (preg_match("/\/embed\/([a-zA-Z0-9_\-]+)/",$filelink,$m)) {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
  $srt=$s[1];
   $id=$m[1];
   $l="https://api.movcloud.net/stream/".$id;
   //echo $l;
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0',
   'Accept: application/json',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/json',
   'Origin: https://movcloud.net');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  //print_r ($r);
  $link=$r['data']['sources'][0]['file'];
  if (strpos($link,"http") !== false && $flash <> "flash")
   $link=$link."|Origin=".urlencode("https://movcloud.net");
  }
} elseif (strpos($filelink,"supervideo.tv") !== false) {
  // https://supervideo.tv/e/ekymi52ok8s8
  $filelink=str_replace("/f/","/e",$filelink);
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"supervideo.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"clicknupload.") !== false) {
  //echo $filelink;
  // https://www.clicknupload.co/rhqqbz328ejz
  $pattern = '@(clicknupload\.(?:com|me|link|co))/(?:f/)?([0-9A-Za-z]+)@';
  preg_match($pattern,$filelink,$m);
  $id=$m[2];
  $ua='Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0';
  $l="https://www.clicknupload.co/".$id;
  $post="op=download2&id=".$id."&rand=&referer=https://www.clicknupload.co/".$id."&method_free=Free+Download+>>&method_premium=&adblock_detected=0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded',
   'Content-Length: '.strlen($post).'',
   'Origin: https://www.clicknupload.co',
   'Connection: keep-alive',
   'Referer: https://www.clicknupload.co/'.$id);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $pat='@class\=\"downloadbtn\"[^>]+onClick\s*\=\s*\"window\.open\(\'([^\']+)@';
  if (preg_match($pat,$h,$r))
   $link=$r[1];
} elseif (strpos($filelink,"abcvideo.") !== false) {
  // https://abcvideo.cc/5x1yjkc56c39.html
  // https://abcvideo.cc/embed-5x1yjkc56c39.html
  //$filelink="https://abcvideo.cc/embed-5x1yjkc56c39.html";
//echo $filelink;
  preg_match("/abcvideo\.cc\/(embed\-)?(\w+)/",$filelink,$a);
  $id=$a[2];
  include ("rec.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
$ua = $_SERVER['HTTP_USER_AGENT'];
$key="6LcOeuUUAAAAANS5Gb3oKwWkBjOdMXxqbj_2cPCy";
$co="aHR0cHM6Ly9hYmN2aWRlby5jYzo0NDM.";
$sa="homepage";
$token=rec($key,$co,$sa,"https://abcvideo.com");
$l="https://abcvideo.cc/dl?op=video_src&file_code=".$id."&g-recaptcha-response=".$token;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: '.$filelink);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
$s=json_decode($h,1);

  $r=array();
  for ($k=0;$k<count($s);$k++) {
   $r[$s[$k]['label']]=$s[$k]['file'];
  }
  if (isset($r["HD"]))
    $link=$r["HD"];
  elseif (isset($r["SD"]))
    $link=$r["SD"];
  else
    $link="";

} elseif (strpos($filelink,"clipot.tv") !== false) {
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('sources"',$h);
  if (preg_match("/file[\"|\']\:\s*[\"|\'](.*?)[\"|\']/",$t1[1],$m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))\"\s*\,\s*\"label\"\:\"\w+\"\s*\,\s*\"kind\"\:\"captions\"/si', $h, $xx)) {
        $srt = $xx[1];
        //print_r ($xx);
    if (strpos($srt,"http") === false && $srt)
        $srt = "https:" . $srt;
        if (strpos($srt,"empty") !== false) $srt="";
    }
   }
} elseif (strpos($filelink,"komlom.com") !== false) {
  // https://komlom.com/player/xgx3CXjQp7Dik7J/
  //echo $filelink;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //https://deacon.komlom.com/U1e8cRzcFv0rnQ8mCwE7udTbacStIonIKlOOxntSZU3OQjrnKxHejSv2tbIu2UUlCjh4NVFufLtwqr_YKLjZ2A/uOnu1f31uO9-U6qjOHzhkuAt7ohSk8OdlQJsWoLrsI4/video.m3u8
  //https://deacon.komlom.com/U1e8cRzcFv0rnQ8mCwE7udTbacStIonIKlOOxntSZU3OQjrnKxHejSv2tbIu2UUlCjh4NVFufLtwqr_YKLjZ2A/uOnu1f31uO9-U6qjOHzhkuAt7ohSk8OdlQJsWoLrsI4/video.m3u8
  if (preg_match('/((https?)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))\"\s*\,\s*\"label\"\:\"\w+\"\s*\,\s*\"kind\"\:\"captions\"/si', $h, $xx)) {
        $srt = $xx[1];
        //print_r ($xx);
    if (strpos($srt,"http") === false && $srt)
        $srt = "https:" . $srt;
        if (strpos($srt,"empty") !== false) $srt="";
    }
    //echo $srt;
    $head=array('Origin: https://komlom.com');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch,CURLOPT_REFERER, "https://komlom.com");
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
$base1=str_replace(strrchr($link, "/"),"/",$link);
$base2=getSiteHost($link);
if (preg_match("/\.m3u8/",$h)) {
$a1=explode("\n",$h);
for ($k=0;$k<count($a1);$k++) {
  if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
}
if ($pl[0][0] == "/")
  $base=$base2;
elseif (preg_match("/http(s)?:/",$pl[0]))
  $base="";
else
  $base=$base1;
if (count($pl) > 0) {
  if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
    preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
  else
    preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $link=$base.$pl[$key_max];
}
}
    if ($flash <> "flash")
     $link=$link."|Origin=".urlencode("https://komlom.com")."&Referer=".urlencode($filelink);
  } else
  $link="";
} elseif (strpos($filelink,"embed.meomeo.pw") !== false) {
//echo $filelink;
  // https://embed.meomeo.pw/fastmedia/tt14412366
  $head=array('Origin: https://embed.meomeo.pw',
  'Cookie: cf_chl_2=6edf9ba66989eac; cf_chl_prog=x12;');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://vumoo.to");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/((https?)?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx)) {
        $srt = $xx[1];
    if (strpos($srt,"http") === false && $srt)
        $srt = "https:" . $srt;
    }
  } else
  $link="";
} elseif (strpos($filelink,"eplayvid.") !== false) {
  // http://eplayvid.com/watch/170bf88ed18a3bc
  // https://eplayvid.net/watch/b02cdc6afdbff36
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/src\=\"((https?)?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
   if ($link && $flash !="flash")
     $link=$link."|Referer=".urlencode($filelink);
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx)) {
        $srt = $xx[1];
    if (strpos($srt, "http") === false)
        $srt = "http:" . $srt;
    }
  } else
  $link="";
} elseif (strpos($filelink,"fmoviesfree.org") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://fmoviesfree.org");
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
  $link=$m[1];
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx)) {
        $srt = $xx[1];
    if (strpos($srt, "http") === false && $srt)
        $srt = "https:" . $srt;
    }
  } else
  $link="";
} elseif (strpos($filelink,"hlsplay.com") !== false) {
  //https://hlsplay.com/e/EIOyhiONJktv
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://play.voxzer.org");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m))
  $link=$m[1];
  else
  $link="";
} elseif (strpos($filelink,"vidfast.co") !== false) {
  //https://go.vidfast.co/embed-5chhcimx6whs.html
  //https://sp.vidfast.co/embed-5chhcimx6whs.html
  //echo $filelink;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
  $link=$m[1];
  $t1=explode('tracks:',$h);
  $h=$t1[1];
  if (preg_match('/file\: \"(.+(srt|vtt))/', $h, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
   if ($srt && strpos($srt,"http") === false) $srt="https://go.vidfast.co".$srt;
  if ($link && preg_match("/\.m3u8/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $link=get_max_res($h,$link);
  }
} elseif (strpos($filelink,"vidsrc.me") !== false) {
//echo $filelink;
//$filelink="https://vidsrc.me/embed/tt1300854/";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
//echo $h;
  $t1=explode('iframe src="',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  if (strpos($l,"http") === false && $l) $l="https://vidsrc.me".$l;
  if ($l) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

  $t1=explode('var query = "',$h);
  $t2=explode('"',$t1[1]);
  $q=$t2[0];
  $l1="https://vidsrc.me/watching".$q;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/location:\s*(\S+)/i",$h,$n);
  preg_match("/v\/([\w\-]*)/",$n[1],$m);
  $id=$m[1];
  $url="https://www.vidsource.me/api/source/".$id;
  $data = array('r' => $l,'d' => 'www.vidsource.me');
  $options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
  );

  $context  = stream_context_create($options);
  $h = @file_get_contents($url, false, $context);
  $r=json_decode($h,1);
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://www.vidsource.me/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $link="https://www.vidsource.me".$r["data"][$c-1]["file"];
  else
   $link=$r["data"][$c-1]["file"];
  }
  //https://uzcuwuvr3.michel-clevenger.xyz/LUOu7cpuSqAKlcjblznYD8b9Wtp5DdmPWtXY7SeyLSetLSsK7I/0/playlist.m3u8
} elseif (strpos($filelink,"dl.movie4k.ag") !== false) {
   $link=$filelink;
   //$link="https://dl.movie4k.ag/files/tvepisodes/720p/1479111-e5.mp4";
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0";
   $ad="|Cookie=".urlencode("approve=1");
   if ($flash <> "flash")
     $link=$link.$ad;
} elseif (strpos($filelink,"cdn.movie4k.ag") !== false) {
   //echo $filelink;
   $ua = $_SERVER['HTTP_USER_AGENT'];
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt ($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   $h = curl_exec($ch);
   curl_close($ch);
   if (preg_match("/Location:\s*(.+\.mp4)/i",$h,$m)) {
   $link=$m[1];
   if ($link && strpos($link,"http") === false) $link="https:".$link;
   } else
     $link="";
   $ad="|Cookie=".urlencode("approve=1");
   if ($flash <> "flash")
     $link=$link.$ad;
} elseif (strpos($filelink,"cloud.vidhubstr.org") !== false) {
  set_time_limit(60);
  $out="";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $x=parse_url($filelink)['query'];
  parse_str($x, $output);
  $id=$output['id'];
  $l="https://cloud.vidhubstr.org/getHost/".$id;
  $post="";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt ($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $l=base64_decode($h);
  if (preg_match("/http/",$l)) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt ($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_HEADER, 1);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   $h = curl_exec($ch);
   curl_close($ch);
   if (preg_match("/Location:\s?(\S+)/i",$h,$m))
    $l=$m[1];
   $new_host=parse_url($l)['host'];
   $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Origin: https://cloud.vidhubstr.org',
   'Referer: https://cloud.vidhubstr.org',
   'Connection: keep-alive');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt ($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   if (preg_match("/drive.*?\.m3u8/",$h,$m) && $flash <> "flash") {
    $origin="https://".$new_host;
    $link="https://".$new_host."/".$m[0]."|Referer=".urlencode($origin)."&Origin=".urlencode($origin);
   }
   if (preg_match("/drive.*?\.m3u8/",$h,$m) && $flash == "flash") {
    $l="https://".$new_host."/".$m[0];
    curl_setopt($ch, CURLOPT_URL, $l);
    $h = curl_exec($ch);
    $n=explode("\n",$h);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    for ($k=0;$k<count($n);$k++) {
     if (strpos($n[$k],"#") !== false)
      $out .=$n[$k]."\n";
     else {
      $l="https://".$new_host.trim($n[$k]);
      curl_setopt($ch, CURLOPT_URL,$l);
      $x = curl_exec($ch);
      preg_match("/Location:\s+(\S+)/i",$x,$y);
      $z=$y[1];
      $out .=$z."\n";
     }
    }
   }
   if (isset($output['sub']))
    $srt=$output['sub'];
   if (isset($output['vlsub'])) {
    $l="https://sub.vidhubstr.org/getSubObj?name=".$output['vlsub'];
    curl_setopt($ch, CURLOPT_NOBODY,0);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_URL,$l);
    $h = curl_exec($ch);
    $x=json_decode($h,1);
    $srt=$x[0]['file'];
   }
  }
  curl_close($ch);

  if ($flash == "flash") {
  file_put_contents("lava.m3u8",$out);
  $p = dirname($_SERVER['HTTP_REFERER']);
  $link = $p."/lava.m3u8";
  } else {
  $link1 = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
} elseif (strpos($filelink,"hdv.fun") !== false) {
  // https://hls.hdv.fun/imdb/tt0097758
  $t1=explode("&referer=",$filelink);
  //echo $filelink;
  $filelink=$t1[0];
  $ref=$t1[1];
  $link=$filelink;
  //$link="https://hls.hdv.fun/imdb/tt2165859";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location:\s*(http.+)/i",$h,$m))
   $link = trim($m[1]);

  $head=array('Origin: '.$ref);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  //curl_setopt($ch, CURLOPT_REFERER,$ref);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  // https://webseed.hdv.fun
  // rr1.srtaemyl.icu
  /*
  $h=preg_replace_callback(
    "/http.+/",  // "" + num
    function ($m) {
      $t1=explode("url=",$m[0]);
      $host=parse_url($t1[1])['host'];
      $x=str_replace($host,"webseed.hdv.fun",$t1[1]);
      return $x;
    },
    $h
  );
  */
  //$h=preg_replace("/http.+/","redirect.php?file="."\$0",$h);
  //echo $h;
  //die();
  file_put_contents("lava.m3u8",$h);

  if ($flash == "flash") {

  //$p = dirname($_SERVER['HTTP_REFERER']);
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }

  if ($link && $flash <> "flash")
   $link=$link."|Origin=".urlencode($ref);
  //echo $link;
  //$h=file_get_contents($link);  //#EXT-X-BYTERANGE:2685580@3072 ??????
  //echo $h;
} elseif (strpos($filelink,"prettyfast.to") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $cookie=$base_cookie."ffmovies.dat";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://ffmovies.to/film/rabid.v9q92/52zlr60',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("hlsUrl = '",$h);
  $t2=explode("'",$t1[1]);
  $link=$t2[0];
  $t1=explode("sub=",$filelink);
  $srt=urldecode($t1[1]);
//} elseif (strpos($filelink,"mcloud") !== false) {
} elseif (preg_match("/(mcloud|vidstream|vizcloud|vidplay|vid41c|vid30c)\./",$filelink)) {
  $dr=$_SERVER['DOCUMENT_ROOT'];
  if (preg_match("/\/(?:f|e|embed)\/([a-z0-9]+)/i",$filelink,$m))
  $id=$m[1];

  if (file_exists($dr."/e/".$id)) {
  $list = glob($dr."/e/".$id."/*.*");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
   }
  rmdir($dr."/e/".$id);
  }

  $host="https://".parse_url($filelink)['host'];
  //echo $host."\n";
  //echo $filelink;
  //die();
  if (preg_match("/sub\.file\=/",$filelink)) {
  $t1=explode("sub.file=",$filelink);
  $t2=explode("&",$t1[1]);
  $srt=urldecode($t2[0]);
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;
  }
  if (preg_match("/sub\.info\=/",$filelink)) {
   $t1=explode("sub.info=",$filelink);
   $l1=urldecode($t1[1]);
   $t1=explode("&",$l1);
   //echo $l1;
  //https://bflix.to/ajax/episode/subtitles/11951
  $host1="https://bflix.to";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: '.$host1,
  'Connection: keep-alive',
  'Referer: '.$host1.'/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($t1[0]));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $s=json_decode($h,1);
  //print_r ($s);
  $srt="";
  for ($k=0;$k<count($s);$k++) {
   if (preg_match("/romanian/i",$s[$k]['label'])) {
    $srt=$s[$k]['file'];
    break;
   }
  }
  if (!$srt) {
  for ($k=0;$k<count($s);$k++) {
   if (preg_match("/english/i",$s[$k]['label'])) {
    $srt=$s[$k]['file'];
    break;
   }
  }
  }
  }
//echo $srt;
//die();

 $file="vid.txt";
 if (file_exists("vid.txt")) {
  $h=file_get_contents("vid.txt");
  //unlink ("vid.txt");
  $r=json_decode($h,1);
  //print_r ($r);
  if (isset($r['result']['sources'][0]['file']))
    $link=$r['result']['sources'][0]['file'];
  else
    $link="";
 }
  if ($link && $flash != "flash1") {
   $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0";
   //$link=str_replace("#.mp4","",$link);
   $head=array("Origin: ".$host);
   //print_r ($head);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch,CURLOPT_REFERER,$host);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   //die();
   $link=get_max_res($h,$link);
   //echo $link;
   //die();
      if ($flash == "flash" && preg_match("/mcloud|vidstream|vizcloud|vidplay/",$host)) {
      $t1=explode("?",$_SERVER['HTTP_REFERER']);
      $p=dirname($t1[0]);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      //die();
      $dd=dirname($link)."/";
      //preg_match_all("/^(?!#).+/m",$h,$m);
      //print_r ($m);
      $out=preg_replace_callback(
      "/^(?!#).+/m",
      function ($m) {
        global $dd;
        return "vvv.php?file=".urlencode($dd.$m[0]);
      //return $m[0];
      },
      $h
      );
      //echo $out;
      file_put_contents("lava.m3u8",$out);
      $link = $p."/lava.m3u8";
      }
   if ($link && $flash <> "flash")
     $link=$link."|Referer=".urlencode($host."/")."&Origin=".urlencode($host); //."&User-Agent=".urlencode($ua);
  }
} elseif (preg_match("/hls\d+x\.vidcloud9\.com/",$filelink)) {
  $link=$filelink;
  $filelink="";
  if ($link && $flash != "flash") {
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
   $head=array("Origin: https://vidcloud9.com");
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch,CURLOPT_REFERER,"https://vidcloud9.com");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $link=get_max_res($h,$link);
  }
  if ($link && $flash != "flash")
  $link=$link."|Referer=".urlencode("https://vidcloud9.com")."&Origin=".urlencode("https://vidcloud9.com");
} elseif (preg_match("/m\d+x?\.vidcloud9\.com/",$filelink)) {
  $link=$filelink;
  $filelink="";
  if ($link && $flash != "flash") {
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
   $head=array("Origin: https://vidcloud9.com");
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch,CURLOPT_REFERER,"https://vidcloud9.com");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $link=get_max_res($h,$link);
  }
  if ($link && $flash != "flash")
  $link=$link."|Referer=".urlencode("https://vidcloud9.com")."&Origin=".urlencode("https://vidcloud9.com");
//} elseif (preg_match("/vidnext\.net|vidnode\.net|vidembed\.net|vidembed\.cc/",$filelink) || preg_match("/\/vidcloud\d+/",$filelink)) {

} elseif (strpos($filelink,"evoload.") !== false) {
  // https://evoload.io/e/wEZkuDhnkURe5j
  // https://evoload.io/e/XZcWJvLlT9LzBD
  //$filelink="https://evoload.io/e/dUZJu6qjQQsqZ3";
  //echo $filelink;
  $filelink=str_replace("/f/","/e/",$filelink);
  if (preg_match("/\/e\/(\w+)/",$filelink,$m))
   $code=$m[1];
  else
   $code="";
  //include ("rec.php");
  $cookie=$base_cookie."evoload.dat";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://evoload.io");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt="https:".$s[1];
  if (preg_match("/captcha\_pass\"\s+value\=\"([^\"]+)\"/",$h,$m)) {
   $pass=$m[1];
   $l="https://csrv.evosrv.com/captcha";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://evoload.io',
   'Connection: keep-alive',
   'Referer: https://evoload.io/');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $csrv_token = curl_exec($ch);
   curl_close($ch);
   // {"code":"1nT1LqinMiOTj3","token":"ok","csrv_token":"xoh068m1fqn","pass":"7dczpuzsmak","reff":""}
   $a=array(
   'code' => $code,
   'token' => 'ok',
   'csrv_token' => $csrv_token,
   'pass' => $pass,
   "reff" => '');
   $post=json_encode($a);
   $l="https://evoload.io/SecurePlayer";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/json;charset=utf-8',
   'Content-Length: '.strlen($post),
   'Origin: https://evoload.io',
   'Connection: keep-alive',
   'Referer: '.$filelink);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);

   if (isset ($r['subtitles'])) {
    $srt = $r['subtitles'][0]['system_name'];
   }
   if (isset($r['stream'])) {
    if (isset($r['stream']['backup']))
     $link=$r['stream']['backup'];
    elseif (isset($r['stream']['src']))
     $link=$r['stream']['src'];
   }
  }
  //}

} elseif (preg_match("/vidnext\.net|vidnode\.net|vidembed\.(net|cc|io)|\/vidcloud9\.|membed\d?\.(net|com)/",$filelink)) {
  //$filelink=str_replace("streaming.php","load.php",$filelink);
  // m1x.vidcloud9.com
  // vidnext.net
  // vidnode.net
  require_once("aes.php");
  //echo $filelink."\n";
  $t1=explode("&",$filelink);
  $rest=$t1[1];
  $x=parse_url($filelink);
  //print_r ($x);
  $host=$x['host'];
  parse_str($x['query'],$y);
  //print_r ($y);
  $id=$y['id'];
  $id1=$id;
  unset($y['id']);
  $q=http_build_query($y);
  //echo $q;
  $key = '25746538592938496764662879833288';
  // 25746538592938496764662879833288
  $iv="9668655035439756";
  $iv="5641039825516312";
  //$iv="39323235363739303833393631383538";
  $key="25742532592138496744665879883281";   // 52+6+8
  $iv="9225679083961858";   //51+29+46
  $aes = new Aes($key, 'CBC', $iv);
  $out="";
  for ($k=0;$k<strlen($id);$k++) {
   $out .="%08";
  }
  $id = $id.$out;
  $e=urldecode($id);

  $y = $aes->encrypt($e);
  $enc=base64_encode($y);
  $l="https://".$host."/encrypt-ajax.php?id=".$zz."&refer=none&time=".time()."000";
  $l="https://vidembed.io/encrypt-ajax.php?id=".$enc."&".$t1[1]."&c=aaaaaaaa&refer=none&time=52564103982551631204&alias=".$id1;
  //echo $l;
  // GGb6Kn4MF0oqy1e0yLUTog==
  // nYTDUBPorEBTDstU7jG9Kg==
  $l="https://membed.net/encrypt-ajax.php?id=".$enc."&".$q."&c=aaaaaaaa&refer=https://vidembed.cc&alias=".$id1;
  //echo $l."\n".$filelink."\n";
  //echo $l."\n";
  // https://membed.net/encrypt-ajax.php?id=WOjl2iz6rcs9eQzt4sgTqQ==&title=A+Magical+Christmas+Village&typesub=SUB&sub=&cover=Y292ZXIvYS1tYWdpY2FsLWNocmlzdG1hcy12aWxsYWdlLnBuZw==&c=aaaaaaaa&refer=none&alias=MzcxNjAz
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://'.$host.'/',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;

  $r=json_decode($h2,1);
  $x=$r['data'];
  $x=base64_decode($x);
  //echo $x."\n";
  $out="";
  for ($k=0;$k<strlen($x);$k++) {
   $out .="%08";
  }
  //$x = $x.$out;
  //$x=urldecode($x);

  //echo $x."\n";
  $y = $aes->decrypt($x);
  $y = preg_replace('/[[:^print:]]/', '', $y);
  //echo $y."\n";
  //echo urlencode($y);
  $r=json_decode($y,1);
  //print_r ($r);
  //die();
  if (isset($r['source'][0]['file'])) {
   $c=count($r['source'])-1;
   if (preg_match("/auto/i",$r['source'][$c]['label']) && $c>1) $c=$c-1;
   $link= $r['source'][$c]['file'];
  }
  if ($link && preg_match("/\.m3u8/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
  //echo $h;
  //die();
  }

if (isset($r['track']['tracks']['file']))
   $srt=$r['track']['tracks']['file'];
elseif (isset($r['track']['tracks'][0]['file']))
   $srt=$r['track']['tracks'][0]['file'];


//////////////////////////////////////////////
     if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode("https://".$host."")."&Origin=".urlencode("https://".$host."");

/////////////////////////////////////////
} elseif (preg_match("/m\d+\.vidcloud(png|file)\.com/",$filelink)) {
  preg_match("/id\=(\w+)/",$filelink,$m);
  $id=$m[1];
  $host=parse_url($filelink)['host'];
  $link="https://".$host."/playlist/".$id."/".time().".m3u8";
} elseif (preg_match("/slave\d+\.vidcloud(png|file)\.com/",$filelink)) {
  $link=$filelink;
} elseif (strpos($filelink,"lavacdn.xyz") !== false) {
  //https://watch.lavacdn.xyz/viphd/0c603259c7331f30/1570352922/03875d88b4d3b0cf5ed017c528f4e7ef
  $t1=explode("?",$filelink);
  //$filelink=$t1[0];
  //$filelink="https://watch.lavacdn.xyz/viphd/0c603259c7331f30/1570353750/ea4e944c85c050fc5675fb1cf5c3f2a5";
  //echo $filelink;
  //echo $filelink;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://vipmovies.to");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  $h = curl_exec($ch);
  curl_close($ch);

  //echo $h;
  //$h=file_get_contents($filelink);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
  $srt=$s[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
   $link=$m[1];
  else
   $link="";

  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $link);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  $x = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/http.*?\.m3u8/",$x,$m))
    $link=$m[0];

  //if ($flash <> "flash") {
  $ch = curl_init($m[0]);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER, $link);
  //curl_setopt($ch, CURLOPT_REFERER, "https://vipmovies.to");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  $x = curl_exec($ch);
  curl_close($ch);
  $out="";
  $n=explode("\n",$x);

  //for ($k=0;$k<count($m[0]);$k++) {
  for ($k=0;$k<count($n);$k++) {
  if ($n[$k] <> "") {
  if (strpos($n[$k],"http") === false)
   $out .=$n[$k]."\n";
  else {
  $l="hserver.php?file=".base64_encode("link=".urlencode(trim($n[$k]))."&origin=".urlencode("htts://vipmovies"));

  $out .=$l."\n";
  }
  }
  }

  file_put_contents("lava.m3u8",$out);
  $link=$hash_path."/lava.m3u8";
  //}

} elseif (strpos($filelink,"daclips.") !== false || strpos($filelink,"movpod.") !== false) {
  //https://movpod.in/9hhueiilr5kb
  //https://movpod.in/c2b3k9wa9ysj
  //http://daclips.in/ulmwt4acqp4n
  $pattern = '/((daclips|movpod)\.(?:in|com|net))\/(?:embed-)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  $url = parse_url($filelink);
  $filelink="https://".$url["host"]."/".$m[3];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  $h = curl_exec($ch);
  curl_close($ch);

  $id=str_between($h,'"id" value="','"');
  $fname=str_between($h,'"fname" value="','"');
  $post="op=download1&usr_login=&id=".$id."&fname=".$fname."&referer=&channel=&method_free=Free+Download";
  sleep(5);
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: gzip, deflate, br',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Cookie: __test'
);
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m))
   $link=$m[1];
  else
   $link="";
} elseif (strpos($filelink,"mangovideo") !== false) {
  $filelink=str_replace("mangovideo.club","mangovideo.pw",$filelink);
  $host=parse_url($filelink)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
if (strpos($html,"license_code:") !== false) {
$t1 = explode("license_code: '", $html);
$t2 = explode("'", $t1[1]);
$d = $t2[0];
$t1 = explode("function/0/", $html);
$t2 = explode("'", $t1[count($t1)-1]);
$orig = $t2[0];
$c = 16;

for ($f = "", $g = 1; $g < strlen($d); $g++)
	{
	$f.= preg_match("/[1-9]/", $d[$g]) ? $d[$g] : 1;
	}

for ($j = intval(strlen($f) / 2) , $k = substr($f, 0, $j + 1) , $l = substr($f, $j) , $g = $l - $k, $g < 0 && ($g = - $g) , $f = $g, $g = $k - $l, $g < 0 && ($g = - $g) , $f+= $g, $f = $f * 2, $f = "" . $f, $i = $c / 2 + 2, $m = "", $g = 0; $g < $j + 1; $g++)
	{
	for ($h = 1; $h <= 4; $h++)
		{
		$n = $d[$g + $h] + $f[$g];
		$n >= $i && ($n-= $i);
		$m.= $n;
		}
	}

$t1 = explode("/", $orig);
$j = $t1[5];
$h = substr($j, 0, 32);
$i = $m;

for ($j = $h, $k = strlen($h) - 1; $k >= 0; $k--)
	{
	for ($l = $k, $m = $k; $m < strlen($i); $m++) $l+= $i[$m];
	for (; $l >= strlen($h);) $l-= strlen($h);
	for ($n = "", $o = 0; $o < strlen($h); $o++)
		{
		$n.= $o == $k ? $h[$l] : ($o == $l ? $h[$k] : $h[$o]);
		}

	$h = $n;
	}

$link = str_replace($j, $h, $orig);
} else {
 $link="";
}
 if ($link && $flash <> "flash")
  $link=$link."|Origin=".urlencode("https://".$host);
} elseif (strpos($filelink,"unlimitedpeer.ru") !== false) {
  $filelink=str_replace("=","%3D",$filelink);
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("urlVideo = '",$h);
  if (isset($t1[1])) {
   $t2=explode("'",$t1[1]);
   $link=$t2[0];
  } else
   $link="";
} elseif (strpos($filelink,"megaxfer.ru") !== false) {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://www1.moviesjoy.net/watch-movie/christmas-a-la-mode-58758.812876',
'Cookie: __cfduid=d38356d4788ac2fcb1ccc2459ffcc1fc11574327214',
'Upgrade-Insecure-Requests: 1');
//echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.m3u8))/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
///////////////////// max rez ///////////////
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://embed.megaxfer.ru");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

/////////////////////////////////// max rez
$base1=str_replace(strrchr($link, "/"),"/",$link);
$base2=getSiteHost($link);
if (preg_match("/\.m3u8/",$h)) {
$pl=array();
if (preg_match_all ("/^(?!#).+/m",$h,$m))
$pl=$m[0];
/*
for ($k=0;$k<count($a1);$k++) {
  if ($a1[$k][0] !="#" && $a1[$k]) $pl[]=trim($a1[$k]);
}
*/
if ($pl[0][0] == "/")
  $base=$base2;
elseif (preg_match("/http(s)?:/",$pl[0]))
  $base="";
else
  $base=$base1;
if (count($pl) > 1) {
  if (preg_match_all("/RESOLUTION\=(\d+)/i",$h))
    preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
  else
    preg_match_all("/BANDWIDTH\=(\d+)/i",$h,$m);
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $link=$base.$pl[$key_max];
}
}
/////////////////////////////////////////////
  if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode("https://embed.megaxfer.ru")."&Origin=".urlencode("https://embed.megaxfer.ru");
} elseif (strpos($filelink,"idtbox.com") !== false) {
  //https://idtbox.com/avslpcj48so9
  //echo $filelink;
  $pattern = '@(?:\/\/|\.)(idtbox\.com)\/(?:embed-)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  $l="https://idtbox.com/".$r[2];
  //echo $l;
  $l="https://idtbox.com/embed-".$r[2].".html";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $out = curl_exec($ch);
  curl_close($ch);

  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"onlystream.tv") !== false) {
  //https://onlystream.tv/e/rgcv3v545biu
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"http://gamovideo.com/");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h3)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  }
  //echo $out;
  $out .=$h3;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"vidload.net") !== false) {
  //http://www.vidload.net/e/29d045cffe0231a4

  require_once("JavaScriptUnpacker.php");
  $cookie=$base_cookie."videomega.dat";
  $filelink=str_replace("/f/","/e/",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //curl_close($ch);
  $srt="";
  if (preg_match("/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/",$filelink,$m)) {
   $srt=$m[1];
  }
  if (!$srt && preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\)\(\s\[\+\]]*(\.(srt|vtt)))\" srclang=\"\w+\" label=\"(\w+)\"/', $h, $s)) {
  $srts=array();
  if (isset($s[4])) {
    for ($k=0;$k<count($s[4]);$k++) {
      $srts[strtolower($s[4][$k])] = $s[1][$k];
    }
  }
  if (isset($srts["romanian"]))
    $srt=$srts["romanian"];
  elseif (isset($srts["romana"]))
    $srt=$srts["romana"];
  elseif (isset($srts["english"]))
    $srt=$srts["english"];
  }
  $t1=explode('token="',$h);
  $t2=explode('"',$t1[1]);
  $gone=$t2[0];
  $t1=explode('crsf="',$h);
  $t2=explode('"',$t1[1]);
  $oujda=$t2[0];

  $l="https://www.vidload.net/vid/";
  $post="gone=".$gone."&oujda=".$oujda;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).'',
  'Origin: https://www.vidload.net',
  'Connection: keep-alive',
  'Referer: '.$filelink.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $link = curl_exec($ch);
  curl_close($ch);
  $link=trim($link);

} elseif (strpos($filelink,"videomega.co") !== false) {
  //https://www.videomega.co/e/78f604df6f6786a8
  //https://www.videomega.co/js/8c8717d31870ccc0
  //https://www.videomega.co/e/8c8717d31870ccc0
  //echo $filelink;
  //$t1=explode("?",$filelink);
  //$filelink=$t1[0];
  require_once("JavaScriptUnpacker.php");
  $cookie=$base_cookie."videomega.dat";
  $filelink=str_replace("/f/","/e/",$filelink);
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h=curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $srt="";
  if (preg_match("/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/",$filelink,$m)) {
   $srt=$m[1];
  }
  $t1=explode('var token="',$h);
  $t2=explode('"',$t1[1]);
  $token=$t2[0];
  $t1=explode('var crsf="',$h);
  $t2=explode('"',$t1[1]);
  $crsf=$t2[0];
  if ($token && $crsf) {
////////////////////////////////////////////////////////////////////////
  if (!$srt && preg_match_all('/([\.\d\w\-\.\=\/\\\:\?\&\#\%\_\,\)\(\s\[\+\]]+(\.(srt|vtt)))\" srclang=\"(\w+)\" label=\"(\w+)\"/', $h, $s)) {
  //print_r ($s);
  $srts=array();
  if (isset($s[5])) {
    for ($k=0;$k<count($s[5]);$k++) {
      if (strpos($s[1][$k],"empty.srt") === false) $srts[strtolower($s[5][$k])] = $s[1][$k];
    }
  }
  //print_r ($srts);
  //die();
  if (count($srts)>1) {
  foreach ($srts as $key => $value) {
  $t1=explode("srt=",$value);
  if (strpos($t1[1],"videomega") === false) {
   $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://'.parse_url($filelink)["host"].'',
   'Connection: keep-alive',
   'Referer: '.$filelink.'');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $t1[1]);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/7");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
   if(!preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $h))
    unset ($srts[$key]);
  }
  }
  }
  if (count($srts)>1) {
  if (isset($srts["romanian"]))
    $srt=$srts["romanian"];
  elseif (isset($srts["romana"]))
    $srt=$srts["romana"];
  elseif (isset($srts["english"]))
    $srt=$srts["english"];
  } elseif (count($srts)>0) {
    $srt=$s[1][0];
  }
  }
////////////////////////////////////////////////////////////////////////
  $post="gone=".$token."&oujda=".$crsf;
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded',
   'Content-Length: '.strlen($post).'',
   'Origin: https://www.videomega.co',
   'Connection: keep-alive',
   'Referer: '.$filelink);
  $l="https://www.videomega.co/vid/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h=curl_exec($ch);
  curl_close($ch);
  if (preg_match("/http/",$h)) {
  $link=trim($h);
  } else {
    $link="";
  }
}
  //echo $srt;
} elseif (strpos($filelink,"vidload.co") !== false) {
  //https://vidload.co/embed/ae48a6808b693b67a9d689bfeb284ea3?sub=http://serialeonline.to/subtitrari/80752-1-3.vtt&img=http://image.tmdb.org/t/p/w1280/3Mq2p5o2DR5VbNiVOOqqU0nf1X2.jpg
  //echo $filelink;
  $t1=explode("sub=",$filelink);
  $srt=$t1[1];
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  $out .=$h3;
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\s\[\]]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (preg_match("/(videyo|bitvid)\./",$filelink)) {
  //https://www.videyo.xyz/source/1fl0u19uvhsa
  //https://videyo.net/embed-1fl0u19uvhsa.html
  //https://www.bitvid.co/source/iuldyxwjl9nx
  //echo $filelink;
  $h3="";
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"https://www.videyo.xyz");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h2)) {
  $jsu = new JavaScriptUnpacker();
  $h3 = $jsu->Unpack($h2);
  }
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\s\[\]]*(\.(mp4|m3u8)))/', $h3.$h2, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h3.$h2, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
  if ($srt)
   if (strpos($srt,"http") === false) $srt="https://videyo.net".$srt;
//} elseif (strpos($filelink,"mixdrop.") !== false) {
} elseif (preg_match($mixdrop,$filelink)) {
  //https://mixdrop.co/e/eaeuizxtz0
  //https://mixdrop.co/f/mxgr3tvc
  $filelink=str_replace("/f/","/e/",$filelink);
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
  $srt=$s[1];
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://mixdrop.co");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  //echo $h3;
  //curl_close($ch);
  //echo urldecode($h3);
  /*
  if (preg_match("/window\.location/",$h3)) {
  $t1=explode('window.location = "',$h3);
  $t2=explode('"',$t1[1]);
  $l="https://mixdrop.to".$t2[0];
  curl_setopt($ch, CURLOPT_URL, $l);
  $h4 = curl_exec($ch);
  //echo $h3;
  }
  */
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match("/(\/\/[\w|\.\:\?\&\/\=\_\-]+\.mp4\?[\w|\.\:\?\&\/\=\_\-]+)[\'\"]/",$out,$m)) {
      $link="https:".$m[1];
      if (preg_match("/\.(remote)?sub\s*\=\s*\"(.*?)\"/",$out,$s)) {
      //print_r ($s);
       if ($s[2]) {
       $srt= urldecode($s[2]);
       $srt=str_replace(" ","%20",$srt);
       if (strpos($srt,"http") === false && $srt) $srt="https:".$srt;
       }
      }
  } else {
    $link="";
  }
  //echo $srt;
} elseif (preg_match("/vidmoly\.to/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"http://gamovideo.com/");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))  // mai sapa
  $srt=$s[1];
  if (preg_match("/file:\"([^\"]+)\"/",$h,$m)) {
   $link=$m[1];
   if ($flask <> "flash") {
    $link=$link."|Referer=".urlencode("https://vidmoly.to/")."&Origin=".urlencode("https://vidmoly.to");
    $link=$link."&User-Agent=".urlencode($ua);
   }
  }
} elseif (strpos($filelink,"datoporn.co") !== false) {
  //https://datoporn.co/embed-24ynitvgmfow-658x400.html
  $pattern = '@(?:\/\/|\.)(datoporn\.(co|com))\/(?:embed-)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  $l="https://datoporn.co/embed-".$r[3]."-658x400.html";
  //echo $l;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"http://gamovideo.com/");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  } else $link="";
} elseif (strpos($filelink,"okayload.com") !== false) {
  //https://okayload.com/emb.html?w9kls6it0hj7=s1.okayload.com/i/03/00000/w9kls6it0hj7
  //https://okayload.com/embed-w9kls6it0hj7.html?auto=1
  $pattern = '@(?:\/\/|\.)(okayload\.com)\/(embed-|emb\.html\?)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  //print_r ($r);
  $l="https://okayload.com/embed-".$r[3].".html?auto=1";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"http://gamovideo.com/");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $out = $h3;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  //print_r ($m);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (preg_match("/(gomoplayer\.)|(mightyupload\.)|(gomo\.to\/vid\/)/",$filelink)) {
   //https://gomoplayer.com/embed-zp4xnbh16tet.html
   //https://mightyupload.com/embed-4ud2i3om3b9k.html
   //https://gomo.to/vid/eyJ0eXBlIjoibW92aWUiLCJpbWQiOiJ0dDAzMzc2OTIiLCJfIjoiMzE2MTUzMDYxODI2MTEiLCJ0b2tlbiI6IjMyMzEzNSJ9&noneemb
   //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  //$out .=$h3;
  //echo $h3;
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $out, $m)) {
  $link=$m[1];
  //print_r ($m);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"gamovideo.") !== false) {
  //http://gamovideo.com/gd82bzc3i6eq
  //http://gamovideo.com/embed-3zcqynp8vcu2-640x360.html
  // https://gomoplayer.com/embed-zp4xnbh16tet.html
  //echo $fielink;
  //$ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
  //$ua="Linux";
  $l="http://gamovideo.com/videonsuc";
  $cookie=$base_cookie."gamovideo.dat";
  $pattern = '@(?:\/\/|\.)(gamovideo\.com)\/(?:embed-)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  $l="http://gamovideo.com/".$r[2];
  //echo $l;
  //$l="http://gamovideo.com/3zcqynp8vcu2";
  require_once("JavaScriptUnpacker.php");
  $head=array('Cookie: gyns=1; fbm=1; gail=1; rtn=1; luq=1; gew=1; col=1; cpl=1; sugamun=1; invn=1; pfm=1; file_id=3183849; aff=36780; gam=1');
  //$head=array('Cookie: __cfduid=d6b05dffcfeb145b86c3b7b94ededea771577545617; gyns=1; fbm=1; gail=1; rtn=1; gew=1; col=1; cpl=1; sugamun=1; invn=1; pfm=1; file_id=4179895; aff=20504; gam=1');
  //$head=array('Cookie: file_id=4179895; sugamun=1');
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  //$out .=$h3;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  //print_r ($m);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"flix555.com") !== false) {
//echo $filelink;
  //https://flix555.com/embed-u6kgm3ho6mbi.html
  $pattern = '@(?:\/\/|\.)(flix555\.com)\/(?:embed-)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  //print_r ($r);
  $l="https://flix555.com/embed-".$r[2].".html";
  //echo $l;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://flix555.com");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"rovideo.net") !== false || strpos($filelink,"playhd.fun") !== false) {
$filelink=str_replace("http:","https:",$filelink);
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$html = file_get_contents($filelink, false, stream_context_create($arrContextOptions));
/*
echo $response;
die();
$html=file_get_contents($filelink);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);

echo $filelink;
echo $html;
*/
$t1=explode("license_code: '",$html);
$t2=explode("'",$t1[1]);
$d=$t2[0];
$t1=explode("function/0/",$html);
$t2=explode("'",$t1[1]);
$orig=$t2[0];
$c=16;
for ($f = "", $g = 1; $g < strlen($d); $g++) {
  $f .= preg_match("/[1-9]/",$d[$g]) ? $d[$g] : 1;
}
for ($j = intval(strlen($f) / 2), $k = substr($f,0, $j + 1), $l = substr($f,$j), $g = $l - $k, $g < 0 && ($g = -$g), $f = $g, $g = $k - $l, $g < 0 && ($g = -$g), $f += $g, $f =$f*2, $f = "" . $f, $i = $c / 2 + 2, $m = "", $g = 0; $g < $j + 1; $g++)  {
 for ($h = 1; $h <= 4; $h++) {
 $n = $d[$g + $h] + $f[$g];
 $n >= $i && ($n -= $i);
 $m .= $n;
}
}

$t1=explode("/",$orig);
$j=$t1[5];
$h=substr($j,0,32);
$i=$m;
for ($j = $h,$k = strlen($h) - 1;$k >= 0; $k--) {
  for ($l = $k,$m = $k; $m < strlen($i); $m++) $l += $i[$m];
  for (; $l >= strlen($h);) $l -= strlen($h);
  for ($n = "",$o = 0;$o < strlen($h); $o++) {
    $n .= $o == $k ? $h[$l] : ($o == $l ? $h[$k] : $h[$o]);
  }
  $h = $n;
}
$link=str_replace($j,$h,$orig);
} elseif (strpos($filelink,"filmeonlinehd.tv") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if (strpos($link,"http") === false && $link) $link="http:".$link;
} elseif (strpos($filelink,"putload.") !== false) {
  //https://putload.tv/embed-phqg03watdpd.html
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://putload.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h3)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  }
  $out .=$h3;
  if (preg_match('/([http|https][\.\d\w\-\.\,\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $out, $m))
   $link=$m[1];
  else
   $link="";
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,"https://putload.tv");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h3 = curl_exec($ch);
  curl_close($ch);
  echo $h3;
  die();
  */
} elseif (strpos($filelink,"streamcherry.com") !== false) {
  //https://streamcherry.com/embed/pdslqaopmlomfrql/
function indexOf($hack,$pos) {
    $ret= strpos($hack,$pos);
    return ($ret === FALSE) ? -1 : $ret;
}
function decode($encoded, $code) {

    $a1 = "";
    $k="=/+9876543210zyxwvutsrqponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFEDCBA";

    $count = 0;

    //for index in range(0, len(encoded) - 1):
    for ($index=0;$index<strlen($encoded);$index++) {
        while ($count <= strlen($encoded) - 1) {
            $b1 = indexOf($k,$encoded[$count]);
            //echo $b1."\n";
            $count++;
            $b2  = indexOf($k,$encoded[$count]);
            $count++;
            $b3  = indexOf($k,$encoded[$count]);
            $count++;
            $b4  = indexOf($k,$encoded[$count]);
            $count++;

            $c1 = (($b1 << 2) | ($b2 >> 4));
            $c2 = ((($b2 & 15) << 4) | ($b3 >> 2));
            $c3 = (($b3 & 3) << 6) | $b4;
            $c1 = $c1 ^ $code;

            $a1 = $a1.chr($c1);

            if ($b3 != 64)
                $a1 = $a1.chr($c2);
            if ($b3 != 64)
                $a1 = $a1.chr($c3);
      }
  }
return $a1;
}
$ua=$_SERVER['HTTP_USER_AGENT'];
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_ENCODING, '');
      curl_setopt($ch, CURLOPT_REFERER, "https://streamcherry.com");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
if (preg_match("@type:\"video/([^\"]+)\",src:d\('([^']+)',(.*?)\).+?height:(\d+)@",$h1,$m)) {
//print_r ($m);
$a=$m[2];
$b=$m[3];
$rez=decode($a,$b);
$rez=str_replace("@","",$rez);
if (strpos($rez,"http") === false) $rez="http:".$rez;
} else {
$rez="";
}
$link=$rez;
} elseif (strpos($filelink,"soap2day.") !== false) {
//$filelink="https://soap2day.com/movie_aTo2MzIzOw.html";
//echo $filelink;
$t1=explode('&val=',$filelink);
$filelink=$t1[0];
$t2=explode("&tip=",$t1[1]);
$val=$t2[0];
$tip=$t2[1];
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $host=parse_url($filelink)['host'];
  $cookie=$base_cookie."hdpopcorns.dat";
  
  $cookie=$base_cookie."soap2day.dat";
  $ua=file_get_contents($base_pass."firefox.txt");
  //include ("../cloudflare.php");
$sjv="9812";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0";
$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  //



  //$html=cf_pass($filelink,$cookie);
  //echo $html;
  $t1=explode('divU" style="display:none">',$html);
  $t2=explode('<',$t1[1]);
  $param=$t2[0];
  $t1=explode('id="hId" value="',$html);
  $t2=explode('"',$t1[1]);
  $pass=$t2[0];
  $t1=explode('id="hIsW" value="',$html);
  $t2=explode('"',$t1[1]);
  $e2=$t2[0];
  curl_setopt($ch, CURLOPT_URL, $param."/info/cek.html");
  $extra = curl_exec($ch);
  curl_close($ch);
//echo $html;

  // https://soap2day.to/home/index/GetMInfoAjax
  if ($tip=="movie")
   $l="https://".$host."/home/index/GetMInfoAjax";
  else
   $l="https://".$host."/home/index/GetEInfoAjax";
//$post="pass=".$id."&param=".$param."&extra=::1";
//echo $post;
$post="pass=".$pass."&param=".$param."&extra=".$extra."&e2=".$e2;
// pass=aTo2MzIzOw&param=https://q14.wewon.to&extra=95.76.3.211
//echo $post."\n";
//$post="pass=aToxMTI5Njs&param=https://q11.wewon.to&extra=95.76.3.189&e2=1";
//echo $post."\n";
$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Origin: https://'.$host.'',
'Connection: keep-alive',
'Referer: https://'.$host.'/');
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $l);
 //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
 curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
 curl_setopt($ch, CURLOPT_HEADER,0);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 $h = curl_exec($ch);
 curl_close($ch);
 //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  if ($val=="1")
   $link=$r['val'];
  else {
   if (isset($r['val_bak']))
    $link= $r['val_bak'];
   else
    $link=$r['val'];
  }
  if (isset($r['subs'][0]['path'])) {
  for ($k=0;$k<count($r['subs']);$k++) {
    if ($r['subs'][$k]['name'] == "Romanian") {
     $srt = "https://".$host.$r['subs'][$k]['path'];
     break;
    } elseif ($r['subs'][$k]['name'] == "English") {
      $srt = "https://".$host.$r['subs'][$k]['path'];
     break;
    }
  }
  }
  //echo $link."\n";
  //$link="https://q11.wewon.to/a/extra/m2/2021/Naked.Singularity.2021.mp4?tok=686B74627853466543694D2533443054567251316472345131694B68395478526C6D4E31683477674A76397867346B51466143696348726949724376674536684548437A63483052552D456B4D49316931754E69732533447A506B4837767755756745434769644C7A52564B456973497066705035764145375146714668394843&valid=1e7TzDYVtuD2tyxkESadlg&t=1629033070";
  //echo $link."\n";
  $head=array('Origin: https://'.$host,
  'Cookie: sjv='.$sjv);
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
  */
  //echo $srt;
} elseif (strpos($filelink,"remotestre.am") !== false) {
  $link=$filelink;
} elseif (strpos($filelink,"streamflix.one") !== false) {
  $t1=explode("link=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $t3=explode("&ref=",$t2[1]);
  $link=$t2[0];
  $srt=$t3[0];
  $ref=$t3[1];
  $host="https://".parse_url($ref)['host'];
  if ($link && $flash != "flash")
   $link=$link."|Origin=".urlencode("https://".$ref)."&Referer=".urlencode("https://".$ref);

} elseif (strpos($filelink,"jwplayer.flowyourvideo") !== false) {
  //https://jwplayer.flowyourvideo.com/embed/5dfc71257bfcf?subtitles=https://isubsmovies.com/subtitles/7984734/&height=720
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www1.subsmovies.nz");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  //$t1=explode('file: "',$h3);
  //$t2=explode('"',$t1[1]);
  //$link=$t2[0];
  if (preg_match("/file\:\s+(\'|\")(.*?)(\'|\")/",$h3,$m))
    $link=$m[2];
  if (strpos($link,"http") === false && $link) $link="https:".$link;
  if ($link) {
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h4 = curl_exec($ch);
  curl_close($ch);
  //echo $h4;
  if (preg_match("/Location:\s*(.+)/i",$h4,$m))
    $link=trim($m[1]);
  }
  parse_str(parse_url($filelink)['query'],$output);
  if (isset($output['subtitles'])) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $output['subtitles']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www1.subsmovies.nz");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $s=json_decode($h3,1);
  if (isset($s['Romanian']))
    $srt=$s['Romanian'][0]['link'];
  elseif (isset($s['English']))
    $srt=$s['English'][0]['link'];
  }
  //echo $srt;
} elseif (strpos($filelink,"flowyourvideo") !== false) {
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www1.subsmovies.nz");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $t1=explode('label="Romanian" src="',$h3);    //Romanian" src="
  $t2=explode('"',$t1[1]);
  if (!$t2[0]) {
  $t1=explode('label="English" src="',$h3);    //Romanian" src="
  $t2=explode('"',$t1[1]);
  }
  if ($t2[0]) $srt="http://www.flowyourvideo.com".$t2[0];
  //echo $srt;
  //die();
  $t1=explode('source src="//',$h3);
  $t2=explode('"',$t1[1]);
  $l1="http://".$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_REFERER,"http://www.flowyourvideo.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h4 = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/Location:\s*(.+)/i",$h4,$m))
    $link=trim($m[1]);
} elseif (strpos($filelink,"moviemaniac.org") !== false) {
  $host=parse_url($filelink)['host'];
  preg_match("/v\/([\w\-]*)/",$filelink,$m);
  $id=$m[1];
  $url="https://".$host."/api/source/".$id;
  $data = array('r' => '','d' => $host);
  $options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
  );
  //echo $filelink;
  $context  = stream_context_create($options);
  $h3 = @file_get_contents($url, false, $context);
  $r=json_decode($h3,1);
  //print_r ($r);
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $link="https://".$host.$r["data"][$c-1]["file"];
  else
   $link=$r["data"][$c-1]["file"];
  $subs=array();
  for ($k=0;$k<count($r['captions']);$k++) {
    $subs[$r['captions'][$k]['language']]="https://moviemaniac.org/asset/userdata/224879/caption/".$r['captions'][$k]['hash']."/".$r['captions'][$k]['id'].".srt";
  }
  //print_r ($subs);
  if (isset($subs['Romanian']))
    $srt=$subs['Romanian'];
  elseif (isset($subs['English']))
    $srt=$subs['English'];
} elseif (preg_match("/vanfem\.|moviepl\.xyz|videobot\.stream|cinegrabber\.com|superplayxyz\.club|embedsito\.com|vidsrc\.xyz|feurl\.|fcdn\.stream|fembed.*?\.|femax\d+\.com|gcloud\.live|bazavox\.com|xstreamcdn\.com|smartshare\.tv|streamhoe\.online|animeawake\.net|mediashore\.org|sexhd\.co|streamm4u\.club/",$filelink)) {
  $host=parse_url($filelink)['host'];
  // https://fcdn.stream/v/y2zjdhepr--e6z3
  //echo $filelink;
  preg_match("/v\/([\w\-]*)/",$filelink,$m);
  $id=$m[1];
  $url="https://".$host."/api/source/".$id;
  //echo $url;
  $data = array('r' => '','d' => $host);
  $options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                  "Origin: https://".$host."\r\n".
                  "Referer: https://".$host."\r\n",

        'method'  => 'POST',
        'content' => http_build_query($data),

    ),
        "ssl"=>array(
        "verify_peer"=> false,
        "verify_peer_name"=> false,
    )
  );
  //print_r ($options);
  //echo $filelink;
  $context  = stream_context_create($options);
  $h3 = @file_get_contents($url, false, $context);
  //echo $h3;
  $r=json_decode($h3,1);
  //print_r ($r);
  // https://sexhd.co/asset/userdata/200842/caption/x5klya5kjzw-pxn/693418.srt
  if (isset($r['player']['poster_file'])) {
   $t1=explode("userdata/",$r['player']['poster_file']);
   $t2=explode("/",$t1[1]);
   $userdata=$t2[0];
  } else {
   $userdata="";
  }
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://".$host."/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
  } elseif (isset($r["captions"][0]["hash"])) {
    $srt="https://".$host."/asset/userdata/".$userdata."/caption/".$r["captions"][0]["hash"]."/".$r["captions"][0]["id"].".".$r["captions"][0]["extension"];
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $link="https://".$host.$r["data"][$c-1]["file"];
  else
   $link=$r["data"][$c-1]["file"];
   if (preg_match("/\#caption\=(.+)/",$filelink,$m))
     $srt=$m[1];
} elseif (strpos($filelink,"vshare.eu") !== false) {
  //echo $filelink;
  //https://vshare.eu/embed-wrejbmze59bk-729x400.html
  //https://vshare.eu/wrejbmze59bk
  
  $pattern = '/(?:\/\/|\.)(vshare\.eu)\/(?:embed-|)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  //$filelink="https://vshare.eu/".$m[2];
  $filelink="https://vshare.eu/embed-".$m[2]."-100x100.html";
  //echo $filelink;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;

  if (preg_match('/[src="]((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m))
   $link=$m[1];
  else
   $link="";
} elseif (strpos($filelink,"thevideobee.to") !== false) {
  //https://thevideobee.to/4r0n95knvkbz.html
  //https://thevideobee.to/embed-lrzpx9akulnm.html
  $pattern = '/(?:\/\/|\.)(thevideobee\.to)\/(?:embed-|)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  $filelink="https://thevideobee.to/embed-".$m[2].".html";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4)))/', $h, $m))
   $link=$m[1];
  else
   $link="";
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.vtt|\.srt))/', $h, $m)) {
  $srt=$m[1];
  //$srt=str_replace("https","http",$srt);
  if (strpos($srt,"empty.srt") !== false) $srt="";
   if ($srt) {
   if (strpos($srt,"http") === false) $srt="https://thevideobee.to/".$srt;
  }
 }
} elseif (strpos($filelink,"vidoo.tv") !== false) {

} elseif (strpos($filelink,"tunestream.net") !== false) {
  //require_once("JavaScriptUnpacker.php");
  //echo $filelink;
  //https://tunestream.net/embed-xzda2vl6yjao.html
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      //curl_setopt($ch, CURLOPT_REFERER, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      //echo $h1;
      //$jsu = new JavaScriptUnpacker();
      //$out = $jsu->Unpack($h1);
      //echo $out;
  if (preg_match('/[{file:"]((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h1, $m)) {
   $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h1, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else
   $link="";
  if ($link && preg_match("/\.m3u8/",$link) && $flash <> "flash") {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_REFERER, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h2 = curl_exec($ch);
      curl_close($ch);
      //echo $h2;
      $link=get_max_res($h2,$link);
      $link=$link."|Origin=".urlencode("https://tunestream.net")."&Referer=".urlencode("https://tunestream.net");
  }
} elseif (strpos($filelink,"vidtodo") !== false) {
  require_once("JavaScriptUnpacker.php");
//echo $filelink;
  //https://vidtodo.com/f6whqkvtnhj9
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_REFERER, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      //echo $h1;
      $jsu = new JavaScriptUnpacker();
      $out = $jsu->Unpack($h1);
      //echo $out;
  if (preg_match('/[{file:"]((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
   $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else
   $link="";
} elseif (strpos($filelink,"flashx.") !== false) {
  //$filelink="https://www.flashx.tv/1n874158a8xq.html";
  $cookie=$base_cookie."cookie.dat";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://www.flashx.pw");
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      //die();
$id=str_between($h,'id" value="','"');
$fname=str_between($h,'fname" value="','"');
$hash=str_between($h,'hash" value="','"');
$l="https://www.flashx.pw/dl?playitnow";
$post="op=download1&usr_login=&id=".$id."&fname=".$fname."&referer=https://www.flashx.tv/&hash=".$hash."&imhuman=Continue+To+Video";
//echo $post;
sleep(8);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).''
    ));
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:42.0) Gecko/20100101 Firefox/42.0');
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.flashx.pw");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
  $srt=$m[1];
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m);
  $link=$m[1];
} elseif (strpos($filelink,"speedvid") !== false) {
  include ("../util.php");
$cookie=$base_cookie."speedvid.dat";
$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
if (file_exists($cookie)) unlink ($cookie);
  preg_match("/(speedvid\.net)\/(?:embed-|download\/)?([0-9a-zA-Z]+)/",$filelink,$m);
  $id=$m[2];
  $filelink="http://www.speedvid.net/".$id;
  $requestLink=$filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt ($ch, CURLOPT_REFERER, "https://123netflix.pro");
  //curl_setopt($ch, CURLOPT_COOKIE, $clearanceCookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
if (strpos($h1,"503 Service") !== false) {
$rez = getClearanceLink($html,$requestLink);
//echo $rez;
//die();
//$rez=solveJavaScriptChallenge($requestLink,$html);
//echo $rez;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $rez);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);
  //http://www.speedvid.net/xoui92chcqbp

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      //echo $h1;
}
require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h1);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m))
     $link=$m[1];
  else
     $link="";
} elseif (strpos($filelink,"http://xxx.abc") !== false) {
  $t1=explode("?file=",$filelink);
  $link=$t1[1];
} elseif (strpos($filelink,"facebook") !== false) {
function decode_code1($code){
    return preg_replace_callback(
        "@\\\\(u)([0-9a-fA-F]{4})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'UTF-8');
        },
        $code
    );
}
function my_simple_crypt( $string, $secret_key,$secret_iv,$action = 'e' ) {

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}
$cookie=$base_cookie."facebook.dat";

if (file_exists($base_pass."facebook.txt") && file_exists($cookie)) {
 $h=trim(file_get_contents($base_pass."facebook.txt"));
 $t1=explode("|",trim($h));
 $key=$t1[0];
 $IV=$t1[1];
 $h=file_get_contents($cookie);
  //echo $h;
  $dec=my_simple_crypt(trim($h),$key,$IV,"d");
  $t2=explode("|",$dec);
  $c_user=$t2[0];
  $fb_dtsg=urldecode($t2[1]);
  $xs=$t2[2];
} else {
 $c_user="";
 $fb_dtsg="";
 $xs="";
}
//echo $filelink;
$pattern = '/(video_id=|videos\/)([0-9a-zA-Z]+)/';
preg_match($pattern,$filelink,$m);
$id=$m[2];
$filelink="https://www.facebook.com/video/embed?video_id=".$m[2];
//$filelink="https://www.facebook.com/watch/?v=".$id;
//echo $filelink;
// https://www.facebook.com/134093565449/videos/342521610130689/
// https://www.facebook.com/watch/live/?v=342521610130689&ref=watch_permalink
//$filelink="https://www.facebook.com/watch/live/?v=".$m[2]."&ref=watch_permalink";
//$filelink="https://www.facebook.com/Stelian.Ion.USR.PLUS/videos/2177983669024007/";
//$filelink="https://www.facebook.com/watch/?v=2177983669024007";
//$filelink="https://www.facebook.com/watch/live/?v=1254569611674582&ref=watch_permalink";
//$filelink="https://www.facebook.com/134093565449/videos/958365288045890";
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Alt-Used: www.facebook.com',
'Connection: keep-alive',
'Referer: https://www.facebook.com',
'Cookie: c_user='.$c_user.';xs='.$xs,
'Upgrade-Insecure-Requests: 1');
//print_r ($head);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h1 = curl_exec($ch);
      curl_close($ch);
      $h1=str_replace("&amp;","&",$h1);
      $h1 = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $h1);
    $h1=decode_code1($h1);
    $h1=str_replace("\\","",$h1);
      //echo $h1;
      preg_match_all("/FBQualityLabel\=\"(\d+)p\"\>\<BaseURL\>(.*?)\</",$h1,$m);
      //print_r ($m);
      preg_match_all("/\"(?:hd_src|sd_src)\":\"(.+?)\"/",$h1,$x);
      //print_r ($x);
      $link=$x[1][0];
      /*
      $r=array();
      $r=array_combine($m[1],$m[2]);
      krsort($r);
      //print_r ($r);
      $link =  reset($r);
      // playable_url_quality_hd
      // playable_url":"

      if (preg_match("/\"playable\_url\_quality\_hd\"\:\"(.*?)\"/",$h1,$m))
       $link=$m[1];
      else if (preg_match("/\"playable\_url\"\:\"(.*?)\"/",$h1,$m))
       $link=$m[1];
      else if (preg_match("/og\:video\" content\=\"([^\"]+)\"/",$h1,$m))
        $link=$m[1];
      */
      $link=str_replace("&amp;","&",$link);
      $link=str_replace("\\","",$link);
} elseif (strpos($filelink,"vidzi.tv") !== false) {
  //http://vidzi.tv/otefvw9e1jcl.html
  require_once("JavaScriptUnpacker.php");
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $a1=explode("jwplayer.js",$h2);
  $h2=$a1[1];
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  //echo $out;
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m);
  $link=$m[1];
  if (!$link) {
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h2, $m);
  $link=$m[1];
  }
} elseif (strpos($filelink,"powvideo") !== false || strpos($filelink,"povvideo") !== false) {
//https://powvideo.cc/iframe-242ozaaonphs-920x360.html
//$filelink="https://powvideo.cc/embed-w6vwzm7aq9a8-920x360.html";
//$filelink="https://powvideo.cc/iframe-w6vwzm7aq9a8-920x360.html";
//$filelink="https://powvideo.cc/preview-w6vwzm7aq9a8-920x360.html";
//echo $filelink;
//https://powvideo.net/l28hwji0d8v4
function rec($site_key,$co,$sa,$loc) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head = array(
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2'
  );
  $v="";
  $cb="123456789";
  $l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, $loc);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace('\x22','"',$h);
  $t1=explode('recaptcha-token" value="',$h);
  $t2=explode('"',$t1[1]);
  $c=$t2[0];
  $l6="https://www.google.com/recaptcha/api2/reload?k=".$site_key;
  $p=array('v' => $v,
  'reason' => 'q',
  'k' => $site_key,
  'c' => $c,
  'sa' => $sa,
  'co' => $co);
  $post=http_build_query($p);
  $head=array(
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
  'Content-Length: '.strlen($post).'',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l6);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, $l2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('rresp","',$h);
  $t2=explode('"',$t1[1]);
  $r=$t2[0];
  return $r;
}
    if (file_exists("streamplay.txt")) {
      $h=file_get_contents("streamplay.txt");
      unlink ("streamplay.txt");
      if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $h, $m)) {
        $link = $m[1];
    if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx))
        $srt = $xx[1];
    }
    } else {
    include ("obfJS.php");
    require_once("JavaScriptUnpacker.php");
    preg_match('/(powvideo|povvideo)\.(net|cc|co)\/(?:embed-|iframe-|preview-|)([a-z0-9]+)/', $filelink, $m);
    $id       = $m[3];
    $host=parse_url($filelink)['host'];
    $filelink="https://".$host."/embed-".$id.".html";
    $ua = $_SERVER["HTTP_USER_AGENT"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/location:\s*(http.+)/i",$h,$m))
      $host=parse_url(trim($m[1]))['host'];
    $l="https://".$host."/iframe-".$id."-1280x665.html";
    $key="6Ldkb-EUAAAAAOz-YgfqoKkODj52CGbTEnuPXRii";
    $co="aHR0cHM6Ly9wb3d2bGRlby5jYzo0NDM.";
    //$co=base64_encode("https://".$host.":443");
    $token=rec($key,$co,"preview","https://".$host);
    $post="op=embed&token=".$token;
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: '.strlen($post).'',
    'Origin: https://'.$host.'',
    'Connection: keep-alive',
    'Referer: https://'.$host.'/preview-'.$id.'-1280x665.html',
    'Cookie: file_id=5005389; ref_url=https%3A%2F%2F'.$host.'%2Fembed-'.$id.'.html;e_'.$id.'=5005389;BJS0=1',
    'Upgrade-Insecure-Requests: 1');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_HEADER,1);
    $h = curl_exec($ch);
    curl_close($ch);
    //die();

    $jsu   = new JavaScriptUnpacker();
    $out   = $jsu->Unpack($h);
    if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $out, $m)) {
        $link = $m[1];
        $t1   = explode("/", $link);
        $a145 = $t1[3];
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $xx)) {
        $srt = $xx[1];
    if (strpos($srt, "http") === false && $srt)
        $srt = "https://".$host . $srt;
    }
    $enc=$h;
    $dec = obfJS();
    //echo $dec;
    include ("ps.php");
    if (preg_match("/r\.splice/",$dec)) {
     $rez=$dec;
     $rez=preg_replace("/r\.splice\s*\(/","array_splice(\$r,",$rez);
     $rez=preg_replace("/r\s*\[/","\$r[",$rez);
     $rez=preg_replace("/r\s*\=/","\$r=",$rez);
     //$rez=str_replace('+"',"",$rez);
     //$rez=str_replace('"',"",$rez);
     $r = str_split(strrev($a145));
     eval($rez);
     $x    = implode($r);
     $link = str_replace($a145, $x, $link);
    } else {
     $link="";
    }
} else {
    $link = "";
}
}

//} elseif (strpos($filelink,"vidcloud.") !== false) {
} elseif (preg_match("/vidcloud\.|streamrapid\.ru|rabbitstream\.net|mzzcloud\.|dokicloud\.one/",$filelink)) {
  // https://vidcloud.pro/embed4/47bkl9d1f7xz1?i=2c6b544306d5c1b81e0b7b86a000da4cb5572df056ec3727324f7db84611806ecdf5a2e3429a1483ca59e880d8e299ab
  // https://vidcloud.pro/embed/5e1b6063ccb14
  // https://vidcloud.msk.ru/embed4/54enm296il6tu?i=2c6b544306d5c1b81e0b7b86a000da4c2d52850a6e79371835929ac55d1155b6c045926b500d70e69163d8e81cf9c0c9&el=4236402
  // https://vidcloud.msk.ru/embed-4/yvUci3z9lqCM?z=
  // https://streamrapid.ru/embed-4/XUjqcvZwXLxN?z=
  // https://streamrapid.ru/embed-5/4GqejHXbPtfK?z=
  // https://streamrapid.ru/embed-4/48Ym1UDbKV1y?z=
  // https://dokicloud.one/embed-4/cuPOVD34pOrr?z=
  // https://dokicloud.one/js/player/prod/e4-player.min.js?v=1671912131
  ///js/player/prod/e4-player.min.js?v=1690572245
  //echo $filelink;
  // https://dokicloud.one/embed-4/NWm2Me4y5gqf?z=
  // https://rabbitstream.net/embed-4/tnI4F7ds9D51?z=
  //die();
  //$filelink="https://streamrapid.ru/embed-4/VKQH3sE2cree?z=";
  //echo $filelink;
  $host="https://".parse_url($filelink)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  if (preg_match("/embed\-4/",$filelink))
  curl_setopt($ch,CURLOPT_REFERER,"https://www3.zoechip.com/");
  elseif (preg_match("/embed\-5/",$filelink))
  curl_setopt($ch,CURLOPT_REFERER,"https://www.2embed.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  $h=str_replace("\\","",$h);
  $t1=explode('data-id="',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  /*
  $t1=explode("recaptchaNumber = '",$h);
  $t2=explode("'",$t1[1]);
  $num=$t2[0];
  $t1=explode('render=',$h);
  $t2=explode('"',$t1[1]);
  $key1=$t2[0];
  require_once ("rec.php");
  //echo base64_decode("aHR0cHM6Ly92aWRjbG91ZC5tc2sucnU6NDQz");
  if (preg_match("/vidcloud\.msk\.ru/",$host)) {
  $key="6LdS_j8bAAAAAFgdltJiyC6RIiqCCG1daI_VYdw3";
  $co="aHR0cHM6Ly92aWRjbG91ZC5tc2sucnU6NDQz";
  $loc="https://vidcloud.msk.ru";
  $sa="embed_4_get_sources";
  } elseif (preg_match("/streamrapid\.ru|rabbitstream\.net|mzzcloud\./",$host)) {
  $key="6LewJewbAAAAAP7e7M1oZPz-yV3m7YblKNkOWjah";
  $key="6LdAJewbAAAAABSUZxkmD7L8EiAr9MLPqa1jNuOZ";
  $key=$key1;
  $co="aHR0cHM6Ly93d3cyLnpvZWNoaXAuY29tOjQ0Mw..";
  
  $co=base64_encode("https://streamrapid.ru:443");
  $co="aHR0cHM6Ly9zdHJlYW1yYXBpZC5ydTo0NDM.";
  $loc=$host;
  $sa="embed_4_get_sources";
  }
  */
  //echo base64_decode($co);
  //$sa="embed_4_get_sources";
  /*
  $token=rec($key,$co,$sa,$loc);
  if (preg_match("/vidcloud\.msk\.ru/",$host)) {
  $l= $host."/ajax/embed-4/getSources?id=".$id."&_token=".$token;
  } elseif (preg_match("/streamrapid\.ru|rabbitstream\.net|mzzcloud\./",$host)) {
  $l= $host."/ajax/embed-4/getSources?id=".$id."&_token=".$token."&_number=".$num;
  }
  */
  if (preg_match("/embed\-5/",$filelink))
  $l= $host."/ajax/embed-5/getSources?id=".$id;
  elseif (preg_match("/embed\-4/",$filelink))
  $l= $host."/ajax/v2/embed-4/getSources?id=".$id;
  //https://rabbitstream.net/ajax/v2/embed-4/
  else
  $l="";
  //echo $l."\n";
  //die();
  //echo time()."\n";
  //$l="https://rabbitstream.net/ajax/v2/embed-4/getSources?id=ZkvAQwVC4z42&v=55914&h=bae42f24040debcb82fe8bcdd176233cbbbdfbdf&b=1676801512"; //1709461512
  $xxx="";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: '.$host.'/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //$xxx .= $x["sources"]."\n";
  //$h = curl_exec($ch);

  //echo $h;
  
  //$x=json_decode($h,1);
  //$xxx .= $x["sources"]."\n";

  //print_r ($x);
  /*
  if (preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))\"\,\"label\"\:\"(\w+)/', $h, $m)) {
  //$srt=$m[1];
  //print_r ($m);
  //die();
  $srt="";
  for ($k=0;$k<count($m[4]);$k++) {
    if (preg_match("/Romanian/i",$m[4][$k])) {
     $srt=$m[1][$k];
     break;
    }
  }
  if (!$srt) {
  for ($k=0;$k<count($m[4]);$k++) {
    if (preg_match("/English/i",$m[4][$k])) {
     $srt=$m[1][$k];
     break;
    }
  }
  }
  }
  */
  $file=$x["sources"];

  if (substr($x["sources"],0,2) == "U2") {
  //echo "asasass";
   require_once("CryptoJSAES_decrypt.php");
   $l="https://raw.githubusercontent.com/enimax-anime/key/e4/key.txt";
   $l="https://raw.githubusercontent.com/theonlymo/keys/e4/key";
   $password="d333edc4f6a0423a32ee00fdf993d267";
   $password="e54c8749d8020713fb0887c0647b22b9";
   $password="HvOPiBTtnIUhGlHfGAILDQHcsVbHQWBr";
   $password="HvOPiBT7bE0yLJudjQA0DQHcAZhvgVHq";
   $l="https://keys4.fun/";
   $l="https://raw.githubusercontent.com/eatmynerds/key/e4/key.txt";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  //$y=json_decode($h1,1)['rabbitstream']['keys'];
  $q="\$y=".$h1.";";
  //echo $q;
  eval ($q);
  //$y=json_decode($h1,1);
  //print_r ($y);
  //$xxx .=$h1;
  //file_put_contents("1.txt",$xxx);
/*
https://github.com/consumet/consumet.ts/blob/master/src/extractors/vidcloud.ts
        const sourcesArray = res.data.sources.split('');
        let extractedKey = '';

        let currentIndex = 0;
        for (const index of key) {
          const start = index[0] + currentIndex;
          const end = start + index[1];
          for (let i = start; i < end; i++) {
            extractedKey += res.data.sources[i];
            sourcesArray[i] = '';
          }
          currentIndex += index[1];
        }

        key = extractedKey;
        res.data.sources = sourcesArray.join('');
*/
  //echo $file."\n"."\n";
  //print_r ($y);
  $offset=0;
  $decryptedKey="";
  $encryptedString=$file;
  $x=str_split($file);
  $out="";
  for ($i=0;$i<count($y);$i++) {
   $out .=chr($y[$i]);
  }
  //echo $out."\n";
 $decryptedKey=base64_encode($out);
 //echo $file."\n".$decryptedKey."\n";
 $x=CryptoJSAES_decrypt($file,$decryptedKey);
 //echo $x;
  //die();
  /*
  foreach($y as $t) {
   $start=$t[0] + $offset;

   $end=$t[1]+$start;
   for ($i=$start;$i<$end;$i++) {
     $decryptedKey .=$file[$i];
     //$encryptedString[$i]="*";
     $x[$i]="";
   }
  // echo $decryptedKey;
   //$decryptedKey .=substr($encryptedString,$start-$offset,$end-$start);
   //$decryptedKey .=substr($encryptedString,$start+$offset,$end);
   //$encryptedString = substr($encryptedString,0,$start-$offset).substr($encryptedString,$end-$offset);
   //$offset +=$end-$start;
   $offset +=$t[1];
  }
  //$encryptedString=str_replace("*","",$encryptedString);
  $encryptedString=implode("",$x);
  //echo $decryptedKey."\n"."\n".$encryptedString."\n";
   $x=CryptoJSAES_decrypt($encryptedString,$decryptedKey);
   //echo $x;
   */
   if ($x) {
     $xx=json_decode($x,1);
     $link=$xx[0]['file'];
   }
  } else {
  if (isset($x["sources_1"][0]["file"]))
   $link= $x["sources_1"][0]["file"];
  elseif (isset($x["sources"][0]["file"]))
   $link= $x["sources"][0]["file"];
  }
  //echo $link;

  //if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $n))
  //$link=$n[1];
  $srt="";
  for ($k=0;$k<count($x["tracks"]);$k++) {
    if (preg_match("/Romanian/i",$x["tracks"][$k]["label"])) {
     $srt=$x["tracks"][$k]["file"];
     break;
    }
  }
  if (!$srt) {
  for ($k=0;$k<count($x["tracks"]);$k++) {
    if (preg_match("/English/i",$x["tracks"][$k]["label"])) {
     $srt=$x["tracks"][$k]["file"];
     break;
    }
  }
  }
  
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
} elseif (strpos($filelink,"vcstream.to") !== false || strpos($filelink,"vidcloud.co") !== false) {
  $cookie=$base_cookie."vcstream.dat";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  //echo $filelink;
  //$filelink="https://vidcloud.co/embed/5d8adcf7c84dc";
  $origin="https://".parse_url($filelink)['host'];
  preg_match("/(embed\/|fid\=)([a-zA-Z0-9]+)/",$filelink,$m);
  $filelink=$origin."/embed/".$m[2];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location:\s*(http.+)/i",$h,$m)) {
    $host=parse_url(trim($m[1]))['host'];
    $origin="https://".$host;
  }
  //echo $origin;
  $t1=explode('csrf-token" content="',$h);
  $t2=explode('"',$t1[1]);
  $csrf=$t2[0];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
  $srt=$m[1];
///////// recaptcha ///////////////////////////
$ua = $_SERVER['HTTP_USER_AGENT'];
$site_key="6LdqXa0UAAAAABc77NIcku_LdXJio9kaJVpYkgQJ";
$co="aHR0cHM6Ly92aWRjbG91ZC5jbzo0NDM.";
$cb="123456789012";
$l1="https://www.google.com/recaptcha/api.js?render=".$site_key;
$head = array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, $origin);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_HEADER, 1);
$h = curl_exec($ch);
curl_close($ch);
$v=str_between($h,"recaptcha/api2/","/");
$l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, $origin);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_HEADER, 1);
$h = curl_exec($ch);
curl_close($ch);
$h=str_replace('\x22','"',$h);

$c=str_between($h,'recaptcha-token" value="','"');
$l6="https://www.google.com/recaptcha/api2/reload?k=".$site_key;
$p=array('v' => $v,
'reason' => 'q',
'k' => $site_key,
'c' => $c,
'sa' => 'get_player',
'co' => $co);
$post=http_build_query($p);
$head=array(
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
'Content-Length: '.strlen($post).'',
'Connection: keep-alive');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l6);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, $l2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
$h = curl_exec($ch);
curl_close($ch);

$recaptcha=str_between($h,'rresp","','"');
///////////////////////////////////////////////
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-CSRF-TOKEN: '.$csrf.'',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: '.$filelink.'');
//print_r ($head);
  //$ua="Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0";
  //echo $filelink."\n";
  //https://vcstream.to/embed/5dd43d0fc65a6
  //https://vidcloud.co/player?fid=5dd43d0fc65a6&page=embed
  //http://vidcloud.co/embed/5de69bd9481cf/Ad.Astra.2019.1080p.WEB-DL.X264.AC3-MeowE.mp4
  //echo $filelink;
  // /player?fid=5e40dc99bba12&page=embed&token=
  preg_match("/(embed\/|fid\=)([a-zA-Z0-9]+)/",$filelink,$m);
  $l=$origin."/player?fid=".$m[2]."&page=embed&token=".$recaptcha;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $l;
  //$h=file_get_contents($l);

  $h=str_replace("\\","",$h);
  //echo $h;
  $t1=explode('file":"',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
//echo $link;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
  $srt=$m[1];
if (preg_match("/\/0\/playlist\.m3u8/",$link)) {
$head=array(
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: '.$origin.'',
'Connection: keep-alive',
'Referer: https://vidcloud.co/embed/5de69bd9481cf/Ad.Astra.2019.1080p.WEB-DL.X264.AC3-MeowE.mp4');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
if (preg_match_all("/\S+\.m3u8/",$h,$m)) {
  //print_r ($m);
  //echo $link;
  $base1=str_replace(strrchr($link, "/"),"/",$link);
  $link=$base1.$m[0][count($m[0])-1];
  //echo $link;
}
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\/\/(.*?)\/redirect\/.+/",$h)) {
  //print_r ($m);
  $h=str_replace('URI="//','URI="https://',$h);
  if (preg_match("/URI\=\"(.*?)\"/",$h,$m) && $flash=="flash") {
  $l1=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  file_put_contents("hash.key",$h1);
  $h=str_replace($l1,$hash_path."/hash.key",$h);
  }
  //echo $h;
  //die();
if ($flash <> "flash") {
  preg_match("/\/\/([a-zA-Z0-9\.\-\_]+)\/redirect\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)/msi",$h,$m);
  $serv=$m[1];
  $sig=$m[2];
  $ids=$m[3];
  $id1=$m[4];
  $id=array();
  $pat="/".$server."\/redirect\/".$sig."\/".$ids."\/([a-zA-Z0-9]+)\//msi";

  preg_match_all($pat,$h,$m);

  $id=array_values(array_unique($m[1]));

  $out="#EXTM3U"."\r\n";
  $out .="#EXT-X-VERSION:5"."\r\n";
  $out .="#EXT-X-PLAYLIST-TYPE:VOD"."\r\n";
  preg_match("/#EXT-X-TARGETDURATION:\d+/",$h,$m);
  $out .=$m[0]."\r\n";
  $out .="#EXT-X-MEDIA-SEQUENCE:0"."\r\n";
  preg_match("/#EXT-X-KEY:METHOD.+/",$h,$m);
  $out .=$m[0]."\r\n";
  for ($k=0;$k<count($id);$k++) {
   $pat="/(\#EXTINF\:\d+\.\d+\,)\n(\#EXT-X-BYTERANGE\:\d+\@\d+)?\n?([https?\:]?\/\/".$serv."\/redirect\/".$sig."\/".$ids."\/".$id[$k].")/";
   preg_match_all($pat,$h,$n);
   $dur=0;
   //print_r ($n);
   for($z=0;$z<count($n[1]);$z++) {
     preg_match("/\#EXTINF\:(\d+\.\d+)\,/",$n[1][$z],$d);
     $dur +=$d[1];
   }
   $out .="#EXTINF:".number_format($dur,6).","."\r\n";
   if ($flash == "flash") {
     $l1="https://".$serv."/redirect/".$sig."/".$ids."/".$id[$k]."/".$id[$k];
     $out .="hserver.php?file=".base64_encode("link=".urlencode($l1)."&origin=".urlencode($origin))."\r\n";
   } else
   $out .="https://".$serv."/redirect/".$sig."/".$ids."/".$id[$k]."/".$id[$k]."\r\n";
  }
   $out .="#EXT-X-ENDLIST";
} else {
  //$h=preg_replace("/(https?\:)?\/\/([a-zA-Z0-9\.\-\_]+)\/redirect\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)/","hserver.php?file=".base64_encode("link="."$0"."&origin=".urlencode($origin)),$h);
  $h=preg_replace_callback(
    "/(https?\:)?\/\/([a-zA-Z0-9\.\-\_]+)\/redirect\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)/",
    function ($matches) {
      global $origin;
      return "hserver.php?file=".base64_encode("link=".$matches[0]."&origin=".urlencode($origin));
    },
    $h
  );
  $out=$h;
}
  //echo $out;
//https://i.rickey-hickok.xyz/redirect/LUOu7cpuSqAKlcjblznYD8b9Wtp5DdmPWtXY7SeyLSey6SWK7I/VzghWxILWPoSXzrRdPsSgsBSKMVnKPWS8ffDtQgSQzCMCfZcJLHCQ7JiC4oo/Bm6PcOZWczFlVJV2gorvSJGs8NKNLSp3caVLgl6oclF6/Bm6PcOZWczFlVJV2gorvSJGs8NKNLSp3caVLgl6oclF6
//https://i.rickey-hickok.xyz/redirect/LUOu7cpuSqAKlcjblznYD8b9Wtp5DdmPWtXY7SeyLSey6SWK7I/VzghWxILWPoSXzrRdPsSgsBSKMVnKPWS8ffDtQgSQzCMCfZcJLHCQ7JiC4oo/Bm6PcOZWczFlVJV2gorvSJGs8NKNLSp3caVLgl6oclF6/BmFVLWFkSmFrLDrbBS4d85VqLNQNUoAvBJ628Wp9gR4P
  if ($out) {
    file_put_contents("lava.m3u8",$out);
    if ($flash == "flash") {
      $link = $hash_path."/lava.m3u8";
    } else
      $link = $hash_path."/lava.m3u8"; //$link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  } else {
    $link="";
  }
  //echo $link;
  }
  }
  //$link="https://n-fabre.betterstream.co/abrplayback/d7/31/07634c0dba96bf2307990b03eaaeab20961f27e60df464a6ea4db32e39dfc7715a25d3f0194a7a89cdd62e111c9f4e7ad19ecef19b83fd0c23b9cc4a9c416451832f3f059d0bef03318d694ad1d2be730526064106e644e5732dd7757924fb56a4598ec9bb0c1e027b8371561095051a7713e68c5f82e04d70f56ec6a84648f46edff142c8d8e54bd842bdf6faca4e737948b5ebfa7c54c00990c8bb25e7b00f8829c95de5015087cb35a517c954ad0a/abr.m3u8?q=r&token=51338bd69aa1c6b0b871318cc09dab6f";
  //echo $h;
  //$link=str_replace("itag=18","itag=22",$link);
  //echo $srt;
  if ($flash <> "flash")
    $link = $link."|Origin=".urlencode($origin);
  /*
  $link="https://qznellgmw.michel-clevenger.xyz/LUOu7cpuSqAKlcjblznYD8b9Wtp5DdmPWtXY7SeyLSetLSsK7I/0/";
  $link = $link."VzghWxILWPoSXzrRdPsSgsBSKMVnKPWS8ffDtQgSQzCMCfZcJLHCQ7JiC4oo/b1c26876aa3b87a9f2ba2583a7f0e9fc.m3u8";
  $head=array('Origin: https://vidcloud.co');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  echo $h;
  */
} elseif (strpos($filelink,"clipwatching") !== false) {
  //https://clipwatching.com/embed-afw5jbvb8hqm.html
  //$filelink="https://clipwatching.com/tr9ppil1qbrq/the.walking.dead.s09e16.720p.web.h264-tbs.mkv.html";
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }

  $out .=$h;
  //echo $out;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\/(v\.mp4|master\.m3u8)))/', $out, $m))
  $link=$m[1];
  if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $x)) {
    $srt=$x[1];
  }
} elseif (strpos($filelink,"gamovideo1") !== false) {
  //http://gamovideo.com/ya5fgw6djnhx
  //http://gamovideo.com/embed-ya5fgw6djnhx-640x360.html
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:62.0) Gecko/20100101 Firefox/62.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');

  //$filelink="http://gamovideo.com/embed-ya5fgw6djnhx-640x360.html";
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m);
  $link=$m[1];
  
} elseif (preg_match("/str?eamplay\./i",$filelink)) {
//$filelink = "https://streamplay.to/hpeg1vyu75yc";
//$filelink="https://streamplay.to/9cqvwxftcqez";
function rec($site_key,$co,$sa,$loc) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head = array(
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2'
  );
  $v="";
  $cb="123456789";
  $l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, $loc);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace('\x22','"',$h);
  $t1=explode('recaptcha-token" value="',$h);
  $t2=explode('"',$t1[1]);
  $c=$t2[0];
  $l6="https://www.google.com/recaptcha/api2/reload?k=".$site_key;
  $p=array('v' => $v,
  'reason' => 'q',
  'k' => $site_key,
  'c' => $c,
  'sa' => $sa,
  'co' => $co);
  $post=http_build_query($p);
  $head=array(
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
  'Content-Length: '.strlen($post).'',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l6);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, $l2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('rresp","',$h);
  $t2=explode('"',$t1[1]);
  $r=$t2[0];
  return $r;
}
    if (file_exists("streamplay.txt")) {
      $h=file_get_contents("streamplay.txt");
      unlink ("streamplay.txt");
      if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $h, $m)) {
        $link = $m[1];
    if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx))
        $srt = $xx[1];
    }
    } else {
    require_once("JavaScriptUnpacker.php");
    include ("obfJS.php");
    preg_match('/(?:\/\/|\.)(str?eamplay\.(?:to|club|top|me))\/(?:embed-|player-)?([0-9a-zA-Z]+)/', $filelink, $m);
    $id=$m[2];
    $ua       = $_SERVER["HTTP_USER_AGENT"];
    $host=parse_url($filelink)['host'];
    $l1="https://".$host."/embed-".$id.".html";
    //echo $l1;
    $ua = $_SERVER["HTTP_USER_AGENT"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);

    curl_setopt($ch, CURLOPT_REFERER, $filelink);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/location:\s*(http.+)/i",$h,$m))
      $host=parse_url(trim($m[1]))['host'];
    $key="6LeYReEUAAAAABmDgdILN0uBjVvWzGaM0EZQ-bfX";

    //https://powvldeo.cc:443
    $co=base64_encode("https://".$host.":443");
    $token=rec($key,$co,"preview","https://".$host);
    $post="op=embed&token=".$token;
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: '.strlen($post).'',
    'Origin: https://'.$host.'',
    'Connection: keep-alive',
    'Cookie: file_id=13136922; ref_yrp=; ref_kun=1; BJS0=1');
    $l="https://".$host."/player-".$id.".html";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_REFERER, $l1);
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);

    //echo $h;
$jsu = new JavaScriptUnpacker();
$out = $jsu->Unpack($h);
if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $out, $m)) {
    $link = $m[1];
    $t1   = explode("/", $link);
    $a145 = $t1[3];
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $xx)) {
        //src:"/srt/00686/ic19hoyeob1d_Italian.vtt"
        $srt = $xx[1];
    if (strpos($srt, "http") === false && $srt)
        $srt = "https://".$host . $srt;
    }
    $enc=$h;
    $dec = obfJS();
    include ("ps.php");
    if (preg_match("/r\.splice/",$dec)) {
     $rez=$dec;
     $rez=preg_replace("/r\.splice\s*\(/","array_splice(\$r,",$rez);
     $rez=preg_replace("/r\s*\[/","\$r[",$rez);
     $rez=preg_replace("/r\s*\=/","\$r=",$rez);
     $r = str_split(strrev($a145));
     eval($rez);
     $x    = implode($r);
     $link = str_replace($a145, $x, $link);
    } else {
     $link="";
    }
}
}
} elseif (strpos($filelink,"gounlimited.to") !== false) {
require_once("JavaScriptUnpacker.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  //echo $out;
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m);
  $link=$m[1];
  $link=str_replace("https","http",$link);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2.$out, $m))
  $srt=$m[1];
} elseif (strpos($filelink,"cdnfile.info") !== false) {
  //https://hls26xx.cdnfile.info/stream_new/e9e68d7f44b73e1738773a5e84175cb4/i-love-my-mum.mp4
  $link=$filelink;
} elseif (strpos($filelink,"vidlox") !== false) {
//echo $filelink;
//die();
//https://www.vidlox.me/qv18pvlx4e3s-769x433.html
//https://www.vidlox.xyz/source/gp4oocbj0lpi
//https://vidlox.me/embed-rforoqdmx6w4.html
preg_match("/vidlox\.(me|tv|xyz)\/(?:embed-|source\/)?([0-9a-zA-Z]+)/",$filelink,$m);
$id=$m[2];
//echo $filelink;
//$filelink="https://vidlox.me/5bji3c5f1jju"; //https://vidlox.me/qv18pvlx4e3s

$filelink="https://vidlox.me/".$id;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('VPZ=',$h);
  $t2=explode(';',$t1[1]);
  $vpz=$t2[0];
  $head=array('Cookie: file_id=12202816; VPZ='.$vpz.'; ref_url='.$filelink.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/msi', $h2, $m);

  if (isset($m[1]))
   $link=$m[1][0];
  else
   $link="";
  $link=str_replace("https","http",$link);

  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $m))
  $srt=$m[1];
  //die();
} elseif (strpos($filelink,"fastplay.cc") !== false || strpos($filelink,"fastplay.to") !== false) {
  //echo $filelink;
  //http://fastplay.cc/flash-2gx5dp3azekq.html
  //http://fastplay.to/embed-fy7x0e0mzxjk.html
  //http://fastplay.to/embed-2gx5dp3azekq.html
  $filelink=str_replace("flash-","embed-",$filelink);
  $filelink=str_replace("fastplay.cc","fastplay.to",$filelink);
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h2)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  }
  //echo $out;
  //echo $out;
  //die();
  $out .=$h2;
  //echo $out;
  if (preg_match_all('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1][count($m[1]) -1];
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1]; if (strpos($srt,"http") === false && $srt) $srt="https://fastplay.to".$srt;
  } else
    $link="";
} elseif (strpos($filelink,"cloudvideo") !== false) {
  //https://cloudvideo.tv/wpdo30vv84c0
  //https://cloudvideo.tv/embed-apqes9igmbpb.html
  preg_match("/cloudvideo\.tv\/(embed-)?([a-zA-Z0-9_]+)/",$filelink,$m);
  $filelink="https://cloudvideo.tv/embed-".$m[2].".html";
  require_once("JavaScriptUnpacker.php");
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //$a1=explode("jwplayer.js",$h2);
  //$h2=$a1[1];
  //$jsu = new JavaScriptUnpacker();
  //$out = $jsu->Unpack($h2);
  //echo $out;
  //echo $h2;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h2, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.vtt|\.srt))/', $h2, $m)) {
  $srt=$m[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else
  $link="";
} elseif (strpos($filelink,"estream.to") !== false) {
  //echo $filelink;
  //require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //$a1=explode("jwplayer.js",$h2);
  //$h2=$a1[1];
  //$jsu = new JavaScriptUnpacker();
  //$out = $jsu->Unpack($h2);
  //echo $out;
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h2, $m);
  $link=$m[1];
  $link=str_replace("https","http",$link);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.vtt|\.srt))/', $h2, $m)) {
  $srt=$m[1];
  //$srt=str_replace("https","http",$srt);
  if (strpos($srt,"empty.srt") !== false) $srt="";
   if ($srt) {
   if (strpos($srt,"http") === false) $srt="https://estream.to/".$srt;
  }
 }
} elseif (strpos($filelink,"grab.php?link1=") !== false) {   //zfilme
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $t1=explode("Location:",$h);
   $t2=explode("\n",$t1[1]);
   $link=trim($t2[0]);
} elseif (strpos($filelink,"watchers.to") !== false) {
  //http://watchers.to/embed-4cbx3nkmjb7x.html
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  //echo $out;
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m);
  $link=$m[1];
  if (preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.srt))/', $out, $m))
  $srt=$m[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
} elseif (strpos($filelink,"vidoza.") !== false || strpos($filelink,"testaway.xyz") !== false) {
  //echo $filelink;
  //https://vidoza.net/embed-sqzn6x38v6p6.html
  if (strpos($filelink,"https") === false) $filelink=str_replace("http","https",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h2, $m);
  $link=$m[1];
  $link=str_replace("https","http",$link);

  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $m)) {
  $srt=$m[1];
  if (strpos($srt,"http") === false) $srt="https:".$srt;
  }
  if (strpos($srt,"empty") !== false) $srt="";
} elseif (preg_match($indirect,parse_url($filelink)['host'])) {
  //echo $filelink;
  $srt="";
  $host="https://".parse_url($filelink)['host'];
  if (file_exists("hqq.txt")) {
   $link=file_get_contents("hqq.txt");
   $t1=explode("&srt=",$link);
   $link=$t1[0];
   $srt=$t1[1];
   unlink ("hqq.txt");
  } else
   $link="";
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\)\(]*(\.(srt|vtt)))/', $filelink, $m) && !$srt)
  $srt=$m[1];
  if ($link && $flash <> "flash")
    $link .="|Referer=".urlencode($host."/")."&Origin=".urlencode($host);

} elseif (strpos($filelink,"thevideo.me") !== false || strpos($filelink,"vev.io") !== false) {
  //http://thevideo.me/embed-0eqr3o05491w.html
  //https://vev.io/embed/78r81xm7ym34  ==> https://thevideo.me/embed-afdtxrbc8wrg.html
  //https://vev.io/embed/xm3z588jym3y
  //https://vev.io/pair?file_code=xm3z588jym3y&check
  //echo $filelink;
  $pattern = '/thevideo\.me\/(?:embed-|download\/)?([0-9a-zA-Z]+)/';
  if (preg_match($pattern,$filelink,$m)) {
  $file_id=$m[1];
  //$filelink="https://thevideo.me/t7ilerxjm6ca";
  $filelink="https://thevideo.me/embed-".$file_id.".html";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  $t1=explode("cation:",$h1);
  $t2=explode("\n",$t1[1]);
  $filelink=trim($t2[0]);
 }
 //echo $filelink;
 if (preg_match("/vev\.io\/(?:embed-|download\/)?([0-9a-zA-Z]+)/",$filelink,$m)) {
   $id=$m[1];
   //print_r ($m);
 }
 //die();
 //https://vev.io/543v12py6930
 /*
  $l="https://vev.io/api/serve/video/".$id;
  //https://thevideo.me/vsign/player/LD0jPUk7JjVSPiZJTS1GLUEK
  //echo $filelink;
  //$l="https://vev.io/543v12py6930";
  $post="{}";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  */
$l="https://vev.io/api/pair/".$id;
//echo $l;
$ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://vev.io/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $r=json_decode($html,1);
  //echo $html;
//preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4))/', $h, $m);
//print_r ($r);
//die();
  if ($r["qualities"]) {
     foreach ($r["qualities"] as $key=>$value) {
     $link=$value;
     }
     if ($r["subtitles"]) $srt=$r["subtitles"][0];
 } else
    $link="";
} elseif (strpos($filelink,"vidup.io") !== false) {
//echo $filelink;
 if (preg_match("/vidup\.io\/([0-9a-zA-Z]+)/",$filelink,$m)) {
   $id=$m[1];
 }
  $l="https://vidup.io/api/pair/".$id;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://vidup.io/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $r=json_decode($html,1);
  if ($r["qualities"]) {
     foreach ($r["qualities"] as $key=>$value) {
     $link=$value;
     }
     if ($r["subtitles"]) $srt=$r["subtitles"][0];
 } else
    $link="";
} elseif (strpos($filelink,"vidto.me") !== false) {
  //http://vidto.me/59gv3qpxt3xi.html
  //http://vidto.me/embed-59gv3qpxt3xi-600x360.html
  if (strpos($filelink,"embed") !== false) {
    $filelink=str_replace("embed-","",$filelink);
    $t1=explode("-",$filelink);
    $filelink=$t1[0].".html";
  }
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //echo $filelink."<BR>".$h1;
  //die();
  //echo $h;
  $id=str_between($h,'id" value="','"');
  $fname=urlencode(str_between($h,'fname" value="','"'));
  $hash=str_between($h,'hash" value="','"');
  $post="op=download1&usr_login=&id=".$id."&fname=".urlencode($fname)."&referer=&hash=".$hash."&imhuman=Proceed+to+video";
  //op=download1&usr_login=&id=59gv3qpxt3xi&fname=inainte_de_cr%C4%83ciun.mp4&referer=&hash=lnrsqdgj2syvvwlun66f4g7fcr3xjzp3&imhuman=Proceed+to+video
  //echo $post;
  //die();
  sleep(6);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);

  //echo $h;
  //$link=unpack_DivXBrowserPlugin(1,$h);
  if (preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m))
   $link=$m[1];
  else
   $link="";
} elseif ((strpos($filelink, 'vk.com') !== false) || (strpos($filelink, 'vkontakte.ru') !== false)) {
  //echo $filelink;
  //http://vk.com/video_ext.php?oid=169048067&id=164398681&hash=8e32454b953dff04&hd=2
  //$link=vk($filelink);
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://vk.com/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $html=str_replace("\\","",$html);
  //echo $html;
  if (preg_match_all("/url\d+\":\"([http|https][\.\d\w\-\.\/\\\:\=\?\&\#\%\_\,]*)\"/",$html,$m))
    $link=$m[1][count($m[1])-1];
  else
    $link="";
} elseif (strpos($filelink, 'youtu') !== false){
   //https://www.youtube-nocookie.com/embed/kfQTqjvaezM?rel=0
   //echo $filelink;
   //die();
   include ("yt.php");
    $filelink=str_replace("https","http",$filelink);
    $filelink=str_replace("youtube-nocookie","youtube",$filelink);
    //echo $filelink;
    $link=youtube_nou1($filelink);
    //die();
    //$link="";
    if (!$link)
      $link=youtube($filelink);
    //echo $link;
    //$t1=explode("|||",$link);
    //$link=$t1[0];
    $audio="";
    //$link=youtube_nou($filelink);
    /*
    if ($link && strpos($link,"m3u8") === false) {
      $t1=explode("?",$link);
      $link=$t1[0]."/youtube.mp4?".$t1[1];
    }
    */
    //$link=$link."&video_link/video.mp4";
    //$link=$link."&type=.mp4";
// #EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID="audio",NAME="merge",DEFAULT=YES,AUTOSELECT=YES,URI="'.$audio.'"

/*
 file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  }
*/
} elseif (strpos($filelink,'vimeo.com') !==false){
  //http://player.vimeo.com/video/16275866
  ///cgi-bin/translate?info,,http://vimeo.com/16275866
  //https://vimeo.com/683631475
  preg_match("/\d+/",$filelink,$m);
  $filelink="http://player.vimeo.com/video/".$m[0];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://player.vimeo.com");
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  $t1=explode('window.playerConfig = ',$h);
  $t2=explode("</script",$t1[1]);
  $h2=$t2[0];
  //echo $h2;
  //$t1=explode("video/mp4",$h2);
  $r=json_decode($h2,1);
  //print_r ($r);
  $link=$r["request"]["files"]["hls"]["cdns"]["akfire_interconnect_quic"]["url"];
  //$p=$r["request"]["files"]["progressive"];
  //$link=$p[0]["url"];
  /*
  if (!$link) {
   $t1=explode('mime":"video/mp4',$h);
   $t2=explode('url":"',$t1[2]);
   $t3=explode('"',$t2[1]);
   $link=$t3[0];
  }
  $link=str_replace("https","http",$link);
  */
  if ($link && $flash <>"flash") {
    $link=$link."|Referer=".urlencode("https://player.vimeo.com/")."&Origin=".urlencode("https://player.vimeo.com");
  }
} elseif (strpos($filelink, 'filebox.com') !==false) {
  //http://www.filebox.com/embed-mxw6nxj1blfs-970x543.html
  //http://www.filebox.com/mxw6nxj1blfs
  if (strpos($filelink,"embed") === false) {
    $id=substr(strrchr($filelink,"/"),1);
    $filelink="http://www.filebox.com/embed-".$id."-970x543.html";
  }
  $h=file_get_contents($filelink);
  $link=str_between($h,"{url: '","'");
} elseif (strpos($filelink,"dailymotion.com") !==false) {
//echo $filelink;
  // https://www.dailymotion.com/video/x2jtx5v
  // https://www.dailymotion.com/embed/video/x2l65up?autoplay=1
  // https://www.dailymotion.com/video/x2l65up
  preg_match ("/video\/([a-zA-Z0-9]+)/",$filelink,$m);
  $id=$m[1];
  $l="https://www.dailymotion.com/embed/video/".$id;
  // https://www.dailymotion.com/player/metadata/video/x2l65up?embedder=https%3A%2F%2Fwww.dailymotion.com%2Fvideo%2Fx2l65up
  //&dmV1st=2EF52097BDECDE2C2BB0655AB76AE081&dmTs=462227&is_native_app=0&app=com.dailymotion.neon&client_type=website&section_type=player&component_style=_
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";

  $h=file_get_contents($l);
  $t1=explode('"ts":',$h);
  $t2=explode(",",$t1[1]);
  $ts=$t2[0];
  $t1=explode('"v1st":"',$h);
  $t2=explode('"',$t1[1]);
  $dm=$t2[0];
  $l="https://www.dailymotion.com/player/metadata/video/".$id."?embedder=".urlencode($filelink);
  $l .="&dmV1st=".$dm."&dmTs=".$ts."&is_native_app=0&app=com.dailymotion.neon&client_type=website&section_type=player&component_style=_";
  //$h=file_get_contents($l);
  //echo $h;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer : '.$filelink,
  'Connection: keep-alive');
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );
  $context  = stream_context_create($options);
  $h = @file_get_contents($l, false, $context);
  
  $r2=json_decode($h,1);
  $l_main=$r2['qualities']['auto'][0]['url'];
  $link=$l_main;
  //echo $h;
  /*
   $ch = curl_init($filelink);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_REFERER, "https://www.dailymotion.com");
   curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
  $t1=explode('var config = {',$h);
  $t2=explode('window.playerV5',$t1[1]);
  $t1=explode('window.__PLAYER_CONFIG__ = {',$h);
  $t2=explode(';</script',$t1[1]);
  $h1=trim("{".$t2[0]);
  //echo $h1;
  //$h1=substr($h1, 0, -1);
  $r1=json_decode($h1,1);
  //print_r ($r1);
  $l1=$r1['context']['metadata_template_url'];
  $l1=str_replace(':videoId',$id,$l1);
  $l1=str_replace('embedder=:',urlencode($filelink),$l1);
  $h3=file_get_contents($l1);
  $r2=json_decode($h3,1);
  //print_r ($r2);
  //echo $h;
  // https://www.dailymotion.com/player/metadata/video/:videoId?embedder=:embedder&referer=:referer&dmV1st=513492F3C81090EDC48F782153E18AD0&dmTs=827940
  //https://www.dailymotion.com/player/metadata/video/x2k64rf?embedder=https%3A%2F%2Fwww.dailymotion.com%2Fvideo%2Fx2k64rf&referer=&app=com.dailymotion.neon&client_type=website&dmV1st=EEEF91DCB8B983BEA48EA247C9688B40&dmTs=235389&section_type=player&component_style=_
  //$l_main=$r1['metadata']['qualities']['auto'][0]['url'];
  $l_main=$r2['qualities']['auto'][0]['url'];
  $link=$l_main;
  */

  //$r=json_decode($h1,1)['metadata']['qualities'];
  //print_r ($r);
  //if (isset($r['auto'][0]['url'])) {
  //$l_main=$r['auto'][0]['url'];

  $h2=file_get_contents($l_main);
  //echo $h2;
  /*
   $ch = curl_init($l_main);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_REFERER, "https://www.dailymotion.com");

   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h2 = curl_exec($ch);
   curl_close($ch);
   */
   //echo $h2;
   //preg_match_all("/^http.+/i",$h2,$x);
  if (preg_match_all("/^http(.*)$/m",$h2,$q)) {
   //print_r ($x);
  //if (preg_match_all("/PROGRESSIVE-URI\=\"(.*?)\"/",$h2,$q)) {
   $link=$q[0][count($q[0])-1];
   $t1=explode("#",$link);
   $link=$t1[0];
  }

  //}
  // https://www.dailymotion.com/cdn/manifest/video/x2k64rf.m3u8?sec=R-wIm2c9JFoc3ufhncTNMnEiA_K4OJ1gYXrDmB0I8e5BuGUXtcp6QKTNMyhelvKkv5Nu5EnrDyWgTOaQbdaJ3Q&dmTs=667241&dmV1st=3228C9FAF2B3A26C3E980E270E0022B4
  // https://www.dailymotion.com/cdn/manifest/video/x2k64rf.m3u8?sec=R-wIm2c9JFoc3ufhncTNMsC0--b9QVOBvLa266FMFLONWqQEaVUO3CDZPQJW6U9B2VbziW0b4_THJzBhV74fzw&dmTs=794589&dmV1st=C8C342E9532EB913885AF5347F0E0D29
  //$link="https://proxy-005.ix7.dailymotion.com/sec(VCPt2gqlXqNXWRYfJNVmK4EGI1b_sKxcvtX_sWpEKT__3mYesmHxfnvmGN1yhGYai1zF8zMVutzaiXp87u_FNHVBGoQB4KN8ycB7pdRumYg)/video/977/018/154810779_mp4_h264_aac_hd.m3u8#cell=core";
  //$link="https://www.dailymotion.com/cdn/manifest/video/x2k64rf.m3u8?sec=R-wIm2c9JFoc3ufhncTNMpg0EEfiwlCWOcNL2AyIw09o8jhtW9aoSTaYEIKi4fsx9PC1Rya9y9t32DpguSgNHw&dmTs=15564&dmV1st=94F3722F82E5C1CC9AEB9798F31DBF8C";
} elseif (strpos($filelink,"streamcloud.eu") !==false) {
   //op=download1&usr_login=&id=zo88qnclmj5z&fname=666_-_Best_Of_Piss_Nr_2_German.avi&referer=http%3A%2F%2Fstreamcloud.eu%2Fzo88qnclmj5z%2F666_-_Best_Of_Piss_Nr_2_German.avi.html&hash=&imhuman=Weiter+zum+Video
   //echo $filelink;
   //die();
   $cookie=$base_cookie."streamcloud.dat";
   $string = $filelink;
   $ch = curl_init($string);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_REFERER, $string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   $id=str_between($h,'"id" value="','"');
   $fname=str_between($h,'"fname" value="','"');
   $reff=str_between($h,'referer" value="','"');
   $hash=str_between($h,'hash" value="','"');
   $post="op=download1&usr_login=&id=".$id."&fname=".$fname."&referer=".urlencode($reff)."&hash=".$hash."&imhuman=Weiter+zum+Video";
   sleep(11);
   //echo $post;
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   $h = curl_exec($ch);
   //echo $h;
   $link=str_between($h,'file: "','"');

} elseif (strpos($filelink,"mail.ru") !==false) {
   $cookie=$base_cookie."mail.dat";
   //echo $filelink;
   //$filelink="http://api.video.mail.ru/videos/mail/alex.costantin/_myvideo/162.json";
   //http://api.video.mail.ru/videos/embed/mail/alex.costantin/_myvideo/1029.html
   //http://my.mail.ru/video/mail/best_movies/_myvideo/4412.html
   //http://api.video.mail.ru/videos/embed/inbox/virusandrei/_myvideo/38.html
   //http://api.video.mail.ru/videos/mail/best_movies/_myvideo/6501.json
   //http://videoapi.my.mail.ru/videos/embed/mail/anders.doni/_myvideo/1645.html
   //http://videoapi.my.mail.ru/videos/mail/anders.doni/_myvideo/1645.json
   ///https://my.mail.ru/video/embed/5857674095629434888?autoplay=yes
   //https://my.mail.ru/mail/guta_smenaru/video/_myvideo/8.html
   //mail/guta_smenaru/_myvideo/8

  $pattern = '/video\/(embed|download\/)?([0-9a-zA-Z]+)/';
  $pattern = '/\/embed\/([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  //print_r ($m);
  $l="http://my.mail.ru/+/video/meta/".$m[1]."?xemail=&ajax_call=1&func_name=&mna=&mnb=&ext=1&_=".(time()*1000);
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:77.0) Gecko/20100101 Firefox/77.0');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_REFERER, "https://my.mail.ru/video/embed/".$m[1]);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   $r=json_decode($h,1);
   //print_r ($r);
   if (isset($r["videos"][0]["url"]))
   $link="https:".$r["videos"][0]["url"];
   if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://my.mail.ru/video/embed/".$m[1])."&Origin=".urlencode("https://my.mail.ru");

} elseif (strpos($filelink,"ok.ru") !==false) {
//$filelink="https://ok.ru/video/5963859626731";
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //echo $filelink;
  //$user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  $pattern = '/(?:\/\/|\.)(ok\.ru|odnoklassniki\.ru)\/(?:videoembed|video)\/(\d+)/';
  preg_match($pattern,$filelink,$m);
  $id=$m[2];
  //echo $filelink;
  $l="http://www.ok.ru/dk";
  $post="cmd=videoPlayerMetadata&mid=".$id;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_REFERER,"http://www.ok.ru");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $z=json_decode($h,1);
  //print_r ($z);
  ///////////

  /*
  $h=str_replace("&quot;",'"',$h);
  //$t1=explode('data-options="',$h);
  //echo $t1[2];
  $t1=explode('OKVideo" data-options="',$h);
  $t2=explode('" data-player-container',$t1[1]);

  $x=json_decode($t2[0],1);
//print_r ($x);
  $y= $x["flashvars"]["metadata"];
  $z=json_decode($y,1);
  */
  //$link=$l;

  $vids=$z["videos"];
  $c=count($vids);
  $link=$vids[$c-1]["url"];
  if ($link) {
    $t1=explode("?",$link);
    $link=$t1[0]."/ok.mp4?".$t1[1];
  }

} elseif (strpos($filelink,"entervideo.net") !==false) {
   //http://entervideo.net/watch/4752dfc86f5df23
   //echo $filelink;
   $h=file_get_contents($filelink);
   $link=str_between($h,'source src="','"');
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
      $srt=$m[1];
      // see https://sites.google.com/site/mxvpen/faq#TOC-How-can-I-pass-other-HTTP-headers-
   if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode("http://entervideo.net");
} elseif (strpos($filelink,"drive.google.com") !==false) {
//echo $filelink;
  //https://drive.google.com/file/d/1yNs4OjXCugk0CddF07xvaIEasxrLkb8V/view
  $cookie=$base_cookie."drive.dat";
  $pat = '@google.+?([a-zA-Z0-9-_]{20,})@';
  preg_match($pat,$filelink,$m);
  $id=$m[1];
  $l="https://drive.google.com/file/d/".$id."/view";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=file_get_contents($cookie);
  preg_match("/NID\s+(\S+)/",$x,$t);
  preg_match("/DRIVE_STREAM\s+(\S+)/",$x,$t1);
  $head=array("Cookie: NID=".$t[1]."; DRIVE_STREAM=".$t1[1]);
  $ad="NID=".$t[1]."; DRIVE_STREAM=".$t1[1];
  $sPattern = '@\["fmt_stream_map","([^"]+)"]@';
  preg_match($sPattern,$h,$m);
  $videos=explode(",",$m[1]);
  //print_r ($videos);
  $a_itags=array(37,22,18);
  foreach ($videos as $video) {
   preg_match("/(\d+)\|(\S+)/",$video,$m);
   $links[$m[1]] = $m[2];
  }
  if (isset($links[37]))
    $link=$links[37];
  elseif (isset($links[22]))
    $link=$links[22];
  elseif (isset($links[18]))
    $link=$links[18];
  else
    $link="";
  $link = utf8_decode(implode(json_decode('["'.$link.'"]')));
  if ($link && $flash != "flash")
     $link=$link."|Cookie=".urlencode($ad);
//} elseif (strpos($filelink,"mystream.to") !==false || strpos($filelink,"mstream.cloud") !==false  || strpos($filelink,"mstream.xyz") !==false) {
} elseif (preg_match("/mystream\.to|mstream\.cloud|mstream\.xyz|mstream\.website/",$filelink)) {
 //echo $filelink;
 // https://mstream.website/5rf7trsa74i0
 //https://mystream.to/watch/uayjgxrfiy1y
 //$filelink="https://embed.mystream.to/uayjgxrfiy1y";
 $pat='@(?://|\.)(my?stream\.(?:la|to|cloud|xyz|website))/(?:external|watch/)?([0-9a-zA-Z_]+)@';
 preg_match($pat,$filelink,$i);
 $filelink="https://embed.mystream.to/".$i[2];
 //echo $filelink;
 $h=file_get_contents($filelink);
 //echo $h;
 if (preg_match("@(\\$\=\~\[\].*?)\<script@si",$h,$u)) {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) // not sure
  $srt=$s[1];
  $code=$u[0];
  $t1=explode(";",$code);
  $js=$code;
  $t3=substr($t1[1],3);
  $c=explode(",",$t3);
  $x="0,f,1,a,2,b,d,3,e,4,5,c,6,7,8,9";
  $y=explode(",",$x);
  $map=array();
  for ($k=0;$k<count($c);$k++) {
    $a1=explode(":",$c[$k]);
    $map[$y[$k]]="$.".$a1[0];
  }
  $map['o']="$._$";
  $map['u']="$._";
  $map['t']="$.__";
  function cmp($a, $b) {
    if (strlen($a) == strlen($b)) {
        return 0;
    }
    return (strlen($a) > strlen($b)) ? -1 : 1;
  }
  uasort($map, 'cmp');  // sort map strlen
  foreach($map as $key=>$value) {
    $js=str_replace($value,$key,$js);
  }
  $js=str_replace("+","",$js);
  $js=str_replace('"','',$js);
  $js=str_replace('(![])[2]','l',$js);
  $js = preg_replace_callback('@\\\\(\d{2,3})@', function($c){return chr(base_convert($c[1], 8, 10)); }, $js);
  $js=str_replace("\\","",$js);
  //echo $js;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $js, $s))   // not sure
  $srt=$s[1];
  if (preg_match("/http.+\.(mp4|m3u8)/",$js,$m))
    $link=$m[0];
  else
    $link="";
 } else
    $link="";
} elseif (strpos($filelink,"hxload.") !==false) {
  //https://hxload.co/embed/dwv1caux062f/
require_once( "rc4.php" );
function decode_code($code){
    return preg_replace_callback(
        "@\\\(x)?([0-9a-f]{2,3})@",
        function($m){
            return chr($m[1]?hexdec($m[2]):octdec($m[2]));
        },
        $code
    );
}
function abc($a52, $a10)
{
    global $mod;
    $a54 = array();
    $a55 = 0x0;
    $a56 = '';
    $a57 = '';
    $a58 = '';
    $a52 = base64_decode($a52);
    $a52 = mb_convert_encoding($a52, 'ISO-8859-1', 'UTF-8');
    /*
    for ($a72 = 0x0; $a72 < 0x100; $a72++) {
        $a54[$a72] = $a72;
    }
    */
    /*
    for ($a72 = 0x0; $a72 < 0x100; $a72++) {     //new
        $a54[$a72] = (0x3 + $a72) % 0x100;
    }
    */
    /*
    for ($a72 = 0x0; $a72 < 0x100; $a72++) {     //new
        $a54[$a72] = (0x3 + $a72 + pow(0x7c,0x0)) % 0x100;
    }
    */

    for ($a72 = 0x0; $a72 < 0x100; $a72++) {
      eval ($mod);
    }

    for ($a72 = 0x0; $a72 < 0x100; $a72++) {
        $a55       = ($a55 + $a54[$a72] + ord($a10[($a72 % strlen($a10))])) % 0x100;
        $a56       = $a54[$a72];
        $a54[$a72] = $a54[$a55];
        $a54[$a55] = $a56;
    }
    $a72 = 0x0;
    $a55 = 0x0;
    for ($a100 = 0x0; $a100 < strlen($a52); $a100++) {
        $a72       = ($a72 + 0x1) % 0x100;
        $a55       = ($a55 + $a54[$a72]) % 0x100;
        $a56       = $a54[$a72];
        $a54[$a72] = $a54[$a55];
        $a54[$a55] = $a56;
        $xx        = $a54[($a54[$a72] + $a54[$a55]) % 0x100];
        $a57 .= chr(ord($a52[$a100]) ^ $xx);
    }
    return $a57;
}
  $ua       = $_SERVER["HTTP_USER_AGENT"];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=decode_code($h);

  if (preg_match("/var (\w+)\s*\=\s*\'([\w\+\\_\/\=]{100,})\'\;/ms",$h,$m)){ // var hxstring = '8j.....
  $rc4=base64_decode($m[2]);
  // fix abc function
  $t1=explode('decodeURIComponent',$h);
  $t2=explode('{',$t1[1]);
  $t3=explode(';',$t2[1]);
  $mod=$t3[0];
  $mod=str_replace("Math.","",$mod);
  $mod=preg_replace_callback(
   "/Math\[(.*?)\]/",
   function ($matches) {
    return preg_replace("/(\s|\"|\'|\+)/","",$matches[1]);;
   },
   $mod
  );

  preg_match_all("/(_0x)?[a-zA-Z0-9]+/",$mod,$m);
  $mod=str_replace($m[0][0],"\$a54",$mod);
  $mod=str_replace($m[0][1],"\$a72",$mod);
  $mod=$mod.";";
  // end fix
  $h=str_replace(" ","",$h);
  $h=str_replace("'",'"',$h); // avoid abc('0x0','fg'x')
  $pat1="(var\s*((_0x)?[a-z0-9_]+)(\=))";
  $pat2="(function\s*((_0x)?[a-z0-9_]+)(\(\)\{return))";
  $pat3="\[(\"?[a-zA-Z0-9_\=\+\/]+\"?\,?)+\]";
  $pat="/(".$pat1."|".$pat2.")".$pat3."/ms";
  while (preg_match($pat,$h,$m)) {
  $c0=array();
  $x=0;
  $code=str_replace($m[1],"\$c0=",$m[0].";");
  eval ($code);
  $pat = "/\(" . $m[3].$m[6] . "\,(0x[a-z0-9_]+)/";
  if (preg_match($pat, $h, $n)) {
    $x = hexdec($n[1]);
    for ($k = 0; $k < $x; $k++) {
      array_push($c0, array_shift($c0));
    }
  }
  $h=str_replace("+","",$h);
  // _0x_0x36fc("0x0","UhHR")
  $pat="/((_0x)?[a-z0-9_]+)\(\"0x0\"\,\"/ms";
  if (preg_match($pat,$h,$f)) {
  $pat   = "/(".$f[1].")\(\"(0x[a-z0-9_]+)\",\s?\"(.*?)\"\)/ms"; //better
  if (preg_match_all($pat, $h, $p)) {
    for ($z = 0; $z < count($p[0]); $z++) {
      $h = str_replace($p[0][$z], '"'.abc($c0[hexdec($p[2][$z])], $p[3][$z]).'"', $h);
    }
  }
  }
 }
  preg_match("/eval\(\w+\((\"|\')(\w+)(\"|\')/",$h,$p);

  $dec = rc4($p[2], $rc4);
  } else {
   $dec=$h;
  }
  //echo $dec;
  if (preg_match('/\/\/.+\.mp4/', $dec, $m)) {
  $link="https:".$m[0];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $dec, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"vidia.tv") !==false) {
  //https://vidia.tv/ekc59ths2ex4.htm
  //echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match('/file:"((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"viduplayer.com") !==false) {
  //https://viduplayer.com/embed-vg4om5my445n.html
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match('/file:"((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"prostream.to") !==false) {
  //https://prostream.to/dnxk1nlbm820.html
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"videobin.co") !==false) {
  //https://videobin.co/protedx2b41a
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4))/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"upstream.to") !==false) {
  //https://upstream.to/4afk5xygzgmj
  // https://upstream.to/embed-pbg60t92rfwx.html
  require_once("JavaScriptUnpacker.php");
  //echo $filelink;
 $ua = $_SERVER['HTTP_USER_AGENT'];
 //$ua="Mozilla/5.0 (Windows NT 10.0; rv:83.0) Gecko/20100101 Firefox/83.0";
 $cookie = $base_cookie."upstream.dat";
 $host=parse_url($l)['host'];
 $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Upgrade-Insecure-Requests: 1',
 'Connection: keep-alive');
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $filelink);
 curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
 curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_USERAGENT, $ua);
 curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
 curl_setopt($ch, CURLOPT_ENCODING, "");
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
 curl_setopt($ch, CURLOPT_TIMEOUT, 25);
 curl_setopt($ch, CURLINFO_HEADER_OUT, true);
 $h = curl_exec($ch);
 $info = curl_getinfo($ch);
 curl_close($ch);
 //$h=file_get_contents($filelink);
  $t1=explode("div id='vplayer",$h);
  $jsu = new JavaScriptUnpacker();
  $h .= $jsu->Unpack($t1[1]);
  //echo $h;
  if (preg_match('/sources\:\[\{file\:\"([^\"]+)\"/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  if ($flash <> "flash")
   $link=$link."|Origin=".urlencode("https://upstream.to")."&Referer=".urlencode($filelink);
} elseif (strpos($filelink,"playtvid.com") !==false) {
  //https://playtvid.com/5ehixfpkfuxz  (vidtodo)
  require_once("JavaScriptUnpacker.php");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"streamwire.") !==false) {
  //https://streamwire.net/e/z0vagq5unpur
  require_once("JavaScriptUnpacker.php");
  $filelink=str_replace("/f/","/e/",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4)))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  if ($srt && strpos($srt,"http") === false) $srt="https://streamwire.net/".$srt;
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
} elseif (strpos($filelink,"jetload.net") !==false) {
  //https://jetload.net/e/UIn06HSYSKY6?autoplay=yes
  //echo $filelink;
///////// recaptcha ///////////////////////////
$ua = $_SERVER['HTTP_USER_AGENT'];
$site_key="6Lc90MkUAAAAAOrqIJqt4iXY_fkXb7j3zwgRGtUI";
$co="aHR0cHM6Ly9qZXRsb2FkLm5ldDo0NDM.";
$cb="123456789012";
$l1="https://www.google.com/recaptcha/api.js?render=".$site_key;
$head = array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2'
);
$v="mhgGrlTs_PbFQOW4ejlxlxZn";
$l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, "https://jetload.net");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_HEADER, 1);
$h = curl_exec($ch);
curl_close($ch);
$h=str_replace('\x22','"',$h);

$c=str_between($h,'recaptcha-token" value="','"');
$l6="https://www.google.com/recaptcha/api2/reload?k=".$site_key;
$p=array('v' => $v,
'reason' => 'q',
'k' => $site_key,
'c' => $c,
'sa' => 'secure_url',
'co' => $co);
$post=http_build_query($p);
$head=array(
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
'Content-Length: '.strlen($post).'',
'Connection: keep-alive');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l6);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, $l2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
$h = curl_exec($ch);
curl_close($ch);

$recaptcha=str_between($h,'rresp","','"');
///////////////////////////////////////////////
  $filelink=str_replace("/f/","/e/",$filelink);
  preg_match("/e\/([a-zA-Z0-9_]+)/",$filelink,$m);
  $id=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  if ($srt && strpos($srt,"http") === false) $srt="https://jetload.net/".$srt;
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }

  $l="https://jetload.net/jet_secure";
  $post='{"token":"'.$recaptcha.'","stream_code":"'.$id.'"}';
  //echo $post;
  $head=array('Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/json;charset=utf-8',
  'Content-Length: '.strlen($post).'',
  'Origin: https://jetload.net',
  'Connection: keep-alive',
  'Referer: '.$filelink.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $link=$r['src']['src'];
} elseif (strpos($filelink,"noxx.") !==false) {
//echo $filelink;
  $filelink=preg_replace("/\n|\r|\t/","",$filelink);
$cookie1=$base_cookie."noxx.txt";
$c1=file_get_contents($cookie1);
$cookie=$base_cookie."noxx.dat";
$h=file_get_contents($cookie);
preg_match("/PHPSESSID\s+(\w+)/",$h,$m);
$c1 .="; PHPSESSID=".trim($m[1])."; ";
preg_match("/5ske\s+(\w+)/",$h,$m);
$c1 .="5ske=".trim($m[1])."; ";
preg_match("/55vxb\s+(\w+)/",$h,$m);
$c1 .="55vxb=".trim($m[1]).";";

$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Origin: https://noxx.is',
'Referer: https://noxx.is/tv/star-trek/1/1/0',
$c1);
//echo $filelink;
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch,CURLOPT_REFERER,"https://noxx.is");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  $link=preg_replace("/\n|\r|\t/","",$link);


/////////////////////////////////////////////////////////
  if (preg_match('/\/subs(\S+)(srt|vtt)/msi', $h, $s)) {
  //print_r ($s);
  $srt="https://noxx.is".$s[0];
  }
} elseif (strpos($filelink,"azm.to") !==false) {
$cookie=$base_cookie."azm.dat";
$cookie1=$base_cookie."azm.txt";
$c1=file_get_contents($cookie1);
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Origin: https://azm.to',
$c1);
//echo $filelink;
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://azm.to/all");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];


/////////////////////////////////////////////////////////
  if (preg_match('/\/subs(\S+)(srt|vtt)/msi', $h, $s)) {
  //print_r ($s);
  $srt="https://azm.to".$s[0];
  }
} elseif (strpos($filelink,"xmovies8.") !==false) {
include ("../cloudflare.php");
  $t1=explode("id=",$filelink);
  $t2=explode("&s=",$t1[1]);
  $id=$t2[0];
  $serv=$t2[1];
  $host=parse_url($filelink)['host'];
$l="https://".$host."/ajax/v4_get_sources?s=".$serv."&id=".$id."&_=";
//echo $l;
//echo $filelink;
//$l="https://xmovies8.tv/ajax/v4_get_sources?s=vserver&id=132948&_=";
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://'.$host.'/watch-earthquake-bird-2019-1080p-hd-online-free/watching.html',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0";
  $cookie=$base_cookie."xmovies8.txt";

  $html=cf_pass($l,$cookie);
  //echo $html;
  $r=json_decode($html,1);
  //print_r ($r);

  $l=$r['value'];
  if (strpos($l,"http") === false && $l) $l="https:".$l;
  if ($serv <> "hserver") {
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $h=str_replace("\\","",$html);
  //echo $h;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=]*(\.(m3u8|mp4|mkv)))/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/\/\/(\S+)(srt|vtt)/msi', $h, $s)) {
  //print_r ($s);
  $srt="https:".$s[0];
  //if ($srt && strpos($srt,"http") === false) $srt="https://jetload.net/".$srt;
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {   //PTserver // XServer
    $t1=explode('file":"',$h);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
  if (preg_match('/\/\/(\S+)(srt|vtt)/msi', $h, $s)) {
  //print_r ($s);
  $srt="https:".$s[0];
  }
  }
  } else {
  //set_time_limit(360);
  //echo $l;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $h=str_replace("\\","",$html);
  //echo $h;
  $host=parse_url($l)['host'];
  $scheme=parse_url($l)['scheme'];
  $origin=$scheme."://".$host;
  //echo $origin;
  $t1=explode('key":"',$h);
  $t2=explode('"',$t1[1]);
  $key=$t2[0];
  $t1=explode('slug","value":"',$h);
  $t2=explode('"',$t1[1]);
  $slug=$t2[0];

    $l="https://ping.idocdn.com/";
    $post="slug=".$slug;
    //echo $post;
    $host="playhydrax.com";
    $head=array('Accept: */*',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://'.$host.'',
    'Referer: https://embed.streamx.me',
    'Content-Length: '.strlen($post).'',
    'Connection: keep-alive');
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    curl_close ($ch);
    $x=json_decode($html,1);
    //print_r ($x);
    $serv=$x['url'];
    $l="https://ping.".$serv."/";
    $l1=$l."ping.gif";
    //echo $l1;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,"https://playhydrax.com/?v=".$slug);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    if (preg_match("/hx_stream\=(.*?)\;/",$h,$m)) {
      $link="https://".$serv."/#st=".(1000*time());
      if ($flash <> "flash") $link=$link."|Cookie=".urlencode("hx_stream=".$m[1])."&Referer=".urlencode($filelink);
    }


  }
   if ($link && $flash != "flash" && $serv == "vserver")
     $link=$link."|Referer=".urlencode("https://xmovies8.tv");
  //$link="https://v.bighost.be/hls/082c3b889ee603c4825b99c2bfd162af/082c3b889ee603c4825b99c2bfd162af.playlist.m3u8";
//} elseif (strpos($filelink,"streamloverx.com") !==false || strpos($filelink,"bazookastream.host") !== false) {
} elseif (preg_match("/streamloverx\.com|bazookastream\.host|nites\.tv/",$filelink)) {
//echo $filelink;
//$filelink="https://streamloverx.com/?id=Vm9vN2gvVjdKTERtMFNlNFRwS3d0ZE1BSnJibUZISjNzQTk1RzNmckFZUGpnclFjYzJxQXRFa0hTUkV2cXkzZA==";
  $origin="https://".parse_url($filelink)['host'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$origin);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  if (preg_match("/streamloverx\.com/",$filelink)) {
  $t1=explode("var __url	=	'",$html);
  $t2=explode("'",$t1[1]);
  $l="https://streamloverx.com/".$t2[0]."&hydrax=1";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$origin);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($h,1);
  $l=$x['url'];
  $t1=explode("v=",$l);
  $slug=$t1[1];
  } else {
  if (preg_match("/\#slug\=(\S+)/",$filelink,$m))
     $slug=$m[1];
  elseif (preg_match("/\#slug\=(\S+)/",$html,$m))
     $slug=$m[1];
  else {
    $t1=explode('slug","value":"',$html);
    $t2=explode('"',$t1[1]);
    $slug=$t2[0];
  }
  $t1=explode('key":"',$html);
  $t2=explode('"',$t1[1]);
  $key=$t2[0];
  if (!$key) {
  $t1=explode('key: "',$html);
  $t2=explode('"',$t1[1]);
  $key=$t2[0];
  }
  }
  //echo $slug."\n".$key;
  //$origin="https://streamloverx.com";

///////////////////////////////////////////////////////////////////////////////////////
    $filelink="https://hydrax.net/watch?v=".$slug;
    $host="hydrax.net";
    $l="https://ping.idocdn.com/";
    $post="slug=".$slug;
    $host="hydrax.net";
    $head=array('Accept: */*',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://'.$host.'',
    'Referer: '.$filelink.'',
    'Content-Length: '.strlen($post).'',
    'Connection: keep-alive');
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $html = curl_exec($ch);
    curl_close ($ch);
    $x=json_decode($html,1);
    //print_r ($x);
    $serv=$x['url'];
    $l="https://".$serv."/";
    //echo $l;
    $l1=$l."ping.gif";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,$filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/hx_stream\=(.*?)\;/",$h,$m)) {
      $link="https://".$serv."/#st=".(1000*time())."";
      //$link="https://".$serv."/1";
      if ($flash <> "flash")
       $link=$link."|Cookie=".urlencode("hx_stream=".$m[1])."&Referer=".urlencode($filelink);
  $head=array('Cookie: hx_stream='.$m[1].'');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
}
}

//////////////////////////////////////////////////////////////////
if (file_exists($base_sub.".srt")) unlink ($base_sub.".srt");
if (!file_exists($base_sub."sub_extern.srt")) {
   $list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
   }
} else {
  $srt=$base_sub."sub_extern.srt";
}
if ($srt <> "") {
   $t1=explode("|",$link);
   $link1=explode("?",$t1[0]);
   $movie_file=substr(strrchr($link1[0], "/"), 1);
   if (!$movie_file) {
   $t1=explode("|",$link);
   $link1=explode("/?",$t1[0]);
   $movie_file=substr(strrchr($link1[0], "/"), 1);
   }
   $movie_file=substr($movie_file,0,min(250-strlen($base_sub),strlen($movie_file)));
   //echo "=====".$movie_file;
   //echo "\n".strlen($movie_file);
     //$movie_file="v.mp4";
   if (preg_match("/m3u8/",$movie_file))
    $srt_name = substr($movie_file, 0, -4).".srt";
   else if (preg_match("/mp4|flv/",$movie_file))
    $srt_name = substr($movie_file, 0, -3).".srt";
   else
    $srt_name= $movie_file.".srt";

   $srt_name = rawurldecode($srt_name);
   //$srt_name=str_replace(":hls:",urlencode(":hls:"),$srt_name);
   if (strpos($srt_name,".srt") === false)  $srt_name=$srt_name.".srt";
   $srt_name=str_replace("..srt",".srt",$srt_name);
   //if (preg_match("/mp4|flv|m3u8/",$link)) {
   //$srt_name=$pg.".srt";
   //if ($flash == "flash")
     //$srt_name="master.srt";
   $new_file = $base_sub.$srt_name;

   if (!file_exists($base_sub."sub_extern.srt")) {
   //echo $srt;
   if (strpos($srt,"azm.to") !== false) {
    $cookie=$base_cookie."azm.dat";
    $cookie1=$base_cookie."azm.txt";
    $c1=file_get_contents($cookie1);
    $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    $c1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $srt);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_REFERER,"https://azm.to/all");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    } elseif (strpos($srt,"noxx.") !== false) {
    $cookie=$base_cookie."noxx.dat";
    $cookie1=$base_cookie."noxx.txt";
    $c1=file_get_contents($cookie1);
    $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    $c1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $srt);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_REFERER,"https://noxx.is");
    curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
   } elseif (strpos($srt,"soap2day") !== false) {
    $cookie=$base_cookie."soap2day.dat";
    if (file_exists($base_pass."firefox.txt"))
     $ua=file_get_contents($base_pass."firefox.txt");
    else
     $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $srt);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_REFERER,$srt);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
   } elseif (strpos($srt,"lookmovie") === false) {
   
   $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://'.parse_url($filelink)["host"].'',
   'Connection: keep-alive',
   'Referer: '.$filelink.'');
   //print_r ($head);
   $cookie=$base_cookie."hdpopcorns.dat";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   //curl_setopt($ch,CURLOPT_REFERER,"https://embed.iseek.to");
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   if (strpos($srt,"soap2day") === false) curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/7");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
   //echo $h;
   } elseif (strpos($srt,"lookmovie") !== false) {
    $cookie=$base_cookie."lookmovie.txt";
    if (file_exists($base_pass."firefox.txt"))
     $ua=file_get_contents($base_pass."firefox.txt");
    else
     $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
//echo $srt;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $srt);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_REFERER,$srt);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
   }
   //echo $h;
   } else {
    $h=file_get_contents($base_sub."sub_extern.srt");
   }
   if ($h) {
   //echo $srt;
   //echo $h;
   if (preg_match("/dl\.opensubtitles\.org/",$srt)) {
    $h = gzdecode($h);
   }
   $h=htmlspecialchars_decode($h,ENT_QUOTES);
   //$h=mb_convert_encoding($h, 'UTF-8');

   //echo $enc;
   //die();
   // ling subtitles....
   $h=str_replace("&lt;","<",$h);
   $h=str_replace("<br/>","\r\n",$h);
   //if (strpos($h,"&lrm;") !== false) { //Netflix styling
    $h=str_replace("&lrm;","",$h);
    $h=preg_replace("/(\d+\:\d+\:\d+\.\d+ --> \d+\:\d+\:\d+\.\d+)(.+)/","$1",$h);
    $h=preg_replace("/\<c.*?\>/","",$h);
    $h=preg_replace("/\<\/c.*?\>/","",$h);
   //}
   $h=str_replace("\n","\r\n",$h);
   //echo $h;
   //file_put_contents($base_sub."default.srt",$h);
if ($link) {
 if (function_exists("mb_convert_encoding")) {
    if (mb_detect_encoding($h, 'UTF-8', true)== false) $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
} else {
    $h = str_replace("ª","S",$h);
    $h = str_replace("º","s",$h);
    $h = str_replace("Þ","T",$h);
    $h = str_replace("þ","t",$h);
    $h=str_replace("ã","a",$h);
	$h=str_replace("â","a",$h);
	$h=str_replace("î","i",$h);
	$h=str_replace("Ã","A",$h);
}
}
//echo $h;
    function split_vtt($contents)
    {
        $lines = explode("\n", $contents);
        if (count($lines) === 1) {
            $lines = explode("\r\n", $contents);
            if (count($lines) === 1) {
                $lines = explode("\r", $contents);
            }
        }
        return $lines;
    }
if (strpos($h,"WEBVTT") !== false) {
  //convert to srt;

    function convert_vtt($contents)
    {
        $lines = split_vtt($contents);
        array_shift($lines); // removes the WEBVTT header
        $output = '';
        $i = 0;
        foreach ($lines as $line) {
            /*
             * at last version subtitle numbers are not working
             * as you can see that way is trustful than older
             *
             *
             * */
            $pattern1 = '#(\d{2}):(\d{2}):(\d{2})\.(\d{2,3})#'; // '01:52:52.554'
            $pattern2 = '#(\d{2}):(\d{2})\.(\d{2,3})#'; // '00:08.301'
            $m1 = preg_match($pattern1, $line);
            if (is_numeric($m1) && $m1 > 0) {
                $i++;
                $output .= $i;
                $output .= PHP_EOL;
                $line = preg_replace($pattern1, '$1:$2:$3,$4' , $line);
            }
            else {
                $m2 = preg_match($pattern2, $line);
                if (is_numeric($m2) && $m2 > 0) {
                    $i++;
                    $output .= $i;
                    $output .= PHP_EOL;
                    $line = preg_replace($pattern2, '00:$1:$2,$3', $line);
                }
            }
            $output .= $line . PHP_EOL;
        }
        return $output;
    }
    $h=convert_vtt($h);
    //echo $h;
}
function fix_srt($contents) {
$n=1;
$output="";
$bstart=false;
$file_array=explode("\n",$contents);
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d{2,3}) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d{2,3})/', $line) && !$bstart)
        {
          $output .= $n;
          $output .= PHP_EOL;
          $output .= $line.PHP_EOL;
          $bstart=true;
          $first=true;
        } elseif($line != '' && $bstart) {
          $output .= $line.PHP_EOL;
          $first=false;
          //$n++;
        } elseif ($line == '' && $bstart) {
          if ($first==true) {
            $line=" ".PHP_EOL;
            $first=false;
          }
          $output .= $line.PHP_EOL;
          $bstart=false;
          $n++;
        }
  }
  if (!$output) $output=$contents;
return $output;
}
//echo $h;
   $h=fix_srt($h);
//echo $h;
   $h=json_encode($h);
   $h=str_replace("\u0083","",$h);
   $h=str_replace("\u0098","",$h);
   $h=json_decode($h);
//echo $h;
   //$h=json_decode(str_replace("\u0083","",json_encode($h)));
   //echo $h1;
   //if ($flash=="mpc") $h = mb_convert_encoding($h, 'ISO-8859-1', 'UTF-8');;
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h);
   fclose($fh);
}
//}
}
$movie=$link;
//echo $movie."<BR>".$srt."<BR>".$filelink;
//die();
//echo $filelink;
if (strpos($movie,"http") === false) $movie="";
// Set HW+ mod //
$hw="/hqq\.|hindipix\.|pajalusta\.|lavacdn\.xyz|mcloud\.to|putload\.|thevideobee\.";
$hw .="|flixtor\.|0123netflix|mangovideo|waaw1?|lookmovie\.ag|onlystream\.|archive\.org|videomega\.|moviehdkh";
$hw .="|hxload.|jetload\.net|azm\.to|movie4k\.ag|hlsplay\.com|videobin\.|moonline\.|do{2,}d(stream)?\.|dailymotion\.com|flowyourvideo\.com|streamtape\.|okstream\.|easyload\.io|youdbox\.com";
$hw .="|ronemo\.com|rocdn\.|abcvideo\.|hdm\.|evoload\.|m4ufree\.yt|anilist1\.ir|animdl\.cf|noxx\.is|filmele-online\.com|playdrive\.xyz|ezylink\.co|gomoplayer\.";
$hw .="|apimdb\_vip\.net|wootly\.ch|playdrive\.plyr\.xyz|msmoviesbd\.com|c1ne\.co|youtu|streamlare\.|rovideo\.|closeload\.";
$hw .="|embed4free\.com|gdrivestream\.com|gdrvplayer\.com|o2tvseries\.co|tubeup\.xyz|xemovies\.to|jeniusplay\.|fslinks\.|vgfplay\./";
//$t1=explode("|",$hw);
//print_r ($t1);
if ($flash== "mpc") {
  //$mpc=trim(file_get_contents($base_pass."mpc.txt"));
  //$c='"'.$mpc.'" /fullscreen "'.$movie.'"';
  //if (strpos($filelink,"ok1.ru") !== false || strpos($filelink,"raptu") !== false || strpos($filelink,"rapidvideo") !== false || strpos($filelink,"hqq.tv") !== false || strpos($filelink,"google") !== false || strpos($filelink,"blogspot") !== false) {
  //$mpc=trim(file_get_contents($base_pass."vlc.txt"));
  //$c = '"'.$mpc.'" --fullscreen --sub-language="ro,rum,ron" --sub-file="'.$base_sub.$srt_name.'" "'.$movie.'"';
  $mpc=trim(file_get_contents($base_pass."vlc.txt"));

  //$mpc=str_replace("\\","\\\\",$mpc);
  //echo $mpc."<BR>";
  //echo $movie;
  $host="https://".parse_url($filelink)['host'];
  $ua=$_SERVER['HTTP_USER_AGENT'];
  $t1=explode("|",$movie);
  $movie=$t1[0];

  //echo $t1[1];
  parse_str(urldecode($t1[1]),$q);
  //print_r ($q);
  //die();
  if (isset($q['Referer']))
   $host="https://".parse_url($q['Referer'])['host'];
  elseif (isset($q['Origin']))
   $host="https://".parse_url($q['Referer'])['host'];
  //$mpc="mpv.exe";
  if ($movie=="http://127.0.0.1:8080/scripts/filme/lava.m3u8") {
  //echo $_SERVER['HTTP_REFERER'];
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $movie = $p."/lava.m3u8";
  }
  $out1="";
  if (isset($q['User-Agent'])) {
   $ua=$q['User-Agent'];
   unset ($q['User-Agent']);
   $out1=' --user-agent="'.urldecode($ua).'"';
  }
  $out="";
  if (isset($q['Referer'])) {
   $ref=urldecode($q['Referer']);
   unset ($q['Referer']);
   $out1 .=' --referrer="'.$ref.'"';
  }
  foreach ($q as $key =>$value) {
   $out .='"'.$key.": ".urldecode($value).'",';
   //echo $out;
  }
  $out=str_replace("\\","",$out);
  if ($out) {
    $out=" --http-header-fields=".$out;
    //echo $out;
    $out = substr($out,0, -1);
  }
  $out2="";
  if ($srt_name)
   $out2=' --sub-file="'.$base_sub.$srt_name.'"';

  $movie=str_replace("%","%%",$movie);
  $c = $mpc." ".'"'.$movie.'"'.' --volume=100 --fullscreen'.$out1.$out.$out2;
  $c .=" --force-window=immediate";

  $mpv_path=dirname($mpc)."/run_in_mpv.bat";
  $out='@echo off
title: running mpv
start '.$c;
//file_put_contents($mpv_path,$out);
$handle = fopen($mpv_path, "w");
fwrite($handle,$out);
fclose($handle);
$link="mpv://".$movie."#";
echo $link;
die();
} elseif ($flash == "mp") {
  //if (!preg_match($hw,$filelink)) // HW=1;SW=2;HW+=4
  // $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";b.decode_mode=1;end";
  //else
  if ($flash_original=="mp") {
   if (preg_match("/tvseries/",parse_url($filelink)['host']))
    $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";b.decode_mode=1;end";
   else
    $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";end";
   echo $c;
   die();
  } elseif ($flash_original=="flash") {
   $c=$movie;
   header('Content-type: application/vnd.apple.mpegURL');
   header('location: '.$c);
   die();
  }
  //$t1=explode("|",$movie);
  //if (substr($t1[0], -4) == "m3u8")
  //if (preg_match("/\.m3u8/",$t1[0]))
  // $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";end";

  //$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";

} else {
$type = "mp4";
if (strpos($movie,"m3u8") !== false) $type="m3u8";

echo '
<!doctype html>
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.$pg.'</title>
<style type="text/css">
body {
margin: 0px auto;
overflow:hidden;
}
body {background-color:#000000;}
</style>
<style type="text/css">*{margin:0;padding:0}#player{position:absolute;width:100%!important;height:100%!important}.jw-button-color:hover,.jw-toggle,.jw-toggle:hover,.jw-open,.w-progress{color:#008fee!important;}.jw-active-option{background-color:#008fee!important;}.jw-progress{background:#008fee!important;}.jw-skin-seven .jw-toggle.jw-off{color:fff!important}</style>
<script type="text/javasript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript" src="../jwplayer.js"></script>

</head>
<body>
<div id="player"></div>

<script type="text/javascript">
';
echo "
var jwDefaults = {
    'aspectratio': '16:9',
    'autostart': true,
    'controls': true,
    'displaydescription': false,
    'displaytitle': true,
    'flashplayer': '//ssl.p.jwpcdn.com/player/v/7.12.11/jwplayer.flash.swf',
    'height': 260,
    'mute': false,
    'volume': 100,
    'preload': 'auto',
    'androidhls': true,
    'hlshtml': true,
    'playbackRateControls': true,
    'ph': 1,
    'plugins': {
        'ping': {}
    },
    captions: {
        color: '#ffffff',
        fontOpacity: 100,
        edgeStyle: 'raised',
        backgroundOpacity: 0,
        fontFamily: 'Arial',
        fontSize: 20
    },
    'primary': 'html5',
    'repeat': false,
    'stagevideo': false,
    'stretching': 'uniform',
    'visualplaylist': true,
    'width': '100%'
};
jwplayer.defaults = jwDefaults;
var player = jwplayer('player');
";
echo '
player.setup({
    sources: [{
        "file": "'.$movie.'",
        "label": "Default",
        "type": "'.$type.'",
        "default": "true"
    }],
    title: "'.preg_replace("/\n|\r|\"/"," ",$pg).'",
    tracks: [{
        "file": "../subs/'.$srt_name.'",
        "kind": "captions",
        "label": "Romanian",
        "default": "true"
    }],
    captions: {
        color: "#FFFFFF",
        fontOpacity: 100,
        edgeStyle: "raised",
        backgroundOpacity: 0,
        fontFamily: "Arial",
        fontSize: 20
    },
    logo: {
        file: ""
    },
});
jwplayer().addButton("../download.svg", "Download Video", function() {
    window.location.href = player.getPlaylistItem()["file"];
}, "download");

 </script>';

echo '
</body>
</html>
';
}
?>
