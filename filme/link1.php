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
    if (preg_match_all ("/^(?!#).+/m",$h,$m)) {
     $pl=$m[0];
     if (substr($pl[0], 0, -2) == "//")
       $base="https:";
     elseif ($pl[0][0] == "/")
       $base=$base2;
     elseif (preg_match("/http(s)?:/",$pl[0]))
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
//}
}
$filelink=str_replace("&amp;","&",$filelink);
//echo $filelink;
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
  include("rec.php");
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
  //echo $filelink;
  $key="6LeLo6IZAAAAAD1sHLlRReThaDfdZvxZ07nS0olp";
  $co="aHR0cHM6Ly9hZmRhaC5pbmZvOjQ0Mw..";
  $sa="play1";
  $loc="https://afdah.info";
  $token=rec($key,$co,$sa,$loc);
  $post="g-recaptcha-response=".$token;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post),
  'Origin: https://afdah.info',
  'Connection: keep-alive',
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
    $srt="https://afdah.info".$s[0];
  }
  $host="afdah.info";
  if ($host && $flash <> "flash") {
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://afdah.info");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match_all("/http.+/",$h,$m))
    $link=trim($m[0][count($m[0])-1]);
  $link=$link."|Referer=".urlencode("https://".$host);
  }
}
if (preg_match("/yifytv\./",$filelink)) {
  $t1=explode("?",$filelink);
  $post=$t1[1];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $l="https://yifytv.top/wp-admin/admin-ajax.php";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://yifytv.top/movies/mind-games-2021/',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: https://yifytv.top',
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  $z=$x['embed_url'];
  $t1=explode('iframe src="',$z);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://yifytv.top");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("FirePlayer(vhash,",$h);
  $t2=explode(", false);",$t1[1]);
  $u=json_decode($t2[0],1);
  //print_r ($u);
  $host=parse_url($l)['host'];
  $videoServer=$u['videoServer'];
  $videoUrl=$u['videoUrl'];
  $videoDisk=$u['videoDisk'];
  $link="https://".$host.$videoUrl."?s=".$videoServer."&d=".base64_encode($videoDisk);
  if ($host && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://".$host);
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
if (preg_match("/sockshare/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
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
if (preg_match("/cdn\-\d+\.fembed\.stream/",$filelink)) {
  $link=$filelink;
  $filelink="";
  if ($flash <> "flash")
   $link=$link."|Origin=".urlencode("https://infinitum.stream")."&Referer=".urlencode("https://infinitum.stream");
}
if (preg_match("/onionbox\.org/",$filelink)) {
  if (preg_match("/onionbox\.org\/v\//",$filelink)) {
   // https://onionbox.org/v/rjRuyXc6mUO9fxW/
   //echo $filelink;
   // https://onionbox.org/v/a86qzi1t0OU8QbC/
   // https://onionbox.org/v/a86qzi1t0OU8QbC/
  require_once("JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://onionplay.uk/');
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
  $out = $jsu->Unpack($h1);
  $t1=explode('file":"',$out);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  } else {
  $link=$filelink;
  $filelink="";
  }
  if ($flash <> "flash")
   $link=$link."|Origin=".urlencode("https://onionbox.org")."&Referer=".urlencode("https://onionbox.org");
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
  if (isset($x['embed_urls']))
   $filelink=$x['embed_urls'];
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
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_REFERER,"https://apimdb.net");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  */
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
$h=@file_get_contents($filelink,false,$context);
  //echo $h;
  // https://apimdb.net/ajax/get_tv_link/?id=R3Bw_&imdb=tt0118480&s=1&e=4
  if (preg_match("/\<iframe/",$h)) {
    $t1=explode("<iframe",$h);
    $t2=explode('src="',$t1[1]);
    $t3=explode('"',$t2[1]);
    $filelink=$t3[0];
  } else {
    $t1=explode("div id='picasa",$h);
    $h=$t1[1];
    //$h=str_replace("\\","",$h);
    //echo $h;
    require_once("JavaScriptUnpacker.php");
    $jsu = new JavaScriptUnpacker();
    $out = $jsu->Unpack($h);
    $out = $jsu->Unpack($out);
    //echo $out;
    if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s)) // not sure
    $srt=$s[1];
    $t1=explode("file:'",$out);
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
if (preg_match("/play\.playm4u\.xyz/",$filelink)) {
  $t1=explode("caption=",$filelink);
  $srt=$t1[1];
  //echo $srt;
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
  // https://api-sing.vnstream.net/apiv3/5e8dd16b70eac4137a676553/5f3f1a8d4d6f1d25eb2ce99d
  //$l="https://api-sing.vnstream.net/apiv3/5e8dd16b70eac4137a676553/5f3f1a8d4d6f1d25eb2ce99d";
  $post="referrer=http://streamm4u.com&typeend=html";
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'Content-Length: '.strlen($post),
   'Origin: https://play.playm4u.xyz',
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  $v=$x['v'];
  $out = "#EXTM3U"."\n";
  $numdm_rd = count($DOMAIN_LIST_RD);
  $out .= "#EXT-X-VERSION:3"."\n";
  $out .="#EXT-X-TARGETDURATION:".$x["tgdr"]."\n";
  $out .="#EXT-X-PLAYLIST-TYPE:VOD"."\n";
  for($i = 0; $i < count($x['data'][0]); $i++) {
   $out .="#EXTINF:".$x['data'][0][$i].","."\n";
   $out .="https://".$DOMAIN_LIST_RD[$i % $numdm_rd]."/rdv".$v."/".$x["quaity"]."/".$user."/".$x['data'][1][$i].".rd"."\n";
  }
  $out .="#EXT-X-ENDLIST";
  file_put_contents("lava.m3u8",$out);
  if ($flash == "flash") {
  $t1=explode("?",$_SERVER['HTTP_REFERER']);
  $p=dirname($t1[0]);
  $link = $p."/lava.m3u8";
  } else {
  $link = "http://127.0.0.1:8080/scripts/filme/lava.m3u8";
  $link .="|Origin=".urlencode("https://play.playm4u.xyz");
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
if (preg_match("/uniquestream\./",$filelink)) {
//echo $filelink;
 $t1=explode("id=",$filelink);
 $t2=explode("&",$t1[1]);
 $id=$t2[0];
 $t1=explode("&type=",$filelink);
 $type=$t1[1];
 $host=parse_url($filelink)['host'];
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
 $l="https://".$host."/wp-admin/admin-ajax.php";
 $post="action=doo_player_ajax&post=".$id."&nume=1&type=".$type;
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
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
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
  if (strpos($link,".m3u8") !== false) {
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $link=get_max_res($html,$link);
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
if (strpos($filelink,"fsharetv.co") !== false) {
//echo $filelink;
$filelink=htmlspecialchars_decode($filelink, ENT_QUOTES);
//echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/captions\"\s+src\=\"(.*?)\"/",$h,$s)) {
    $srt="https://fsharetv.co".$s[1];
  }
  $t1=explode("Movie.setSource('",$h);
  $t2=explode("'",$t1[1]);
  $l="https://fsharetv.co/api/file/".$t2[0]."/source";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  if (isset($r['data']['file']['sources'][0]['src'])) {
   $link="https://fsharetv.co".$r['data']['file']['sources'][0]['src'];
   if ($flash <> "flash")
    $link=$link."|Referer=".urlencode("https://fsharetv.co");
  }
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://fsharetv.co");
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

  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
   $srt=$m[1];
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $h .= $jsu->Unpack($h);
  }
  //echo $h;
  if (preg_match("/(https?\:\/\/stream\d+\.streamlord\.com\:8080.+)\"/",$h,$m))
   $link=$m[1];
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
   $t1=explode('anchor.href = "',$h);
   //$t2=explode('url=',$t1[1]);
   $t3=explode('"',$t1[1]);
   $link=$t3[0];
   $link=str_replace("&#038;","&",$link);
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
if (strpos($filelink,"ling.") !== false) {
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
  if (preg_match("/\/\/[\w\d\/\_\:\.\?\-]+\.mp4/",$h,$m)) {
   $link="https:".$m[0];
   if ($link && $flash <> "flash")
    $link=$link."|Origin=".urlencode("https://ling.online")."&Referer=".urlencode("https://ling.online");
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
$ua=file_get_contents($base_pass."firefox.txt");

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
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
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
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
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
  $head1=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://bmovies.cloud/film',
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
  if (preg_match("/iframe src\=\"(.*?)\"/",$h,$m)) {
    $filelink=$m[1];
  } else {
   if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,\+]*(\.(srt|vtt)))/', $h, $s))
    $srt="https:".$s[1];
   $t1=explode('sources:',$h);
   $t2=explode('tracks',$t1[1]);
   $h=$t2[0];
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
if (strpos($filelink,"yesmovies.ag") !== false) {
//echo $filelink;
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
  //$x=json_decode($html,1);
  //print_r ($x);
  $filelink=json_decode($html,1)['src'];
  //echo $filelink;
}
if (strpos($filelink,"solarmovie.") !== false) {
//echo $filelink;
  $host=parse_url($filelink)['host'];
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $l="https://".$host."/user/status.html";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/__cf_uid\=(.*?)\;/",$h,$m);
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Cookie: __cf_uid='.$m[1].'',
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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
if (preg_match("/ff?movies\./",$filelink) && !preg_match("/123fmovies/",$filelink)) {  //123fmovies.best
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
if (strpos($filelink,"zoechip.") !== false) {
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
} elseif (strpos($filelink,"vidsrc.stream") !== false) {
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://v2.vidsrc.me/',
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
  $filelink="";
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $s))
  $srt=$s[1];
  $t1=explode('file": "',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
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
} elseif (strpos($filelink,"voe.sx") !== false) {
  // https://voe.sx/e/abx2no5yos5s
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
  $t1=explode('hls": "',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
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
  // https://streamzz.to/fZ200bWlzamw4Y2Rwc3hk
  //echo $filelink;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match_all("/location:\s+(.+)/i",$h,$m)) {
   $l=trim($m[1][count($m[1])-1]);
   $host=parse_url($l)['host'];
   preg_match("/\/x(\w+)/",$m[1][1],$n);
   $link="https://".$host."/getlink-".$n[1].".dll";
  }
} elseif (strpos($filelink,"hls.ronemo.com") !== false) {
  $t2=explode("?sub=",$filelink);
  $link=$t2[0];
  $srt=$t2[1];
  //$filelink="";
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

  $link=get_max_res($h,$link);
  }
} elseif (strpos($filelink,"ronemo.com") !== false && strpos($filelink,"hls.ronemo.com") === false) {
  // https://ronemo.com/embed/ZwGt07_hpYI
  // https://hls.ronemo.com/ZwGt07_hpYI/f/playlist.m3u8
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
} elseif (strpos($filelink,"ninjastream.to") !== false) {
  // https://ninjastream.to/watch/74GA0J8brZYBk
  //echo $filelink;
  // https://ninjastream.to/watch/GeLZzzEo1ZyOn/La.bonne.epouse.720p.mp4
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
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $html=htmlspecialchars_decode($html,ENT_QUOTES);
  //echo $html;

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
  $l="https://ninjastream.to/api/video/get";
  $post='{"id":"'.$id.'"}';
  $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Referer: '.$filelink,
   'X-Requested-With: XMLHttpRequest',
   'X-CSRF-TOKEN: '.$csrf,
   'Content-Type: application/json;charset=utf-8',
   'Content-Length: '.strlen($post),
   'Origin: https://ninjastream.to',
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['result']['playlist']))
   $link= $x['result']['playlist'];
  /*
  if (isset($r['host'])) {
   $y = xor_string($r['host'],"2");
   $link=$y.$r['hash']."/index.m3u8";
  }
  */
  if ($link && $flash <> "flash") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://ninjastream.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
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
  curl_setopt($ch,CURLOPT_REFERER,"https://ninjastream.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  
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
  include ("AADecoder.php");
  require_once("JavaScriptUnpacker.php");
  $l="https://userload.co/api/assets/userload/js/videojs.js";
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
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://userload.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
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
  // morocco="+adddebbf+"&mycountry="+eefcfcccdeef
  $l="https://userload.co/api/request/";
  $post="morocco=".$morocco."&mycountry=".$mycountry;
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
  if (strpos($h,"http") === false)
    $link="https://userload.co".trim($h);
  else
    $link=trim($h);
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://userload.co");
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
} elseif (strpos($filelink,"videovard.") !== false) {
  // https://videovard.sx/e/m97g3y0q9xcs?c1_file=https://seriale-online.net/subtitrari/7482-5-2.vtt&c1_label=Romana
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
    $srt="https:".$s[1];
  $id="";
   if (preg_match("/\/[ef]\/([0-9a-zA-Z\_\-]+)/",$filelink,$m)) {
   $id=$m[1];
   }
   if ($id) {
   include ("tear.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:90.0) Gecko/20100101 Firefox/90.0";
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Alt-Used: videovard.sx',
   'Connection: keep-alive',
   'Referer: https://videovard.sx');
   $l="https://videovard.sx/api/make/hash/".$id;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   $x=json_decode($h,1);
   if (isset($x['hash'])) {
   $hash=$x['hash'];
   $l="https://videovard.sx/api/player/setup";
   $post="cmd=get_stream&file_code=".$id."&hash=".$hash;

   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Length: '.strlen($post),
   'Origin: https://videovard.sx',
   'Alt-Used: videovard.sx',
   'Connection: keep-alive',
   'Referer: https://videovard.sx');
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

  $y=json_decode($h,1);
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
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://videovard.sx")."&Origin=".urlencode("https://videovard.sx");
  // https://videovard.sx/js/tear.js
  // https://videovard.sx/_nuxt/daa1d94.js
  // return r={0:"5",1:"6",2:"7",5:"0",6:"1",7:"2"},t.next=3,e.seed.replace(/[012567]/g
  $head=array('Origin: https://videovard.sx');
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
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  */
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
  if (preg_match("/\(\'\w+\'\)\.innerHTML\s*\=\s*\(?[\'|\"]([^\'|\"]+)\)?[\'|\"]\s*\+\s*\(?[\'|\"]([^\'|\"]+)[\'|\"]\)?\.substring\((\d+)\)/i",$h,$m)) {
   $link=$m[1].substr($m[2],$m[3])."&stream=1";
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
} elseif (strpos($filelink,"jawcloud.") !== false) {
//echo $filelink;
  // https://jawcloud.co/embed-7ezp8ikxy7f8.html?cap&c1_file=https://upvtt.com/uploads/Blindspot-05x01-ICametoSleigh.POKE.English.C.orig.Addic7ed.com.vtt&c1_label=English
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_REFERER,"https://jawcloud.co");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
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
  $link=$link."|Referer=".urlencode("https://jawcloud.co");
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
} elseif (preg_match("/dood(stream)?\./",$filelink)) {
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
  $ua="Mozilla/5.0";
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
  if (preg_match("/http/",$h1))
   $link=$h1."?token=".$tok."&expiry=".(time()*1000);
  else
   $link="";
  } else {
   $link="";
  }
  //$link="https://mir44lo.dood.video/hls/u5kj7c2tf3hlsdgge7ygeoshiv7zu7b2nlcy7ig6sfirx4dzevc2ltmeie5q/master.m3u8";
   if ($flash <> "flash" && $link) $link =$link."|Referer=".urlencode("https://".$host);
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
    if (strpos($srt,"http") === false && $srt)
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
} elseif (strpos($filelink,"mcloud") !== false) {
//echo $filelink;
$filelink=str_replace("embed/","info/",$filelink);
//https://mcloud2.to/embed/60r98p?key=865b046ec31955bfd6f85993fafc2e7d
//$filelink="https://mcloud2.to/embed/0m6lw5?key=648c8297acd9b0a55006ee6e80a3fb84c648bbcff1c1bbe63d55fcfd8f2247c3";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $cookie=$base_cookie."ffmovies.dat";
  //https://mcloud.to/embed/k3720r?key=bdc4df372aebeeb6355277be86e94db9&sub.file=//sub1.iseek.to/sub.vtt?hash=caHR0cHM6Ly93d3c3LnB1dGxvY2tlcnR2LnRvL3N1YnRpdGxlLzQ1MDE2LnZ0dA==&autostart=true
  $t1=explode("&sub.file=",$filelink);
  $t2=explode("&",$t1[1]);
  $srt=urldecode($t2[0]);
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;

  $filelink=$t1[0];
  if (preg_match("/sub\.info\=(.+)/",$filelink,$s)) {
   $srt_json=trim($s[1]);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt_json);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h1 = curl_exec($ch);
   curl_close($ch);
   $s1=json_decode($h1,1);
   $srt_arr=array();
   for ($k=0;$k<count($s1);$k++) {
    if (preg_match("/(English|Romanian)\s*(\(verified\))?/",$s1[$k]['label'],$m)) $srt_arr[$m[0]]=$s1[$k]['file'];
   }
   if (isset($srt_arr['Romanian (verified)']))
    $srt=$srt_arr['Romanian (verified)'];
   elseif (isset($srt_arr['Romanian']))
    $srt=$srt_arr['Romanian'];
   elseif (isset($srt_arr['English (verified)']))
    $srt=$srt_arr['English (verified)'];
   elseif (isset($srt_arr['English']))
    $srt=$srt_arr['English'];
  }
  //echo $filelink;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://ffmovies.to/film/lois-clark-the-new-adventures-of-superman-3.m21x8/6l8n350',
'Upgrade-Insecure-Requests: 1');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://ffmovies.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $z=json_decode($h,1);
  //print_r ($z);
  //echo $h;
  //include ("obfJS.php");
  //$enc=$h;
  //echo obfJS();
  /*
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
   $link=$m[1];
  else
   $link="";
   $t1=explode('mediaSources = [{"file":"',$h);
   $t2=explode('"',$t1[1]);
   $link=$t2[0];
  */
  if (isset($z['media']['sources'][0]['file']))
    $link=$z['media']['sources'][0]['file'];
  else
    $link="";
  if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode($filelink)."&Origin=".urlencode('https://mcloud.to');
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
} elseif (preg_match("/vidnext\.net|vidnode\.net|vidembed\.net/",$filelink) || preg_match("/\/vidcloud\d+/",$filelink)) {
  //$filelink=str_replace("streaming.php","load.php",$filelink);
  // m1x.vidcloud9.com
  // vidnext.net
  // vidnode.net
  //echo $filelink;
  $t1=explode("@@",$filelink);
  $filelink=$t1[0];
  $t1=explode("?",$filelink);
  $host=parse_url($filelink)['host'];
  //https://vidcloud9.com/ajax.php
  $l="https://vidcloud9.com/ajax.php?".$t1[1];
  $l="https://vidcloud9.com/encrypt-ajax.php?".$t1[1];
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://vidcloud9.com/',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $r=json_decode($h2,1);
  //print_r ($r);
  if (isset($r['source'][0]['file']))
   $link= $r['source'][0]['file'];

if (isset($r['track']['tracks']['file']))
   $srt=$r['track']['tracks']['file'];
elseif (isset($r['track']['tracks'][0]['file']))
   $srt=$r['track']['tracks'][0]['file'];
*/
$l="https://vidembed.net/loadserver.php?".$t1[1];
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://vidcloud9.com");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/file\:\s*\'([^\']+)\'/",$h2,$r))
   $link=$r[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $s))
   $srt=$s[1];
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
  $link=$hash_path."/lava.m3u8";
  }
}
}
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
  //curl_close($ch);
  //echo $h3;
  if (preg_match("/window\.location/",$h3)) {
  $t1=explode('window.location = "',$h3);
  $t2=explode('"',$t1[1]);
  $l="https://mixdrop.to".$t2[0];
  curl_setopt($ch, CURLOPT_URL, $l);
  $h3 = curl_exec($ch);
  }
  curl_close($ch);
  //echo $h3;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match("/(\/\/[\w|\.\:\?\&\/\=\_\-]+\.mp4\?[\w|\.\:\?\&\/\=\_\-]+)[\'\"]/",$out,$m)) {
      $link="https:".$m[1];
      if (preg_match("/\.(remote)?sub\s*\=\s*\"(.*?)\"/",$out,$s)) {
      //print_r ($s);
       $srt= urldecode($s[2]);
       $srt=str_replace(" ","%20",$srt);
       if (strpos($srt,"http") === false && $srt) $srt="https:".$srt;
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

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

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
//echo $html;

  // https://soap2day.to/home/index/GetMInfoAjax
  if ($tip=="movie")
   $l="https://".$host."/home/index/GetMInfoAjax";
  else
   $l="https://".$host."/home/index/GetEInfoAjax";
//$post="pass=".$id."&param=".$param."&extra=::1";
//echo $post;
$post="pass=".$pass."&param=".$param."&extra=::1&e2=".$e2;
// pass=aTo2MzIzOw&param=https://q14.wewon.to&extra=95.76.3.211
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Origin: https://'.$host.'',
'Connection: keep-alive',
'Referer: https://'.$host.'/');
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $l);
 curl_setopt($ch, CURLOPT_USERAGENT, $ua);
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
  //echo $srt;
} elseif (strpos($filelink,"lookmovie.ag") !== false) {
  $t1=explode("link=",$filelink);
  $t2=explode("&sub=",$t1[1]);
  $link=$t2[0];
  $srt=$t2[1];
  if ($srt && strpos($srt,"http") === false) $srt="https:".$srt;
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
} elseif (preg_match("/embedsito\.com|vidsrc\.xyz|feurl\.|fcdn\.stream|fembed\.|femax\d+\.com|gcloud\.live|bazavox\.com|xstreamcdn\.com|smartshare\.tv|streamhoe\.online|animeawake\.net|mediashore\.org|sexhd\.co|streamm4u\.club/",$filelink)) {
  $host=parse_url($filelink)['host'];
  // https://fcdn.stream/v/y2zjdhepr--e6z3
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
} elseif (strpos($filelink,"facebook") !== false) {
function decode_code1($code){
    return preg_replace_callback(
        "@\\\\(u)([0-9a-f]{4})@",
        function($m){
            return mb_convert_encoding(chr($m[1]?hexdec($m[2]):octdec($m[2])),'UTF-8');
        },
        $code
    );
}
$pattern = '/(video_id=|videos\/)([0-9a-zA-Z]+)/';
preg_match($pattern,$filelink,$m);
$filelink="https://www.facebook.com/video/embed?video_id=".$m[2];
//echo $filelink;
// https://www.facebook.com/134093565449/videos/342521610130689/
// https://www.facebook.com/watch/live/?v=342521610130689&ref=watch_permalink
$filelink="https://www.facebook.com/watch/live/?v=".$m[2]."&ref=watch_permalink";
//$filelink="https://www.facebook.com/watch/live/?v=1254569611674582&ref=watch_permalink";
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
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
      // preg_match_all ("/'(\{\"[^\}]+\})'/", $html, $matches);
      //preg_match_all ("/(\{\".+\})\"/mi", $h1, $m);
      //print_r ($m[1][2]);
      //$x=json_decode($m[1][2].";",1);
      //print_r ($x);
      //echo $h1;
      //$h1= decode_code1($h1);
//https://video-otp1-1.xx.fbcdn.net/hvideo-atn2-prn/v/r6i7GhhicDIncA34OyVmH/live-dash/live-md-a/2211900909099438_0-init.m4a?ms=1
      //echo $h1;
      //$h1=str_between($h1,'videoData":[',',false,0.9]]]});}');
      //$r=json_decode($h1,1);
      //print_r ($r);
      //echo $h1;
      // hd_src:null,sd_src:"https://scontent-
      //$h1=urldecode(str_replace("\\","",$h1));
      //echo $h1;
      //preg_match('/(?:hd_src|sd_src):\"([\w\-\.\_\/\&\=\:\?]+)/',$h1,$m);
      //preg_match('/contentUrl\":\"
      //print_r ($m);
      //preg_match("/contentUrl\":\"([\w\-\.\_\/\&\=\:\?]+)/",$h1,$m);
      //print_r ($m);
      //$link=$m[1];
      //$t1=explode('sd_src:"',$h1);
      //$t2=explode('"',$t1[1]);
      //$link=$t2[0];
      if (preg_match("/(?:hd_src|sd_src)\:\"([^\"]+)\"/",$h1,$m))
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

} elseif (strpos($filelink,"vidcloud.") !== false) {
  // https://vidcloud.pro/embed4/47bkl9d1f7xz1?i=2c6b544306d5c1b81e0b7b86a000da4cb5572df056ec3727324f7db84611806ecdf5a2e3429a1483ca59e880d8e299ab
  // https://vidcloud.pro/embed/5e1b6063ccb14
  // https://vidcloud.msk.ru/embed4/54enm296il6tu?i=2c6b544306d5c1b81e0b7b86a000da4c2d52850a6e79371835929ac55d1155b6c045926b500d70e69163d8e81cf9c0c9&el=4236402
  // https://vidcloud.msk.ru/embed-4/yvUci3z9lqCM?z=
  //echo $filelink;
  //die();
  $host="https://".parse_url($filelink)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://www2.zoechip.com/");
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
  require_once ("rec.php");
  $key="6LdS_j8bAAAAAFgdltJiyC6RIiqCCG1daI_VYdw3";
  $co="aHR0cHM6Ly92aWRjbG91ZC5tc2sucnU6NDQz";
  //echo base64_decode($co);
  $sa="embed_4_get_sources";
  $loc="https://vidcloud.msk.ru";
  $token=rec($key,$co,$sa,$loc);
  $l= "https://vidcloud.msk.ru/ajax/embed-4/getSources?id=".$id."&_token=".$token;
  //echo $l;
  //die();
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Alt-Used: vidcloud.msk.ru',
  'Connection: keep-alive',
  'Referer: https://vidcloud.msk.ru/');
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
  if (isset($x["sources_1"][0]["file"]))
   $link= $x["sources_1"][0]["file"];
   
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
} elseif (strpos($filelink,"hqq.") !== false || strpos($filelink,"hqq.watch") !== false || strpos($filelink,"waaw.tv") !== false || strpos($filelink,"waaw1.tv") !== false  || strpos($filelink,"hindipix.in") !== false  || strpos($filelink,"pajalusta.") !== false) {
//echo $filelink;
function rec() {
  $ua="Mozilla/5.0";

  $site_key="6Ldf5F0UAAAAALErn6bLEcv7JldhivPzb93Oy5t9";
  $co="aHR0cHM6Ly9kb29kLnRvOjQ0Mw..";
  $co=base64_encode("https://hqq.tv:443");
  $co="aHR0cHM6Ly9ocXEudHY6NDQz";
  $sa="watch_video";
  $loc="https://hqq.tv";

  $head = array(
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Referer: https://hqq.tv'
  );
  $l="https://www.google.com/recaptcha/api.js";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_REFERER, $loc);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $h = curl_exec($ch);


  $t1=explode("releases/",$h);
  $t2=explode("/",$t1[1]);
  $v=$t2[0];

  $cb="123456789";

  $l2="https://www.google.com/recaptcha/api2/anchor?ar=1&k=".$site_key."&co=".$co."&hl=ro&v=".$v."&size=invisible&cb=".$cb;

  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_REFERER, $loc);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
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
  'Referer: '.$l2.'',
  'Connection: keep-alive');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l6);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);


  $t1=explode('rresp","',$h);
  $t2=explode('"',$t1[1]);
  $token=$t2[0];
  return $token;
}
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
$filelink=str_replace("/f/","/e/",$filelink);
$filelink=str_replace("/e/","/watch_video.php?v=",$filelink);
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
$ua="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10', #'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0";
$pattern = "@(?:\/\/|\.)((?:waaw1?|netu|hqq|hindipix)\.(?:tv|watch|in))\/(?:watch_video\.php\?v|.+?vid)=([a-zA-Z0-9]+)@";
//echo $filelink;
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
//$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
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
      curl_setopt($ch, CURLOPT_REFERER, "http://filmeserialeonline.org/");
      //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h1 = curl_exec($ch);
      curl_close($ch);
      $h1=urldecode($h1);
      //echo urldecode("%3c");
      //echo $h1;
      //vid':'
     preg_match("/vid\s*\'\:\s*\'(?P<vid>[^\']+)\'/",$h1,$m);
     $vid=$m["vid"];
  }
  //$vid="19hfRmn5ZcxP";

