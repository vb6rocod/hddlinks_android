<?php
error_reporting(0);
//set_time_limit(0);
include ("../common.php");
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
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$location = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
if ($_SERVER["SERVER_PORT"] != "80") {
    $location .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["SCRIPT_NAME"];
} else {
    $location .= $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"];
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
$flash="direct";
}
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
if (isset($_GET["title"])) $pg = unfix_t(urldecode($_GET["title"]));
$pg=str_replace('"',"",$pg);
$pg=str_replace("'","",$pg);
$t1=explode(",",$filelink);
if (sizeof($t1)>1) {
$pg = urldecode($t1[1]);
$filelink=urldecode($t1[0]);
} else {
$filelink=urldecode($filelink);
}
}
$filelink=str_replace("&amp;","&",$filelink);
//echo $filelink;
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
if (strpos($filelink,"yifymovies.") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $cookie=$base_cookie."hdpopcorns.dat";
  $host=parse_url($filelink)['host'];
  $t1=explode("?",$filelink);
  $post=$t1[1];

  parse_str($post, $p);
  $ref=$p['ref'];
  unset ($p['ref']);
  $post=http_build_query($p);

  $post=str_replace("+","%2B",$post);
  $l="https://".$host."/wp-admin/admin-ajax.php";
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
  if (count($r) > 1) {
   if (isset($r[0]['label'])) {
    $q=array();
    for ($k=0;$k<count($r);$k++) {
     $q[$r[$k]['label']]=$r[$k]['file'];
    }
    if (isset($q['1080p']))
     $link=$q['1080p'];
    elseif (isset($q['720p']))
     $link=$q['720p'];
    elseif (isset($q['480p']))
     $link=$q['480p'];
    elseif (isset($q['360p']))
     $link=$q['360p'];
   }
  } else {
   $link=$r[0]['file'];
   if (preg_match("/playlist\.m3u8/",$link)) {
   $head=array('Origin: https://'.$host.'',
      'Connection: keep-alive',
      'Referer: '.$ref.'');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
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
  }
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://".$host)."&Origin=".urlencode("https://".$host);
}
if (strpos($filelink,"database.gdriveplayer.us") !== false) {
//echo $filelink;
//die();
  parse_str(parse_url($filelink)['query'],$output);
  //print_r ($output);
  $mod=$output['mod'];
  if ($mod=="direct") {
   $link=$output['file'];
   $link=str_replace("+","%2B",$link);
  } else {
   //$filelink=$output['file'];
   //echo $filelink;
   $t1=explode("file=",$filelink);
   $filelink=$t1[1];
   $filelink=str_replace("+","%2B",$filelink);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_REFERER,"https://database.gdriveplayer.us");
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);
      if (preg_match_all("/Location\:\s+(.+)/i",$h,$m)) {
        $filelink=trim($m[1][count($m[1])-1]);
        if (strpos($filelink,"http") === false) $filelink="https:".$filelink;
        $t1=explode("@@",$filelink);
        $filelink= $t1[0];
      } else {
        $filelink="";
      }
  }
  $srt=$output['sub'];
  //$srt=str_replace("%2B","+",$srt);
  //echo $srt;
  //echo $link;
  if ($mod=="direct" && strpos($link,"index.php") !== false) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $link);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_NOBODY,1);
   $h = curl_exec($ch);
   curl_close($ch) ;
   if (preg_match("/Location\:\s+(http.+)/i",$h,$m))
    $link=trim($m[1]);
  }
  if ($mod=="direct" && strpos($link,".m3u8") !== false) {
    $h=file_get_contents($link);
    preg_match_all ("/^(?!#).+/msi",$h,$m);
    $l="https:".$m[0][count($m[0])-1];
    $h=file_get_contents($l);
    $h=preg_replace_callback(
    "/hls\.php.+/",
    function ($matches) {
      $base2="https://gdriveplayer.us/";
      $host="gdriveplayer.us";
      return "hserver.php?file=".base64_encode("link=".$base2.$matches[0]."&origin=".urlencode("https://".$host.""));
    },
    $h
    );
   file_put_contents("lava.m3u8",$h);
   $a1=$_SERVER['HTTP_REFERER'];   // if exist !!!!!!!!!!!
   $a2=explode("?",$a1);
   $hash_path = dirname($a2[0]);  // change this
   $link=$hash_path."/lava.m3u8";
   }
}
if (strpos($filelink,"moviehdkh.com") !== false) {
   $ua = $_SERVER['HTTP_USER_AGENT'];
   //$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
   $cookie=$base_cookie."hdpopcorns.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.moviehdkh.com");
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('<iframe',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l="https://www.moviehdkh.com".$t3[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.moviehdkh.com");
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
   $srt=$m[1];
  $t1=explode('var videos = [',$h);
  $t2=explode(']',$t1[1]);
  $s="[".$t2[0]."]";
  $r=json_decode($s,1);
  $links=array();
    for ($k=0;$k<count($r);$k++) {
      $links[$r[$k]['label']]=$r[$k]['src'];
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
if (strpos($filelink,"yesmovies.ag") !== false) {
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $filelink=json_decode($html,1)['src'];
  //echo $filelink;
}
if (strpos($filelink,"streamvid.co") !== false) {
  // https://streamvid.co/player/pi5Mk6un7tIap1k/
 $host=parse_url($filelink)['host'];
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
if (strpos($filelink,"watchstreamonline.xyz") !== false) {
 $link=$filelink;
}
if (strpos($filelink,"hdm.to") !== false) {
 $t1=explode("v=",$filelink);
 $link=str_replace("1o.to/", "hls.1o.to/vod/",$t1[1])."/playlist.m3u8";
}
if (strpos($filelink,"voxzer.org") !== false) {  // check for "slug" else redirect
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
      $a1=$_SERVER['HTTP_REFERER'];
      $a2=explode("?",$a1);
      $hash_path = dirname($a2[0]);
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
      $server=$x['servers']['redirect'][0];
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
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   //echo $h."\n";
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
    $head=array('Referer: https://embed.iseek.to',
     'Upgrade-Insecure-Requests: 1');
    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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
if (strpos($filelink,"https://ffmovies.to") !== false) {
 $ua = $_SERVER['HTTP_USER_AGENT'];
 $cookie=$base_cookie."ffmovies.dat";
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Age: 0',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://ffmovies.to/film/in-the-tall-grass.1q7nx');
 $find = substr(strrchr($filelink, "."), 1);
 $t1=explode("/",$find);
 //echo $find;

 $l="https://ffmovies.to/ajax/film/servers/".$t1[0];
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
  //curl_close($ch);
$x=json_decode($h,1);
$h=$x['html'];
//echo $h;
//die();
$videos = explode('div class="server row', $h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t2=explode('data-id="',$video);
  $t3=explode('"',$t2[1]);
  $server=$t3[0];
  $vids=explode('data-id="',$video);
  unset($vids[0]);
  foreach($vids as $vid) {
  $t4=explode('"',$vid);
  $id=$t4[0];
  $t1=explode('href="',$vid);
  $t2=explode('"',$t1[1]);
  $l="https://ffmovies.to".$t2[0];
  if (strpos($l,$find) !== false) {
   $id_bun=$id;
   $server_bun=$server;
   //echo $server_bun."\n".$id_bun."\n";
   break;
  }
  }
}
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Age: 0',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $l="https://ffmovies.to/ajax/episode/info?id=".$id_bun."&server=".$server_bun;
  //$l="https://ffmovies.to/ajax/episode/info?ts=1573545600&_=694&id=5c6a3d606b92fd4c03c0efd4d46d3d0000082fcfb564f79fdcc61c331307edb3&server=36";
 //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  for ($k=0;$k<10;$k++) {
   $h = curl_exec($ch);
   if (strpos($h,"200 OK") !== false) break;
  }

  curl_close($ch);
  $t1=explode("{",$h);
  $h="{".$t1[1];
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
 $filelink=urldecode($x['target']);
 //echo $filelink;
 $srt=$x['subtitle'];
}
if (strpos($filelink,"moviesjoy.net") !== false) {
  //$filelink="https://www.moviesjoy.net/ajax/movie_sources/2203229-30";
  //echo $filelink;
///////// recaptcha ///////////////////////////
$ua = $_SERVER['HTTP_USER_AGENT'];
$site_key="6LfHPLoUAAAAAO0Jylr8Bn5RptHLGDdGuDybODPA";
$co="aHR0cHM6Ly93d3cxLm1vdmllc2pveS5uZXQ6NDQz";
$cb="123456789012";
$l1="https://www.google.com/recaptcha/api.js?render=".$site_key;
$head = array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2'
);
$v="";
$l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l2);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, "https://www.moviesjoy.net");
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
'sa' => 'get_sources',
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
$id = substr(strrchr($filelink, "/"), 1);
//$id="2228820-55";
//$id="812876";
$l="https://www.moviesjoy.net/ajax/movie_sources";
$l="https://www1.moviesjoy.net/ajax/get_link/".$id."?_token=".$recaptcha;
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
    if (preg_match("/megaxfer\.ru/",$filelink)) {
     $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0',
     'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'Connection: keep-alive',
     'Referer: https://www1.moviesjoy.net/watch-movie/christmas-a-la-mode-58758.812876',
     'Cookie: __cfduid=d38356d4788ac2fcb1ccc2459ffcc1fc11574327214',
     'Upgrade-Insecure-Requests: 1');
     $ch = curl_init($filelink);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     $h = curl_exec($ch);
     curl_close($ch);
     if (preg_match("/iframe\s+src\=[\"|\'](.*?)[\'|\"]/",$h,$m))
      $filelink=$m[1];
    }
  }
  if (isset($y["tracks"][0]["file"]))
     $srt=$y["tracks"][0]["file"];
