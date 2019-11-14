<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function kt($d,$orig) {
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
 return str_replace($j, $h, $orig);
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
  $l = urldecode($_POST["link"]);
  $l=str_replace(" ","%20",$l);
  $title = unfix_t(urldecode($_POST["title"]));
} else {
  $l = urldecode($_GET["link"]);
  $l=str_replace(" ","%20",$l);
  $title = unfix_t(urldecode($_GET["title"]));
}
$l=trim($l);
//echo $l;
$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
$host=parse_url($l)["host"];
$out="";
$type="mp4";
$cookie=$base_cookie."adultc.dat";
if (preg_match("/jizzbunker\.com|familyporn\.tv|zbporn\.com/",$host)) {
  $h=@file_get_contents($l);
} else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
}
//echo $h;
if (strpos($l,"porndbs.com") !== false) {
//echo $h;
  $l=str_between($h,'iframe src="','"');
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $host=parse_url($l)["host"];
  //echo $host;
}
if (!$h) $host="";
/* serveres */
if (preg_match("/4tube\.com/",$host)) {
  $t0=explode('button id="download',$h);
  $t1=explode('data-id="',$t0[1]);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $t1=explode('data-quality="',$h);
  $t2=explode('"',$t1[count($t1)-1]);
  $q=$t2[0];

  if ($id) {
    $filelink="https://tkn.4tube.com/".$id."/desktop/1080+720+480+360+240";
    $filelink="https://tkn.kodicdn.com/".$id."/desktop/1080+720+480+360+240";
    $filelink="https://token.4tube.com/0000000".$id."/desktop/1080+720+480+360+240";
    $head=array('Accept: application/json, text/javascript, */*; q=0.01','Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2','Origin: http://www.4tube.com');

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
      if ($h) {
         $r=json_decode($h,1);
         $out=$r[$q]["token"];
      } else $out="";
 }
} else if (preg_match("/anybunny\.com/",$host)) {
  $id=str_between($h,"embed/",'"');
  $l="https://vartuc.com/embed/".$id;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://vartuc.com/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

  $t1=explode("player.php",$h);
  $t2=explode("'",$t1[1]);
  $ll="https://vartuc.com//kt_player/player.php".$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $ll);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER,"https://vartuc.com/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  //echo $h1;
  preg_match("/video_url:([^}]+)/",$h1,$m);
  preg_match_all('/(gh\w\w\w)="([^"]+)/',$h1,$m1);

  for ($i=0 ; $i<count($m1[0]);$i++) {
   $link=str_replace($m1[1],$m1[2],$m[0]);
  }

  $link=str_replace('"',"",$link);
  $link=str_replace("+","",$link);

  $t1=explode("video_html5_url:",$link);
  $link1=trim($t1[1]);
  if (!$link1) {
   $t2=explode("video_url:",$link);
   $t3=explode(",video_html5",$t2[1]);
   $link1=$t3[0];
  }
  $t1=explode("video_url:",$link);
  $t2=explode(",video_html5_url",$t1[1]);
  $link11=$t2[0];
  if (strpos($link1,"xhcdn") !== false) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_REFERER, "http://xhamster.com");
      curl_setopt($ch, CURLOPT_NOBODY,true);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("\n",$t1[1]);
      $link2=trim($t2[0]);
      if (!$link2)
        $out=$link1;
      else
       $out=$link2;
  } else {
    $out=$link1;
  }
} else if (preg_match("/ashemaletube\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/befuck\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/bitporno\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/bravoporn\.com/",$host)) {
  if (preg_match_all("/source src\=\"(.*?)\"/",$h,$m)) {
    $out=$m[1][count($m[1])-1];
  }
} else if (preg_match("/dansmovies\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  if (!$out) {
   $t1=explode("url: '",$h);
   $t2=explode("'",$t1[1]);
   $out=$t2[0];
  }
} else if (preg_match("/deviantclip\.com/",$host)) {
  $t1=explode("source src='",$h);
  $t2=explode("'",$t1[1]);
  $out=$t2[0];
} else if (preg_match("/drtuber\.com|proporn\.com/",$host)) {
  $r=json_decode($h,1);
  if (isset($r["files"])) {
  if (isset($r["files"]["hq"]))
    $out= $r["files"]["hq"];
  else if (isset($r["files"]["lq"]))
    $out=$r["files"]["lq"];
  } else {
    $out="";
  }
} else if (preg_match("/eporner\.com/",$host)) {
  function encode_base_n($num,$n) {
    $table='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $table = substr($table,0,$n);
    $ret="";
    while ($num) {
      $ret = $table[$num - $n*floor($num/$n)] . $ret;  // fix $num % $n
      $num = floor($num/$n);
    }
    return $ret;
  }
  function calc_hash($s) {
    $ret="";
    for ($k=0;$k<32;$k +=8) {
     $ret .=encode_base_n(hexdec(substr($s,$k,8)),36);
    }
    return $ret;
  }
  $t1=explode("vid: '",$h);
  $t2=explode("'",$t1[1]);
  $vid=$t2[0];
  $t1=explode("hash: '",$h);
  $t2=explode("'",$t1[1]);
  $hash=$t2[0];
  $hash=calc_hash($hash);

  $l ="https://www.eporner.com/xhr/video/".$vid."?hash=".$hash."&device=generic&domain=www.eporner.com&fallback=false&embed=false&supportedFormats=mp4&tech=Html5&_=";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:66.0) Gecko/20100101 Firefox/66.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.eporner.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  foreach ($r['sources']['mp4'] as $key => $value) {
    $out = $r['sources']['mp4'][$key]['src'];
    if ($out) break;
  }
} else if (preg_match("/eroxia\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/extremetube\.com/",$host)) {
  $t1=explode('window.flashvars =',$h);
  $t2=explode(';',$t1[1]);
  $h=trim($t2[0]);
  $r=json_decode($h,1);

  $q=array("1080p","720p","480p","360p","240p","180p");
  foreach($q as $v) {
   $qv="quality_".$v;
   if (isset($r[$qv]) && $r[$qv]) {
      $out=$r[$qv];
      break;
   }
  }
} else if (preg_match("/familyporn\.tv/",$host)) {
  //echo $h;
  if (preg_match("/license_code:\s+\'(.*?)\'/ms",$h,$m)) {
   if (preg_match_all("/(video_url|video_alt_url|video_alt_url2|video_alt_url3)\:\s+\'function\/0\/(.*?)\/\'/ms",$h,$u)) {
    $movie=$u[2][count($u[2])-1];
    $lic=$m[1];
    $out=kt($lic,$movie);
   } else if (preg_match_all("/(video_url|video_alt_url|video_alt_url2|video_alt_url3)\:\s+\'(.*?)\'/ms",$h,$u)) {
     $out=$u[2][count($u[2])-1];
   }
   //$out=$movie;
   //echo "\n\r".$out."\r\n";
   //$r = get_headers($out);
   //print_r ($r);
   //die();
   //$t1=explode('Location:',$r[6]);
   //$out=trim($t1[1]);
   //$r = get_headers($out);
   //print_r ($r);
  }
} else if (preg_match("/fapbox\.com/",$host)) {
  $out=str_between($h,'file:"','"');
} else if (preg_match("/handjobhub\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/hdmovz\.com|porntrex\.com/",$host)) {
  if (preg_match("/license_code:\s+\'(.*?)\'/ms",$h,$m)) {
   preg_match_all("/(video_url|video_alt_url|video_alt_url2|video_alt_url3)\:\s+\'(.*?)\/\'/ms",$h,$u);
   $movie=$u[2][count($u[2])-1];
   $lic=$m[1];
   //$out=kt($lic,$movie);
   $out=$movie;
 }
} else if (preg_match("/hellmoms\.com/",$host)) {
  if (preg_match_all("/src\=\"([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=]*)\"\s+title\=\"(\d+)p\"/",$h,$m)) {
     $maxs = array_keys($m[2], max($m[2]));
     $out=$m[1][$maxs[0]];
  }
} else if (preg_match("/jizzbunker\.com/",$host)) {
  $out = urldecode(str_between($h, "src:'", "'"));
  $out=str_replace("https","http",$out);
} else if (preg_match("/lubetube\.com/",$host)) {
  $t1=explode('id="video-',$h);
  $t2=explode('href="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $out=$t3[0];
} else if (preg_match("/mangovideo\.pw/",$host)) {
  if (strpos($h,"license_code:") !== false) {
   $t1 = explode("license_code: '", $h);
   $t2 = explode("'", $t1[1]);
   $d = $t2[0];
   $t1 = explode("function/0/", $h);
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

 $out = str_replace($j, $h, $orig);
 } else {
 $out="";
 }
} else if (preg_match("/milfzr\.com/",$host)) {
   $h = str_replace("\/","/",$h);
   if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m))
    $out=$m[1];
} else if (preg_match("/mofosex\.com/",$host)) {
  $t1=explode('quality_720p":"',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  if (!$out) {
     $t1=explode('quality_4800p":"',$h);
     $t2=explode('"',$t1[1]);
     $out=$t2[0];
  }
  if (!$out) {
     $t1=explode('quality_360p":"',$h);
     $t2=explode('"',$t1[1]);
     $out=$t2[0];
  }
  if (!$out) {
     $t1=explode('quality_240p":"',$h);
     $t2=explode('"',$t1[1]);
     $out=$t2[0];
  }
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/nuvid\.com/",$host)) {
  $r=json_decode($h,1);
  if (isset($r["files"])) {
  if (isset($r["files"]["hq"]))
    $out= $r["files"]["hq"];
  else if (isset($r["files"]["lq"]))
    $out=$r["files"]["lq"];
  } else {
    $out="";
  }
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/(\/|\.)porn\.com/",$host)) {
  if (preg_match_all("/(1080|720|480|360|240)p\"\,url\:\"(.*?)\"/ms",$h,$m)) {
     $maxs = array_keys($m[1], max($m[1]));
     $out=$m[2][$maxs[0]];
  }
} else if (preg_match("/porn300\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornburst\.xxx|pornjam\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
} else if (preg_match("/porndoe\.com/",$host)) {
  $t1=explode('video id="',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $out=$t3[0];
} else if (preg_match("/porndroids\.com/",$host)) {
  $t1=explode('data-video="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornhd\.com/",$host)) {
  if (preg_match_all("/(1080|720|480|360|240)p\"\:\"(.*?)\"/ms",$h,$m)) {
     $maxs = array_keys($m[1], max($m[1]));
     $out=$m[2][$maxs[0]];
     $out=str_replace("\\","",$out);
     if (strpos($out,"http") === false) $out="https://www.pornhd.com".$out;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $out);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_REFERER, "https://www.pornhd.com");
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_NOBODY,true);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("\n",$t1[1]);
      $out=trim($t2[0]);
 }
} else if (preg_match("/pornheed\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornhost\.com/",$host)) {
  $t1=explode('file: "',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornhub\.com/",$host)) {
  //https://www.pornhub.com/embed/ph5d4b0d9dbca84
  preg_match_all("/flashvars\.mediaDefinitions\.(.*?)\.videoUrl/msi",$h,$q);
  $s=$q[1][0];
  preg_match_all("/var ra[a-zA-Z0-9]+.*?\;/msi",$h,$m);
  $find="/var ".$s."=.*?\;/";
  preg_match($find,$h,$n);
  $x=preg_replace("/\/\*.*?\*\//","",$n[0]);
  $x=str_replace("var ".$s."=ra","\$out=\$ra",$x);
  $o="";
  for ($k=0;$k<count($m[0]);$k++) {
   $o .=str_replace("+",".",str_replace("var ra","\$ra",$m[0][$k]))."\n";
  }
  $o .=str_replace("+",".",str_replace(" ra","\$ra",$x))."\n";
  eval ($o);
} else if (preg_match("/pornmaki\.com/",$host)) {
  $t1=explode('file:"',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornrabbit\.com/",$host)) {
  $t1=explode("file: '",$h);
  $t2=explode("'",$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/pornrox\.com/",$host)) {
  if (preg_match_all("/source src\=\"(.*?)\"\s+type\=\'video\/mp4\' label\=\'(1080|720|480|360|240)p\'/ms",$h,$m)) {
     $maxs = array_keys($m[2], max($m[2]));
     $out=$m[1][$maxs[0]];
     $out=trim(str_replace("\\","",$out));
     //echo $out;
     if (strpos($out,"http") === false) $out="https://www.pornrox.com".$out;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $out);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_REFERER, "https://www.pornrox.com");
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_NOBODY,true);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("\n",$t1[1]);
      $out=trim($t2[0]);
      if (strpos($out,"http") === false && $out) $out="https:".trim($t2[0]);
 }
} else if (preg_match("/redtube\.com/",$host)) {
  if (preg_match_all("/videoUrl\"\:\"(.*?)\"/",$h,$m)) {
  foreach ($m[1] as $key=>$value) {
    $out=$value;
    if ($out) break;
  }
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="http:".$out;
  $out=str_replace("https","http",$out);
  }
} else if (preg_match("/slutload\.com/",$host)) {
  if (preg_match("/license_code:\s+\'(.*?)\'/ms",$h,$m)) {
   preg_match_all("/(video_url|video_alt_url|video_alt_url2|video_alt_url3)\:\s+\'function\/0\/(.*?)\/\'/ms",$h,$u);
   $movie=$u[2][count($u[2])-1];
   $lic=$m[1];
   $out=kt($lic,$movie);
 }
} else if (preg_match("/spankbang\.com/",$host)) {
  $t1=explode('data-streamkey="',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $c=file_get_contents($cookie);
  $t1=explode('sb_csrf_session',$c);
  $t2=explode('#',$t1[1]);
  $csrf=trim($t2[0]);

  $l="https://pl.spankbang.com/api/videos/stream";
  $post="id=".$id."&data=0&sb_csrf_session=".$csrf;

  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Referer: https://pl.spankbang.com/30gee/video/thickness',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-CSRFToken: '.$csrf.'',
  'X-Requested-With: XMLHttpRequest');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch,CURLOPT_ENCODING, '');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $x = curl_exec($ch);
  curl_close($ch);

  $r=json_decode($x,1);
  //print_r ($r);
  if (isset($r["stream_url_1080p"]) && $r["stream_url_1080p"] !="")
    $out=$r["stream_url_1080p"][0];
  elseif (isset($r["stream_url_720p"]) && $r["stream_url_720p"] !="")
    $out=$r["stream_url_720p"][0];
  elseif (isset($r["stream_url_480p"]) && $r["stream_url_480p"] !="")
    $out=$r["stream_url_480p"][0];
  elseif (isset($r["stream_url_360p"]) && $r["stream_url_360p"] !="")
    $out=$r["stream_url_360p"][0];
  elseif (isset($r["stream_url_240p"]) && $r["stream_url_240p"] !="")
    $out=$r["stream_url_240p"][0];
  else
    $out="";
} else if (preg_match("/thumbzilla\.com/",$host)) {
  if (preg_match_all("/data-quality\=\"(.*?)\"/ms",$h,$m)) {
    $out=$m[1][count($m[1])-1];
    $link=str_replace("&amp;","&",$out);
    $out=str_replace("https","http",$out);
  }
} else if (preg_match("/tnaflix\.com/",$host)) {
  $vid=str_between($h,'VID" type="hidden" value="','"');
  $key=str_between($h,'nkey" type="hidden" value="','"');
  $fid=str_between($h,'vkey" type="hidden" value="','"');
  $l1="https://cdn-fck.tnaflix.com/tnaflix/".$fid.".fid?key=".$key."&VID=".$vid."&rollover=1&startThumb=19&premium=1&country=&user=0&vip=0&cd=u&ref=embed&alpha";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.tnaflix.com/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  $t1=explode('<videoLink><![CDATA[',$h1);
  $t2=explode(']]>',$t1[count($t1)-1]);
  $out=$t2[0];
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/trannytube\.net|trannytube\.tv/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("&amp;","&",$out);
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
} else if (preg_match("/tube8\.com/",$host)) {
  $out=trim(str_between($h,'videoUrlJS = "','"'));
} else if (preg_match("/vporn\.com/",$host)) {
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $out=$t2[0];
  $out=str_replace("&amp;","&",$out);
  $out=str_replace("\\","",$out);
} else if (preg_match("/vpornvideos\.com/",$host)) {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m)) {
     $out=$m[1];
     $out=str_replace("\\","",$out);
     if (strpos($out,"http") === false && $out) $out="https:".$out;
  }
} else if (preg_match("/xhamster\.com/",$host)) {
  $h=str_replace("\\","",$h);
  if (preg_match_all("/(144|240|360|480|720|1080)p\"\:\"(.*?)\"/",$h,$m)) {
    $out=$m[2][0];
  }
} else if (preg_match("/xozilla\.com/",$host)) {
  $t1=explode("video_url: '",$h);
  $t2=explode("'",$t1[1]);
  $out=$t2[0];
} else if (preg_match("/xnxx\.com|xvideos\.com/",$host)) {
  if (preg_match_all("/html5player\.setVideoUrl(Low|High)\(\'(.*?)\'/ims",$h,$m)) {
     $out=$m[2][count($m[2])-1];
  }
} else if (preg_match("/youjizz\.com/",$host)) {
  $h=str_replace("\\","",$h);
  if (preg_match_all("/quality\"\:\"(1080|720|480|360|240)\"\,\"filename\"\:\"(.*?)\"/",$h,$m)) {
    $maxs = array_keys($m[1], max($m[1]));
    $out=$m[2][$maxs[0]];
    if (strpos($out,"http") === false && $out) $out="https:".$out;
  }
} else if (preg_match("/youporn\.com/",$host)) {
  $t1=explode('id="player-html5',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $out=str_replace("&amp;","&",$t3[0]);
} else if (preg_match("/zbporn\.com/",$host)) {
  $t1=explode("video_url: '",$h);
  $t2=explode("'",$t1[1]);
  $out=$t2[0];
  $out=str_replace("\\","",$out);
  if (strpos($out,"http") === false && $out) $out="https:".$out;
}
///////////////////////////////////////////////////////////////////
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
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": [{
"title": "'.preg_replace("/\n|\r/"," ",$title).'",
"sources": [{"file": "'.$out.'", "type": "'.$type.'"}]
}],
"height": $(document).height(),
"width": $(document).width(),
"skin": {
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"autostart": true,
"startparam": "start",
"fallback": false,
"wmode": "direct",
"title": "'.preg_replace("/\n|\r/"," ",$title).'",
"abouttext": "'.preg_replace("/\n|\r/"," ",$title).'",
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