$l="https://hqq.tv/player/embed_player.php?vid=".$vid."&autoplay=no";
//$l="http://hqq.watch/e/".$vid;
$cookie=$base_cookie."hqq.txt";



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "http://filmeserialeonline.org");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_TIMEOUT, 25);
$h = curl_exec($ch);
curl_close($ch);
$h=urldecode($h);
//echo $h;
$y="";
$bsol_gt=false;
if (preg_match("/userid\s*\=\s*\"(\w+)\"/",$h,$n)) {
 $adb=$n[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m)) {
  $srt=$m[1];
  $srt=urldecode(str_replace("https","http",$srt));
  }
  if (preg_match("/shh\=\'(\w{40})\'/",$h,$s)) {
   $sh=$s[1];
  } elseif (preg_match("/\w+\(\'(\w{40})\'\)/",$t1[1],$s)) {
  $t1=explode('if(obs){',$h);
  preg_match("/\w+\(\'(\w{40})\'\)/",$t1[1],$s);
  $sh=$s[1];
  }


//include ("shh.php");
//$sh=get_sh($h);
$gt="";
$token="";
//echo "<BR>".$sh;
//die();
if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
$h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
if (preg_match("/hqq\.tv	\w+	\/	\w+	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
  $t= $m[1]-time();
  if ($t>0) {
    file_put_contents($base_cookie."max_time_hqq.txt",$m[1]);
    file_put_contents($base_cookie."hqq.txt",$m[2]);
  }
}
unlink ("/storage/emulated/0/Download/cookies.txt");
}
if (file_exists($base_cookie."cookies.txt")) {
$h1=file_get_contents($base_cookie."cookies.txt");
if (preg_match("/hqq\.tv	\w+	\/	\w+	(\d+)	gt	([a-zA-Z0-9]+)/",$h1,$m)) {
  $t= $m[1]-time();
  if ($t>0) {
    file_put_contents($base_cookie."max_time_hqq.txt",$m[1]);
    file_put_contents($base_cookie."hqq.txt",$m[2]);
  }
}
unlink ($base_cookie."cookies.txt");
}
if (file_exists($base_cookie."max_time_hqq.txt")) {
$time_now=time();
$time_exp=file_get_contents($base_cookie."max_time_hqq.txt");
   if ($time_exp > $time_now) {
     $minutes = intval(($time_exp-$time_now)/60);
     $seconds= ($time_exp-$time_now) - $minutes*60;
     if ($seconds < 10) $seconds = "0".$seconds;
     $msg_captcha=" | Expira in ".$minutes.":".$seconds." min.";
     $gt=file_get_contents($base_cookie."hqq.txt");
   } else {
     $msg_captcha="";
   }
} else {
   $msg_captcha="";
}