//echo $filelink;
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
if (!$pg) $pg = "play now...";
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
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
if (strpos($filelink,".googlevideo.com") !== false) {
  $link=$filelink;
} elseif (strpos($filelink,"hydrax.net") !== false) {
    //https://hydrax.net/watch?v=fZUPUFcqYIz
    //echo $filelink;
    if (preg_match("/watch\?v\=([a-zA-Z0-9_\-]+)/",$filelink,$m)) {
      $slug=$m[1];
      $l="https://multi.idocdn.com/guest";
      $post="slug=".$slug;
    } else {
      $l="https://multi.idocdn.com/vip";
      $slug="";
      $type="slug";
      $key="50545b9c603dc6e66bf8bd25ccf4e66d";
      $post="key=".$key."&type=".$type."&value=".$slug;
    }
    //echo $filelink;
  /*
    $head=array('Origin: https://hydrax.net');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch,CURLOPT_REFERER,"https://hydrax.net");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  */
  //echo $h;
  //die();
    $a1=$_SERVER['HTTP_REFERER'];
    $a2=explode("?",$a1);
    $hash_path = dirname($a2[0]);

    $host="hydrax.net";
    //echo $post;
    $origin="https://".$host;
    $head=array('Accept: */*',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://'.$host.'',
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
//////////////////////////////////////////////////////
  if (isset($x['servers'])) {
  $server=$x['servers']['redirect'][0];
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
  if ($link && $flash <> "flash")
    $link=$link."|Origin=".urlencode($origin);
} elseif (strpos($filelink,"archive.org") !== false) {
  $link=$filelink;
} elseif (strpos($filelink,"embed.meomeo.pw") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"http://vumoo.to");
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/((https?)?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx)) {
        $srt = $xx[1];
    if (strpos("http", $srt) === false && $srt)
        $srt = "https:" . $srt;
    }
  } else
  $link="";
} elseif (strpos($filelink,"eplayvid.com") !== false) {
  // http://eplayvid.com/watch/170bf88ed18a3bc
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/src\=\"((https?)?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4|\.m3u8))/', $h, $m)) {
   $link=$m[1];
   if (strpos($link,"http") === false) $link="https:".$link;
   if ($link && $flash !="flash")
     $link=$link."|Referer=".urlencode($filelink);
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $h, $xx)) {
        $srt = $xx[1];
    if (strpos("http", $srt) === false && $srt)
        $srt = "https:" . $srt;
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
    if (strpos("http", $srt) === false && $srt)
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
} elseif (strpos($filelink,"vidsrc.me") !== false) {
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
  $t1=explode('iframe src="',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  if (strpos($l,"http") === false) $l="https://vidsrc.me".$l;
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
  $link=$filelink;
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
} elseif (strpos($filelink,"mcloud.to") !== false) {
//echo $filelink;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  //https://mcloud.to/embed/k3720r?key=bdc4df372aebeeb6355277be86e94db9&sub.file=//sub1.iseek.to/sub.vtt?hash=caHR0cHM6Ly93d3c3LnB1dGxvY2tlcnR2LnRvL3N1YnRpdGxlLzQ1MDE2LnZ0dA==&autostart=true
  $t1=explode("&sub.file=",$filelink);
  $t2=explode("&",$t1[1]);
  $srt=urldecode($t2[0]);
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;

  $filelink=$t1[0];
  //echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //include ("obfJS.php");
  //$enc=$h;
  //echo obfJS();
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
   $link=$m[1];
  else
   $link="";
   $t1=explode('mediaSources = [{"file":"',$h);
   $t2=explode('"',$t1[1]);
   $link=$t2[0];
   $head=array('Origin: https://mcloud.to');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode($filelink)."&Origin=".urlencode('https://mcloud.to');
} elseif (strpos($filelink,"/vidnode.net") !== false || preg_match("/\/vidcloud\d+/",$filelink)) {
  //$filelink=str_replace("streaming.php","load.php",$filelink);
  //echo $filelink;
  $t1=explode("@@",$filelink);
  $filelink=$t1[0];
  $host=parse_url($filelink)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  if (preg_match('/sources\:\[\{file\:\s*(\'|\")((https?)?(\S+))(\'|\")/', $h2, $m))
   $link=$m[2];
  elseif (preg_match('/window\.urlVideo\s+\=\s+(\'|\")((https?)?(\S+))(\'|\")/', $h2, $m))
   $link=$m[2];
  else
   $link="";

  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $s)) {
  $srt=$s[1];
  if (strpos($srt,"/images") !== false) $srt="";
  }
  //https://vidnode.net/load.php?id=MzAwNDIy&title=The+Expanse+-+Season+4+Episode+8&typesub=SUB&sub=L3RoZS1leHBhbnNlLXNlYXNvbi00LWVwaXNvZGUtOC90aGUtZXhwYW5zZS1zZWFzb24tNC1lcGlzb2RlLTgudnR0&cover=Y292ZXIvdGhlLWV4cGFuc2Utc2Vhc29uLTQucG5n
  parse_str($filelink,$s);
  if (!$srt) {
   if (isset($s['sub']))
     if ($s['sub'])
     $srt="https://".$host."/player/sub/index.php?id=".$s['sub'];
  }
  //echo $srt;
  //echo $h2;
  //$link="https://slave25.vidcloudfile.com/drive//hls/7e72d7d31931a08af54051043a365263/7e72d7d31931a08af54051043a365263.m3u8";
  if (strpos($link,".m3u8") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host."");
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
///////////////////////////////////////////////
if ($flash == "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://vidnode.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace('URI="//','URI="https://',$h);
  if (preg_match("/URI\=\"(.*?)\"/",$h,$m)) {
  $l1=$m[1];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host."");
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  file_put_contents("hash.key",$h1);
  $h=str_replace($l1,$hash_path."/hash.key",$h);
  }
  if (strpos($h,"/drive/hls/") !== false) {
  $h=preg_replace_callback(
    "/\/.+/",
    function ($matches) {
      global $base2;
      global $host;
      return "hserver.php?file=".base64_encode("link=".$base2.$matches[0]."&origin=".urlencode("https://".$host.""));
    },
    $h
  );
  file_put_contents("lava.m3u8",$h);
  $a1=$_SERVER['HTTP_REFERER'];   // if exist !!!!!!!!!!!
  $a2=explode("?",$a1);
  $hash_path = dirname($a2[0]);  // change this
  $link=$hash_path."/lava.m3u8";
  }
}
}
//////////////////////////////////////////////
     if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode("https://".$host."")."&Origin=".urlencode("https://".$host."");

/////////////////////////////////////////

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
  $a1=$_SERVER['HTTP_REFERER'];   // if exist !!!!!!!!!!!
  $a2=explode("?",$a1);
  $hash_path = dirname($a2[0]);  // change this
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


  //https://megaxfer.ru/embed/5cea5502e49c0
  //https://embed.megaxfer.ru/embed/4c7bdac27256a35d67b042f6c3df3f50
  $pattern = '@(?:\/\/|\.)(megaxfer\.ru)\/(?:embed)?\/([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$m);
  //print_r ($m);
  //die();
  //echo $l;
  //https://streaming.megaxfer.ru/api/m3u8/auto/906c2f8d704b149eb6ed2d290f667af1.m3u8
  // https://embed2.megaxfer.ru/embed/7b133af81f98fff4b3107095f7cb47dd
  $l="https://megaxfer.ru/player?fid=".$m[2]."&page=embed";   // ??? vidcloud
  //$h2=file_get_contents($l);   // ???? why ?????????
  //echo $h2;
  $l="https://embed2.megaxfer.ru/embed/".$m[2];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
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
  //echo $filelink;
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
  curl_close($ch);

  $ids=array();
  // href="xcxcc" id="ererer"
  preg_match_all("/href\s*\=\s*[\'|\"](.*?)[\'|\"].*?id\s*\=\s*[\'|\"](\w+)[\'|\"]/ms",$h,$m);
  for ($k=0;$k<count($m[2]);$k++) {
   $ids[$m[2][$k]]=$m[1][$k];
  }
  // type="hidden" id="5e29af3b6ac6b" value="
  preg_match_all("/type\=\"hidden\"\s*id=\"(.*?)\"\s*value\=\"(.*?)\"/ms",$h,$m);
  for ($k=0;$k<count($m[2]);$k++) {
   $ids[$m[1][$k]]=$m[2][$k];
  }
  // bigbangass=document.getElementById("
  $rep=array();
  preg_match_all("/(\w+)\s*\=\s*document\.getElementById\([\'\"](\w+)[\'\"]/ms",$h,$m);
  for ($k=0;$k<count($m[1]);$k++) {
    $rep[$m[1][$k]]=$ids[$m[2][$k]];
  }
  // xhr.send("myreason="+tnaketalikom+"&saveme="+fuckoff)
  preg_match("/xhr\.send\s*\((.*?)\)/ms",$h,$m);
  $p=preg_replace("/(\"|\'|\s|\+)/","",$m[1]);
  parse_str($p,$o);
  $a=array();
  foreach($o as $key => $value) {
    $a[$key]=$rep[$value];
  }
  $post=http_build_query($a);
  //echo $post;
  preg_match("/streamurl\/[\'|\"]\s*\+\s*(\w+)/ms",$h,$m);
  $id=$m[1];
  $str_url=$rep[$id];
  $l="https://www.vidload.net/streamurl/".$str_url."/";
  //echo $l;
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
  $l = curl_exec($ch);
  curl_close($ch);
  //echo "\n".$l;
  $l="https://www.vidload.net".$l;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$filelink.'',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, trim($l));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);

  $out .=$h3;
  if (preg_match('/http.+\.mp4/', $out, $m)) {
  $link=$m[0];
  if (preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\)\(\s\[\+\]]*(\.(srt|vtt)))\" srclang=\"\S+\" label=\"(.*?)\"/', $out, $s))
  //print_r ($s);
  $srts=array();
  if (isset($s[4])) {
    for ($k=0;$k<count($s[4]);$k++) {
      $srts[$s[4][$k]] = $s[1][$k];
    }
  }
  if (isset($srts["Romanian"]))
    $srt=$srts["Romanian"];
  elseif (isset($srts["Romana"]))
    $srt=$srts["Romana"];
  elseif (isset($srts["English"]))
    $srt=$srts["English"];
  } else {
    $link="";
  }
  //echo $srt;
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
  //curl_close($ch);
  //echo $h;
  $ids=array();
  // href="xcxcc" id="ererer"
  preg_match_all("/href\s*\=\s*[\'|\"](.*?)[\'|\"].*?id\s*\=\s*[\'|\"](\w+)[\'|\"]/ms",$h,$m);
  for ($k=0;$k<count($m[2]);$k++) {
   $ids[$m[2][$k]]=$m[1][$k];
  }
  // bigbangass=document.getElementById("
  $rep=array();
  preg_match_all("/(\w+)\s*\=\s*document\.getElementById\([\'\"](\w+)[\'\"]/ms",$h,$m);
  for ($k=0;$k<count($m[1]);$k++) {
    $rep[$m[1][$k]]=$ids[$m[2][$k]];
  }
  // xhr.send("myreason="+tnaketalikom+"&saveme="+fuckoff)
  preg_match("/xhr\.send\s*\((.*?)\)/ms",$h,$m);
  $p=preg_replace("/(\"|\'|\s|\+)/","",$m[1]);
  parse_str($p,$o);
  $a=array();
  foreach($o as $key => $value) {
    $a[$key]=$rep[$value];
  }
  $post=http_build_query($a);
  //echo $post;
  preg_match("/streamurl\/[\'|\"]\s*\+\s*(\w+)/ms",$h,$m);
  $id=$m[1];
  $str_url=$rep[$id];
  $l="https://www.videomega.co/streamurl/".$str_url."/";
  //echo $l;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).'',
  'Origin: https://www.videomega.co',
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $l = curl_exec($ch);
  curl_close($ch);
  //echo $l;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$filelink.'',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, trim($l));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  $out="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h3)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  }
  //echo $out."\n";
  // |https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\s\[\]\+\(\)]*
  $out .=$h3;
  //echo $out;
  if (preg_match('/\/\/.+\.mp4/', $out, $m)) {
  $link="https:".$m[0];
  if (preg_match_all('/([\.\d\w\-\.\=\/\\\:\?\&\#\%\_\,\)\(\s\[\+\]]+(\.(srt|vtt)))\" srclang=\"(\w+)\" label=\"(\w+)\"/', $out, $s))
  $srts=array();
  if (isset($s[5])) {
    for ($k=0;$k<count($s[5]);$k++) {
      $srts[$s[5][$k]] = $s[1][$k];
    }
  }
  if (isset($srts["Romanian"]))
    $srt=$srts["Romanian"];
  elseif (isset($srts["Romana"]))
    $srt=$srts["Romana"];
  elseif (isset($srts["English"]))
    $srt=$srts["English"];
  } else {
    $link="";
  }
  if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, trim($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location:\s*(http.+)/i",$h,$m))
    $link=trim($m[1]);
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
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\s\[\]]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"videyo.") !== false) {
  //https://www.videyo.xyz/source/1fl0u19uvhsa
  //https://videyo.net/embed-1fl0u19uvhsa.html
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.videyo.xyz");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\s\[\]]*(\.(mp4|m3u8)))/', $h3, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h3, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"mixdrop.") !== false) {
  //https://mixdrop.co/e/eaeuizxtz0
  //https://mixdrop.co/f/mxgr3tvc
  $filelink=str_replace("/f/","/e/",$filelink);
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
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match("/(\/\/[\w|\.\:\?\&\/\=\_\-]+\.mp4\?[\w|\.\:\?\&\/\=\_\-]+)[\'\"]/",$out,$m)) {
      $link="https:".$m[1];
      if (preg_match("/\.sub\s*\=\s*\"(.*?)\"/",$out,$s)) {
       $srt= $s[1];
       if (strpos($srt,"http") === false) $srt="https:".$srt;
      }
  } else {
    $link="";
  }
  //echo $srt;
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
} elseif (strpos($filelink,"gamovideo.") !== false) {
  //http://gamovideo.com/gd82bzc3i6eq
  //http://gamovideo.com/embed-3zcqynp8vcu2-640x360.html
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
$html=file_get_contents($filelink);
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
  $host=parse_url($filelink)['host'];
  $cookie=$base_cookie."hdpopcorns.dat";
  preg_match("/(movie\_|episode\_)(.*?)\.html/",$filelink,$m);
  //print_r ($m);
  $id=$m[2];
  if (preg_match("/movie\_/",$filelink))
   $l="https://".$host."/home/index/GetMInfoAjax";
  else
   $l="https://".$host."/home/index/GetEInfoAjax";
  $post="pass=".$id;
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Connection: keep-alive',
  'Referer: https://'.$host.'/movie_aTo2MzIzOw.html');
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode(json_decode($h,1),1);
  //print_r ($r);
  $link=$r['val'];
  if ($r['subs'][0]['path'])
    $srt = "https://".$host.$r['subs'][0]['path'];
  //die();
} elseif (strpos($filelink,"lookmovie.ag") !== false) {
  function password_generate($chars) {
   $data = '_1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
   return substr(str_shuffle($data), 0, $chars);
  }
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  //preg_match_all("/fe(\d{2})\./",$h,$m);
  preg_match_all("/(78|79)\.\d{1,3}\.\d{1,3}\.\d{1,3}/",$h,$m);

  $max=count($m[0])-1;
  $alt=$m[0][rand(0,$max)];
  if (preg_match_all("/track\s*src=\"(\S+)\"\s*kind=\"subtitles\"\s*label=\"(.*?)\"/msi",$h,$m)) {
   foreach ($m[2] as $key => $value)
    if ($value == "English") {
     $srt=$m[1][$key];
     break;
    }
    if (strpos($srt,"http") === false) $srt="https://lookmovie.ag".$srt;
 }
  $t1=explode("id_movie='",$h);
  $t2=explode("'",$t1[1]);
  $id=$t2[0];
  $token="03";
  //$token=file_get_contents($base_cookie."look_token.txt");
  //$l="https://lookmovie.ag/api/v1/movies/storage/?id_movie=".$id."&token=&sk=null&step=2";
  //$token=rec("6Ley5moUAAAAAJxloiuF--u_uS28aYUj-0E6tSfZ","aHR0cHM6Ly9sb29rbW92aWUuYWc6NDQz","view","https://lookmovie.ag");
  $l="https://false-promise.lookmovie.ag/api/v1/storage/movies?id_movie=".$id."&token=".$token."&sk=null&step=1";
  //echo $l;
  $head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: https://lookmovie.ag',
'Connection: keep-alive',
'Referer: https://lookmovie.ag/movies/view/6619188-the-favorite-2019');
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_REFERER, "https://lookmovie.ag");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $time=$r['data']['expires'];
  $token=$r['data']['accessToken'];

  //$time=time();
  //$token=password_generate(22);
  //$token="QZlccqcdGzb7UbMiXvSl-Q";
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=true";
  $l="https://lookmovie.ag/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8?extClient=false";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, "https://lookmovie.ag");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  if (isset($r['720p']))
   $link=$r['720p'];
  elseif (isset($r['480p']))
   $link=$r['480p'];
  elseif (isset($r['360p']))
   $link=$r['360p'];
  else
   $link="";