$bsol_gt=false;
if (file_exists($base_cookie."max_time_hqq.txt")) {
$time_now=time();
$time_exp=file_get_contents($base_cookie."max_time_hqq.txt");
if ($time_exp > $time_now) {
$p=array(
  'sh' => $sh,
  'ver' => '4',
  'secure' => '0',
  'adb' => $adb,
  'v' => $vid,
  'token' => $token,
  'gt' => $gt,
  'embed_from' => '0',
  'wasmcheck' => '2'
);
$adscorestored="";
$p=array(
  'sh' => $sh,
  'ver' => '4',
  'secure' => '0',
  'adb' => $adb,
  'v' => $vid,
  'token' => $token,
  'gt' => $gt,
  'embed_from' => '0',
  'wasmcheck' => '1',
  'adscore' => $adscorestored,
  'clickx' => '300',
  'clicky' => '100',
  'click_hash' => ''
);
//print_r ($p);
$l="https://hqq.tv/player/get_md5.php?".http_build_query($p);
$l="https://hqq.tv/player/get_md5.php";
$post=json_encode($p);
//echo $l;
$head1=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://hqq.tv',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
);
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/json',
'Content-Length: '.strlen($post),
'X-Requested-With: XMLHttpRequest',
'Origin: https://hqq.tv',
'Referer: https://hqq.tv',
'Connection: keep-alive');
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
$h = curl_exec($ch);
curl_close($ch);
    //echo $h;
    $file=str_between($h,'obf_link":"','"');
    $y=decodeUN($file);
    //echo $y;
    if (strpos($y,"http") === false && $y) $y="https:".$y;
    if ($y)
      $link=$y.".mp4.m3u8";
    else
     $link="";