*/
  //echo $link;
  //78.47.3.61
   //$link = preg_replace("/fe\d{2,3}|vii|xii/","fe".$alt,$link);
  // $link=preg_replace("/https\:\/\/\w+\.lookmovie\.ag/","http://78.47.3.61",$link);
   //$link=str_replace("https","http");
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, "https://lookmovie.ag");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
  $link=file_get_contents($base_cookie."look_token.txt");
  if ($link && $flash != "flash")
   $link=$link."|Origin=".urlencode("https://lookmovie.ag")."&Referer=".urlencode("https://lookmovie.ag");
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
} elseif (preg_match("/fembed\.|gcloud\.live|bazavox\.com|xstreamcdn\.com|smartshare\.tv|streamhoe\.online/",$filelink)) {
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
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://".$host."/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
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
} elseif (strpos($filelink,"facebook") !== false) {
$pattern = '/(video_id=|videos\/)([0-9a-zA-Z]+)/';
preg_match($pattern,$filelink,$m);
$filelink="https://www.facebook.com/video/embed?video_id=".$m[2];
//echo $filelink;
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
//https://video-otp1-1.xx.fbcdn.net/hvideo-atn2-prn/v/r6i7GhhicDIncA34OyVmH/live-dash/live-md-a/2211900909099438_0-init.m4a?ms=1
      //echo $h1;
      //$h1=str_between($h1,'videoData":[',',false,0.9]]]});}');
      //$r=json_decode($h1,1);
      //print_r ($r);
      //echo $h1;
      $h1=str_replace("\\","",$h1);
      preg_match('/(?:hd_src|sd_src)\":\"([\w\-\.\_\/\&\=\:\?]+)/',$h1,$m);
      //print_r ($m);
      $link=$m[1];
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
    if (preg_match("/location:\s*(http.+)/i",$h,$m))
      $host=parse_url(trim($m[1]))['host'];
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'Connection: keep-alive',
     'Referer: https://'.$host.'/preview-'.$id.'-1280x665.html',
     'Cookie: ref_url='.urlencode($filelink).'; e_'.$id.'=123456789',
     'Upgrade-Insecure-Requests: 1');
    $l = "https://".$host."/iframe-" . $id . "-954x562.html";

    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_NOBODY,0);
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
    if (strpos("http", $srt) === false && $srt)
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
} elseif (strpos($filelink,"vcstream.to") !== false || strpos($filelink,"vidcloud.") !== false) {
  $cookie=$base_cookie."vcstream.dat";
  $ua = $_SERVER['HTTP_USER_AGENT'];
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
  $a1=$_SERVER['HTTP_REFERER'];
  $a2=explode("?",$a1);
  $hash_path = dirname($a2[0]);
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
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\/v\.mp4))/', $out, $m);
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
  
} elseif (strpos($filelink,"streamplay.to") !== false || strpos($filelink,"streamplay.me") !== false) {

//$filelink = "https://streamplay.to/hpeg1vyu75yc";
//$filelink="https://streamplay.to/9cqvwxftcqez";
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
    preg_match('/(?:\/\/|\.)(streamplay\.(?:to|club|top|me))\/(?:embed-|player-)?([0-9a-zA-Z]+)/', $filelink, $m);
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
    $head=array('Cookie: file_id=13136922; ref_yrp=; ref_kun=1; BJS0=1');
    $l="https://".$host."/player-".$id.".html";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_REFERER, $l1);
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
    if (strpos("http", $srt) === false && $srt)
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
  $h2 = curl_exec($ch);
  curl_close($ch);

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
} elseif (strpos($filelink,"hqq.tv") !== false || strpos($filelink,"hqq.watch") !== false || strpos($filelink,"waaw.tv") !== false || strpos($filelink,"waaw1.tv") !== false  || strpos($filelink,"hindipix.in") !== false  || strpos($filelink,"pajalusta.") !== false) {
//echo $filelink;
  //if (!file_exists($base_script."filme/result.txt")) die();
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

function unicodeString($str, $encoding=null) {
    if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
    return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', function($match) use ($encoding) {
        return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
    }, $str);
}
function indexOf($hack,$pos) {
    $ret= strpos($hack,$pos);
    return ($ret === FALSE) ? -1 : $ret;
}
function aa($data){
   $OI="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
   //var o1,o2,o3,h1,h2,h3,h4,bits,i=0,
   $i=0;
   $c1="";
   $c2="";
   $c3="";
   $h1="";
   $h2="";
   $h3="";
   $h4="";
   $bits="";
   $enc="";
   do {
     $h1 = indexOf($OI,$data[$i]);
     $i++;
     $h2 = indexOf($OI,$data[$i]);
     $i++;
     $h3 = indexOf($OI,$data[$i]);
     $i++;
     $h4 = indexOf($OI,$data[$i]);
     $i++;
     //echo $h1." ".$h2." ".$h3." ".$h4."\n";
     $bits=$h1<<18|$h2<<12|$h3<<6|$h4;
     $c1=$bits>>16&0xff;
     $c2=$bits>>8&0xff;
     $c3=$bits&0xff;
     //echo $c1." ".$c2." ".$c3."\n";
     if($h3==64){
       $enc .=chr($c1);
     }
     else
     {
       if($h4==64){
         $enc .=chr($c1).chr($c2);
       }
       else {
         $enc .=chr($c1).chr($c2).chr($c3);
       }
     }
   }
   while($i < strlen($data));
return $enc;
}

function bb($s){
  $ret="";
  $i=0;
  for($i=strlen($s)-1;$i>=0;$i--) {
    $ret .=$s[$i];
  }
return $ret;
}
    function K12K($a, $typ) {
        $codec_a = array("G", "L", "M", "N", "Z", "o", "I", "t", "V", "y", "x", "p", "R", "m", "z", "u",
                   "D", "7", "W", "v", "Q", "n", "e", "0", "b", "=");
        $codec_b = array("2", "6", "i", "k", "8", "X", "J", "B", "a", "s", "d", "H", "w", "f", "T", "3",
                   "l", "c", "5", "Y", "g", "1", "4", "9", "U", "A");
        if ('d' == $typ) {
            $tmp = $codec_a;
            $codec_a = $codec_b;
            $codec_b = $tmp;
        }
        $idx = 0;
        while ($idx < count($codec_a)) {
            $a = str_replace($codec_a[$idx], "___",$a);
            $a = str_replace($codec_b[$idx], $codec_a[$idx],$a);
            $a = str_replace("___", $codec_b[$idx],$a);
            $idx += 1;
        }
        return $a;
    }

    function xc13($arg1) {
        $lg27 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        $l2 = "";
        $l3 = array(0, 0, 0, 0);
        $l4 = array(0, 0, 0);
        $l5 = 0;
        while ($l5 < strlen($arg1)) {
            $l6 = 0;
            while ($l6 < 4 && ($l5 + $l6) < strlen($arg1)) {
                $l3[$l6] = strpos($lg27,$arg1[$l5 + $l6]);
                $l6 += 1;
            }
            $l4[0] = (($l3[0] << 2) + (($l3[1] & 48) >> 4));
            $l4[1] = ((($l3[1] & 15) << 4) + (($l3[2] & 60) >> 2));
            $l4[2] = ((($l3[2] & 3) << 6) + $l3[3]);

            $l7 = 0;
            while ($l7 < count($l4)) {
                if ($l3[$l7 + 1] == 64)
                    break;
                $l2 .= chr($l4[$l7]);
                $l7 += 1;
            }
            $l5 += 4;
        }
        return $l2;
    }
function decode3($w,$i,$s,$e){
$var1=0;
$var2=0;
$var3=0;
$var4=[];
$var5=[];
while(true){
if($var1<5)
     array_push($var5,$w[$var1]); //$var5.push($w[$var1]); //array_push($var5,$w[$var1]) ????
else if($var1<strlen($w))
     array_push($var4,$w[$var1]); //$var4.push($w[$var1]);
$var1++;
if($var2<5)
     array_push($var5,$i[$var2]); //$var5.push($i[$var2]);
else if($var2<strlen($i))
     array_push($var4,$i[$var2]); //$var4.push($i[$var2]);
$var2++;
if($var3<5)
     array_push($var5,$s[$var3]); //$var5.push($s[$var3]);
else if($var3<strlen($s))
     array_push($var4,$s[$var3]); //$var4.push($s[$var3]);
$var3++;
//if (len(w) + len(i) + len(s) + len(e) == len(var4) + len(var5) + len(e)):
if(strlen($w)+strlen($i)+strlen($s)+strlen($e) == count($var4) + count($var5) +strlen($e))
  break;
}
$var6=join('',$var4);
$var7=join('',$var5);
//print_r ($var5);
//die();
$var2=0;
$result=[];
//echo chr(intval(substr($var6,$var1,2),36)-$ad);
for($var1=0;$var1<count($var4);$var1=$var1+2){
   $ad=-1;
   if(ord($var7[$var2])%2)  //if (ord(var7[var2]) % 2):
     $ad=1;
array_push($result,chr(intval(substr($var6,$var1,2),36)-$ad));  //chr(int(var6[var1:var1 + 2], 36) - ll11))
$var2++;
if($var2>=count($var5))
$var2=0;
}
return join('',$result);
}
function decode_wise($x) {
  $h2="";
  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$x,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

  $t1=explode(";;",$h2);
  $h2=$t1[1];
  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
  $y=$x." ".$h2;
  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$y,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
  $t1=explode(";;",$h2);
  $h2=$t1[1];
  if (preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m))
     $h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
  return $h2;
}

  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
$ua=$user_agent;
//$filelink="https://waaw.tv/watch_video.php?v=YS2pcOGtyneo";
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
$ua="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10', #'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0";
$pattern = "@(?:\/\/|\.)((?:waaw1?|netu|hqq|hindipix)\.(?:tv|watch|in))\/(?:watch_video\.php\?v|.+?vid)=([a-zA-Z0-9]+)@";
  if (preg_match($pattern,$filelink,$m))
    $vid=$m[2];
  elseif (preg_match("/(hqq|netu)(\.tv|\.watch)\/player\/hash\.php\?hash=\d+/",$filelink)) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_ENCODING, '');
      curl_setopt($ch, CURLOPT_REFERER, "http://hqq.watch/");
      //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      $h1=urldecode($h1);
      //echo urldecode("%3c");
      //echo $h1;
      //vid':'
     preg_match("/vid\s*\'\:\s*\'(?P<vid>[^\']+)\'/",$h1,$m);
     $vid=$m["vid"];
  }