$bsol_gt=true;
}
}
if ($bsol_gt==false) {
$netu=$base_pass."netu.txt";
if (file_exists($netu)) {
$l=trim(file_get_contents($netu)).$vid;
//echo $l;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
curl_setopt($ch, CURLOPT_TIMEOUT, 25);
$h = curl_exec($ch);
curl_close($ch);
$t1=explode('file": "',$h);
$t2=explode('"',$t1[1]);
$link=$t2[0];
  if (preg_match("/url\=/",$link)) {
  $t1=explode("url=",$link);
  $link=$t1[1];
  }
}
}
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
    $link=$m[1][count($m[1])-1];
  else
    $link="";
} elseif (strpos($filelink, 'youtu') !== false){
   //https://www.youtube-nocookie.com/embed/kfQTqjvaezM?rel=0
   //echo $filelink;
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
//echo $filelink;
  // https://www.dailymotion.com/video/x2jtx5v
  // https://www.dailymotion.com/embed/video/x2l65up?autoplay=1
  preg_match ("/video\/([a-zA-Z0-9]+)/",$filelink,$m);
  $id=$m[1];
  $filelink="https://www.dailymotion.com/embed/video/".$id;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";

  $h=file_get_contents($filelink);
  //echo $h;
  $t1=explode('var config = {',$h);
  $t2=explode('window.playerV5',$t1[1]);
  $t1=explode('window.__PLAYER_CONFIG__ = {',$h);
  $t2=explode(';</script',$t1[1]);
  $h1=trim("{".$t2[0]);
  //$h1=substr($h1, 0, -1);
  $r=json_decode($h1,1)['metadata']['qualities'];
  if (isset($r['auto'][0]['url'])) {
  $l_main=$r['auto'][0]['url'];
  $h2=file_get_contents($l_main);
  if (preg_match_all("/PROGRESSIVE-URI\=\"(.*?)\"/",$h2,$q)) {
   $link=$q[1][count($q[1])-1];
   $t1=explode("#",$link);
   $link=$t1[0];
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
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(v\.mp4|master\.m3u8))/', $h, $m)) {
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
   //$srt_name=$pg.".srt";
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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
$hw .="|hxload.|jetload\.net|azm\.to|movie4k\.ag|hlsplay\.com|videobin\.|moonline\.|dood(stream)?\.|dailymotion\.com|flowyourvideo\.com|streamtape\.|okstream\.|easyload\.io|youdbox\.com";
$hw .="|ronemo\.com|abcvideo\.|hdm\.|evoload\.|m4ufree\.yt|anilist1\.ir|animdl\.cf|noxx\.is|filmele-online\.com|playdrive\.xyz|ezylink\.co|gomoplayer\.";
$hw .="|apimdb\_vip\.net|wootly\.ch|playdrive\.plyr\.xyz|msmoviesbd\.com|c1ne\.co/";
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
  //if (substr($t1[0], -4) == "m3u8")
  if (preg_match("/\.m3u8/",$t1[0]))
   $c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.filename=".urlencode($pg).";S.title=".urlencode($pg).";end";

  //$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";
  echo $c;
  die();
} else {
$type = "mp4";
if (strpos($movie,"m3u8") !== false) $type="m3u8";
/*
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
player.setup({
';
*/
//http://my1.imgsmail.ru/r/video2/uvpv3.swf?58
/*
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
*/
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


 </script>
</body>
</html>
';
}
?>