$l="http://hqq.tv/player/embed_player.php?vid=".$vid."&autoplay=no";
$cookie=$base_cookie."hqq.txt";
/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://hqq.tv");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
$h=urldecode($h);
*/
/*
$iss=base64_encode($_SERVER['REMOTE_ADDR']);
$vid="";
$vid=str_between($h,"videokeyorig='","'");
$at="";
$http_referer="";
if (preg_match_all("/eval\(function\(w\,i\,s\,e\)(.*?)\<\/script/ms",$h,$r)) {
  //$h .=decode_wise($r[1][0]);
  //$h .=decode_wise($r[1][1]);
  $vid=str_between($h,"videokeyorig='","'");
  $at=str_between($h,"attoken='","'");
  $http_referer=str_between($h,"server_referer='","'");
}
*/
$l="https://hqq.tv/player/embed_player.php";
$post="pop=1&v=".$vid;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: '.$filelink.'',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Origin: https://hqq.tv',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
$h=urldecode($h);
$y="";
if (preg_match("/get_md5/",$h)){
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m)) {
  $srt=$m[1];
  $srt=urldecode(str_replace("https","http",$srt));
  //echo $srt;
  }    $l="http://hqq.tv/player/get_md5.php?ver=3&secure=0&adb=0%2F&v=".$vid."&token=";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'Accept: */*',
     'Accept-Language: en-US,en;q=0.5',
     'Accept-Encoding: deflate',
     'X-Requested-With: XMLHttpRequest'
    ));
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, "http://hqq.watch/");
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    $file=str_between($h,'obf_link":"','"');
    $y=decodeUN($file);
    if (strpos($y,"http") === false && $y) $y="https:".$y;
    if ($y)
      $link=$y.".mp4.m3u8";
    else
     $link="";
} else {
 $link="";
}

/*
$h="";
$l_ref="";
if ($vid && $at) {
      $l="http://hqq.tv/sec/player/embed_player_9331445831509874.php?vid=".$vid."&need_captcha=1&iss=".$iss."&vid=".$vid."&at=".$at."&autoplayed=yes&referer=on&http_referer=".$http_referer."&pass=&embed_from=&need_captcha=0&hash_from=&secured=0&token=03";
      $l_ref=$l;
      //echo $l;
      //$l="https://hqq.tv/player/embed_player.php?secure=1&vid=Rk5LeUN2VkVHMmcvN256WlE1LytVUT09";
      //$l="https://hqq.tv/sec/player/embed_player_7988862232204833.php?vid=eHhqYTk3aFlVbkoxaFdsbTIvNWpLQT09&need_captcha=1&iss=OTUuNzYuMTkuNDM%3D&vid=eHhqYTk3aFlVbkoxaFdsbTIvNWpLQT09&at=7b0b612c8b69967673ea4b401b64e1fd&autoplayed=yes&referer=on&http_referer=aHR0cHM6Ly92ZXppb25saW5lLm5ldC9mYWxsaW5nLWlubi1sb3ZlLWhhbnVsLWN1LW5vcm9jLTIwMTkuaHRtbA%3D%3D&pass=&embed_from=&need_captcha=1&secure=0&gtoken=&lpo=1&g-recaptcha-response=03";
      $head=array('Cookie: gt=536606632dec68aa2bd81d153ce3f4a7');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://hqq.watch");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_HEADER,1);
      $h = curl_exec($ch);
      curl_close($ch);
}
$h=urldecode($h);
//echo $h;
if (preg_match("/get_md5/",$h)){
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m)) {
  $srt=$m[1];
  $srt=urldecode(str_replace("https","http",$srt));
  }
  $vid="";
  $at="";
  $vid_server="";
  $vid_link="";
  $vid=str_between($h,'videokey = "','"');
  preg_match_all("/eval\(function\(w\,i\,s\,e\)(.*?)\<\/script/ms",$h,$r);
  if (isset($r[1][0])) {
    $e=decode_wise($r[1][0]);
    preg_match('/at\s*=\s*"([^"]*?)"/ms',$e,$m);
    $at=$m[1];
  }
  if (isset($r[1][1])) {
   $e=decode_wise($r[1][1]);
   //echo $h;
   preg_match("/server_2=\"\s*\+*encodeURIComponent\(([^\)]+)/",$h,$m);
   $pat='/'.$m[1].'\s*=\s*"([^"]*?)"/ms';
   preg_match($pat,$e,$m);
   $vid_server=$m[1];
   preg_match("/link_1=\"\s*\+*encodeURIComponent\(([^\)]+)/ms",$h,$m);
   $pat='/'.$m[1].'\s*=\s*"([^"]*?)"/ms';
   preg_match($pat,$e,$m);
   $vid_link=$m[1];
  }
  $y="";
  if ($vid && $vid_server && $vid_link) {
    $l="http://hqq.tv/player/get_md5.php?&ver=2&need_captcha=1&at=".$at."&adb=0%2F&b=1&link_1=".$vid_link."&server_2=".$vid_server."&vid=".$vid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, $l_ref);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER,1);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    $y="";
    if (preg_match("/Location:\s*(\S+)/",$h,$m)) {
    //print_r ($m);
    $y=$m[1];
    }
    //$x=json_decode($h,1);
    //$file=str_between($h,'file":"','"');
    //$file= $x["obf_link"];
    //$file=str_between($h,'obf_link":"','"');
    //$y=decodeUN($file);
    if (strpos($y,"http") === false && $y) $y="https:".$y;
  }
  if ($y)
   $link=$y.".mp4.m3u8";
  else
   $link="";
} else {
 $link="";
}
*/
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
    $link=$m[1][count($m)-1];
  else
    $link="";
} elseif (strpos($filelink, 'youtu') !== false){
   //https://www.youtube-nocookie.com/embed/kfQTqjvaezM?rel=0
    $filelink=str_replace("https","http",$filelink);
    $filelink=str_replace("youtube-nocookie","youtube",$filelink);
    //echo $filelink;
    $link=youtube($filelink);
    /*
    if ($link && strpos($link,"m3u8") === false) {
      $t1=explode("?",$link);
      $link=$t1[0]."/youtube.mp4?".$t1[1];
    }
    */
    //$link=$link."&video_link/video.mp4";
    //$link=$link."&type=.mp4";
} elseif (strpos($filelink,'vimeo.com') !==false){
  //http://player.vimeo.com/video/16275866
  ///cgi-bin/translate?info,,http://vimeo.com/16275866
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  if ($referer)
  curl_setopt($ch, CURLOPT_REFERER, $referer);
  else
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
  $t1=explode("class=player></div><script>(function(t,e){var r=",$h);
  $t2=explode(";if(!r.request",$t1[1]);
  $h2=$t2[0];
  //echo $h2;
  //$t1=explode("video/mp4",$h2);
  $r=json_decode($h2,1);
  //print_r ($r);
  $p=$r["request"]["files"]["progressive"];
  $link=$p[0]["url"];
  if (!$link) {
   $t1=explode('mime":"video/mp4',$h);
   $t2=explode('url":"',$t1[2]);
   $t3=explode('"',$t2[1]);
   $link=$t3[0];
  }
  $link=str_replace("https","http",$link);
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
  if (strpos($filelink,"embed") !== false) {
    $h=file_get_contents($filelink);
    //echo $h;
    $l=str_between($h,'stream_h264_url":"','"');
    $link=str_replace("\\","",$l);
  } else {
    $html = file_get_contents($filelink);
    $h=urldecode($html);
    $link=urldecode(str_between($h,'video_url":"','"'));
    if (!$link) {
    $t1 = explode('sdURL', $html);
    $sd=urldecode($t1[1]);
    $t1=explode('"',$sd);
    $sd=$t1[2];
    $sd=str_replace("\\","",$sd);
    $n=explode("?",$sd);
    $nameSD=$n[0];
    $nameSD=substr(strrchr($nameSD,"/"),1);
    $t1 = explode('hqURL', $html);
    $hd=urldecode($t1[1]);
    $t1=explode('"',$hd);
    $hd=$t1[2];
    $hd=str_replace("\\","",$hd);
    $n=explode("?",$hd);
    $nameHD=$n[0];
    $nameHD=substr(strrchr($nameHD,"/"),1);
    if ($hd <> "") {
     $link = $hd;
    }
    if (($sd <> "") && ($hd=="")) {
     $link = $sd;
    }
    }
  }
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
  $l="http://my.mail.ru/+/video/meta/".$m[1];
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_REFERER, "http://my9.imgsmail.ru/r/video2/uvpv3.swf?3");
   curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
   curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   //print_r ($r);
   $link="http:".$r["videos"][0]["url"];

} elseif (strpos($filelink,"ok.ru") !==false) {
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //echo $filelink;
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
} elseif (strpos($filelink,"mystream.to") !==false || strpos($filelink,"mstream.cloud") !==false  || strpos($filelink,"mstream.xyz") !==false) {
  //https://embed.mystream.to/pufpln9x8ejh
  //mstream.xyz
  require_once('AADecoder.php');
  include ("obfJS.php");
  include ("jj.php");
function vv($a157,$a158) {
  $a159 = array();
  $a160 = 0x0;
  $a161="";
  $a162 = '';
  for($a163 = 0x0; $a163 < 0x100; $a163++) {
    $a159[$a163] = $a163;
  }
  for($a163 = 0x0; $a163 < 0x100; $a163++) {
    $a160 = ($a160 + $a159[$a163] + ord($a157[$a163 % strlen($a157)])) % 0x100;
    $a161 = $a159[$a163];
    $a159[$a163] = $a159[$a160];
    $a159[$a160] = $a161;
  }
  $a163 = 0x0;
  $a160 = 0x0;
  for($a191 = 0x0; $a191 < strlen($a158); $a191++) {
    $a163 = ($a163 + 0x1) % 0x100;
    $a160 = ($a160 + $a159[$a163]) % 0x100;
    $a161 = $a159[$a163];
    $a159[$a163] = $a159[$a160];
    $a159[$a160] = $a161;
    $a162 .= chr(ord($a158[$a191]) ^ $a159[($a159[$a163] + $a159[$a160]) % 0x100]);
  }
  return $a162;
}
  $k=0;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  while ($k<5) {
   $h=file_get_contents($filelink);
   if (preg_match("/Video not found/",$h)) break;
   $h=decode_code($h);
   $h1=AADecoder::decode($h);
   $pat="/setAttribute\(\'src\', *\'(http.+?mp4)\'\)/";
   if (preg_match($pat,$h1)) break;
   $pat1="/setAttribute\(\'src\',\s*vv\(key,\s*atob\(\'(\S+)\'/";
   if (preg_match($pat1,$h1)) break;
   sleep(1);
   $k++;
  }
  if (strpos($h1,"Video not found") === false) {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h1, $s))
  $srt=$s[1];
  $pat="/setAttribute\(\'src\', *\'(http.+?mp4)\'\)/";
  if (preg_match($pat, $h1, $m)) {
    $link=$m[1];
    if (strpos($link,"http") === false && $link) $link="https:".$link;
  } else {
    $pat1="/setAttribute\(\'src\',\s*vv\(key,\s*atob\(\'(\S+)\'/";
    if (preg_match($pat1, $h1,$q))
     $encoded=$q[1];
    else
     $encoded="";
    $hash="";
    $t1=explode("<script>",$h1);
    $t2=explode("</script",$t1[1]);
    if ($z1 = jjdecode($t2[0])) {
     $z1=preg_replace_callback(
      "/atob\(\'(.*?)\'\)/ms",
      function ($matches) {
       return base64_decode($matches[1]);
      },
      $z1
     );
    preg_match("/(\w{20,})/",$z1,$p);
    $hash=$p[1];
   }
   $t2=explode("</script",$t1[2]);
   $enc=$t2[0];
   $dec=obfJS();
   $h=$l=$n="";
   if(preg_match("/var\s*h\=\'(\w+)\'/ms",$dec,$m))
    $h=$m[1];
   if (preg_match("/var\s*l\=\'(\w+)\'/ms",$dec,$m))
    $l=$m[1];
   if (preg_match("/var\s*n\=\'(\w+)\'/ms",$dec,$m))
    $n=$m[1];
   $test=array();
   for($j = 0x0; $j < strlen($l); $j++) {
     for($k = 0x0; $k < strlen($n); $k++) {
       $test[$l[$j].$n[$k]] = $h[$j + $k];
     }
   }
   $key="";
   for($i = 0x0; $i < strlen($hash); $i += 0x2) {
      $key = $key.$test[$hash[$i].$hash[$i + 1]];
   }
   $link=vv($key,base64_decode($encoded));
   if (strpos($link,"http") === false && $link) $link="https:".$link;
  }
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
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4|master\.m3u8))/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {
    $link="";
  }
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
  $r=json_decode($h,1);
  //print_r ($r);
  $link=$r['src']['src'];
} elseif (strpos($filelink,"azm.to") !==false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  //$ua="Mozilla/5.0(Linux;Android 10.1.2) MXPlayer";
  $cookie=$base_cookie."azm.dat";
  $cookie1=$base_cookie."azm1.dat";
  sleep(5);
  $recaptcha=file_get_contents($cookie);

  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Cookie:  response='.$recaptcha.'',
  'Upgrade-Insecure-Requests: 1');
  //$ua="MX Player";

  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, "https://azm.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
*/
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: response=".$recaptcha."\r\n"
  )
);
$context = stream_context_create($opts);
$h=@file_get_contents($filelink,false,$context);
  if (strpos($filelink,"getlink.php") !== false) {
    $t1=explode('source src="',$h);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
  } else {
    $t1=explode("Location:",$h);
    $t2=explode("\n",$t1[1]);
    $link=trim($t2[0]);
  }

} elseif (strpos($filelink,"xmovies8.tv") !==false) {
  $t1=explode("id=",$filelink);
  $t2=explode("&s=",$t1[1]);
  $id=$t2[0];
  $serv=$t2[1];
$l="https://xmovies8.tv/ajax/v4_get_sources?s=".$serv."&id=".$id."&_=";
//echo $l;
//$l="https://xmovies8.tv/ajax/v4_get_sources?s=vserver&id=132948&_=";
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://xmovies8.tv/watch-earthquake-bird-2019-1080p-hd-online-free/watching.html',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0";
  $cookie=$base_cookie."xmovies8.txt";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://xmovies8.tv");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $r=json_decode($html,1);
  //print_r ($r);

  $l=$r['value'];
  if ($serv <> "hserver") {
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://xmovies8.tv");
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
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4)))/', $h, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s)) {
  $srt=$s[1];
  //if ($srt && strpos($srt,"http") === false) $srt="https://jetload.net/".$srt;
  if (strpos($srt,"empty.srt") !== false) $srt="";
  }
  } else {   //PTserver
    $t1=explode('file":"',$h);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
  }
  } else {
  //set_time_limit(360);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://xmovies8.tv");
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
  /*
  include ("hydrax.php");
  $a1=$_SERVER['HTTP_REFERER'];
  $a2=explode("?",$a1);
  $p = dirname($a2[0]);
  $out=hydrax($key,$slug,$origin,$flash,$p);
  if ($out) {
    file_put_contents("lava.m3u8",$out);
    if ($flash == "flash") {
      $link = $p."/lava.m3u8";
    } else
      $link="http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  } else {
    $link="";
  }
  */
  $a1=$_SERVER['HTTP_REFERER'];
  $a2=explode("?",$a1);
  $hash_path = dirname($a2[0]);
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
  $x=json_decode($html,1);
  //print_r ($x);
  if (isset($x['servers'])) {
  $server=$x['servers']['redirect'][0];
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
  }
   if ($link && $flash != "flash" && $serv == "vserver")
     $link=$link."|Referer=".urlencode("https://xmovies8.tv");
   if ($link && $flash != "flash" && $serv == "hserver")
     $link=$link."|Referer=".urlencode($origin)."&Origin=".urlencode($origin);
  //$link="https://v.bighost.be/hls/082c3b889ee603c4825b99c2bfd162af/082c3b889ee603c4825b99c2bfd162af.playlist.m3u8";
} elseif (strpos($filelink,"streamloverx.com") !==false) {
//echo $filelink;
//die();
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
  if (preg_match("/\#slug\=(\S+)/",$html,$m))
     $slug=$m[1];
  else {
    $t1=explode('slug","value":"',$html);
    $t2=explode('"',$t1[1]);
    $slug=$t2[0];
  }
  $t1=explode('key":"',$html);
  $t2=explode('"',$t1[1]);
  $key=$t2[0];
  //echo $slug."\n".$key;
  //$origin="https://streamloverx.com";
  $a1=$_SERVER['HTTP_REFERER'];
  $a2=explode("?",$a1);
  $hash_path = dirname($a2[0]);
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
  $server=$x['servers']['redirect'][0];
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
}

//////////////////////////////////////////////////////////////////
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
   //echo $movie_file;
     //$movie_file="v.mp4";
   if (preg_match("/m3u8/",$movie_file))
    $srt_name = substr($movie_file, 0, -4).".srt";
   else if (preg_match("/mp4|flv/",$movie_file))
    $srt_name = substr($movie_file, 0, -3).".srt";
   else
    $srt_name= $movie_file.".srt";

   $srt_name = rawurldecode($srt_name);
   if (strpos($srt_name,".srt") === false)  $srt_name=$srt_name.".srt";
   $srt_name=str_replace("..srt",".srt",$srt_name);
   //if (preg_match("/mp4|flv|m3u8/",$link)) {
   $new_file = $base_sub.$srt_name;
   if (!file_exists($base_sub."sub_extern.srt")) {
   $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: https://'.parse_url($filelink)["host"].'',
   'Connection: keep-alive',
   'Referer: '.$filelink.'');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   //curl_setopt($ch,CURLOPT_REFERER,"https://embed.iseek.to");
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/7");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
   //echo $h;
   } else {
    $h=file_get_contents($base_sub."sub_extern.srt");
   }
   if ($h) {
   //$h=mb_convert_encoding($h, 'UTF-8');

   //echo $enc;
   //die();
   if (strpos($h,"&lrm;") !== false) { //Netflix styling
    $h=str_replace("&lrm;","",$h);
    $h=preg_replace("/(\d+\:\d+\:\d+\.\d+ --> \d+\:\d+\:\d+\.\d+)(.+)/","$1",$h);
    $h=preg_replace("/\<c.*?\>/","",$h);
    $h=preg_replace("/\<\/c.*?\>/","",$h);
   }
   $h=str_replace("\n","\r\n",$h);
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
            $pattern1 = '#(\d{2}):(\d{2}):(\d{2})\.(\d{3})#'; // '01:52:52.554'
            $pattern2 = '#(\d{2}):(\d{2})\.(\d{3})#'; // '00:08.301'
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
}
function fix_srt($contents) {
$n=1;
$output="";
$bstart=false;
$file_array=explode("\n",$contents);
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line) && !$bstart)
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
return $output;
}
//echo $h;
   $h=fix_srt($h);

   $h=json_encode($h);
   $h=str_replace("\u0083","",$h);
   $h=str_replace("\u0098","",$h);
   $h=json_decode($h);

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
if (strpos($movie,"http") === false) $movie="";
// Set HW+ mod //
$hw="/hqq\.|hindipix\.|pajalusta\.|lavacdn\.xyz|mcloud\.to|putload\.|thevideobee\.";
$hw .="|flixtor\.|0123netflix|mangovideo|waaw1?|lookmovie\.ag|onlystream\.|archive\.org";
$hw .="|hxload.|jetload\.net|azm\.to|movie4k\.ag|hlsplay\.com|videobin\.|moonline\./";
if ($flash== "mpc") {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$movie.'"';
  if (strpos($filelink,"ok1.ru") !== false || strpos($filelink,"raptu") !== false || strpos($filelink,"rapidvideo") !== false || strpos($filelink,"hqq.tv") !== false || strpos($filelink,"google") !== false || strpos($filelink,"blogspot") !== false) {
  $mpc=trim(file_get_contents($base_pass."vlc.txt"));
  $c = '"'.$mpc.'" --fullscreen --sub-language="ro,rum,ron" --sub-file="'.$base_sub.$srt_name.'" "'.$movie.'"';
  }
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
} elseif ($flash == "direct") {
   $movie_file=substr(strrchr($movie, "/"), 1);
   header('Content-type: application/vnd.apple.mpegURL');
   if (strpos($filelink,"ok.ru") !== false && strpos($filelink,"ok=") !== false) {
     $movie = substr($filelink, 0, -5);
     $movie_file=substr(strrchr($movie, "/"), 1);
   }
   header('Content-Disposition: attachment; filename="'.$movie_file.'"');
   header("Location: $movie");
} elseif ($flash == "chrome") {
  $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";
  header("Location: $c");
} elseif ($flash == "mp") {

  if (!preg_match($hw,$filelink)) // HW=1;SW=2;HW+=4
   $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";b.decode_mode=1;end";
  else
   $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";end";
  $t1=explode("|",$movie);
  if (substr($t1[0], -4) == "m3u8")
   $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";end";

  //$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";
  echo $c;
  die();
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
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="../jwplayer.js"></script>
';
echo '
</HEAD>
<body><div id="mainnav">
<div id="container"></div>
<script type="text/javascript">
var player = jwplayer("container");
jwplayer("container").setup({
';
//http://my1.imgsmail.ru/r/video2/uvpv3.swf?58

echo '
"playlist": [{
"title": "'.preg_replace("/\n|\r/"," ",$pg).'",
"sources": [{"file": "'.$movie.'", "type":"'.$type.'"
}],
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
"fallback": false,
"wmode": "direct",
"title": "'.preg_replace("/\n|\r/"," ",$pg).'",
"abouttext": "'.preg_replace("/\n|\r/"," ",$pg).'",
"stagevideo": true
});
player.addButton(
  //This portion is what designates the graphic used for the button
  "../download.svg",
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
