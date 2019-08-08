<?php
error_reporting(0);
//set_time_limit(0);
include ("../common.php");
$my_srt="";
$srt="";
$srt_name = "";
$movie="";
$movie_file="";
$pg="";
$referer="";
$link="";
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
if (strpos($filelink,"0123netflix.site") !== false) {
   $ua = $_SERVER['HTTP_USER_AGENT'];
   $head=array(
   'Accept: application/json, text/javascript, */*; q=0.01',
   'Accept-Language: en-US,en;q=0.5',
   'Accept-Encoding: deflate',
   'X-Requested-With: XMLHttpRequest'
   );
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER, "https://0123netflix.site");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   //print_r ($r);
   if (isset($r["target"])) {
   $l=$r["target"];
   $t1=explode("id=",$l);
   $t2=explode("&",$t1[1]);
   $id=$t2[0];
   $l="https://0123netflix.site/v/?id=".$id;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER, "https://0123netflix.site/");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   //print_r ($r);
   $l="https:".$r[0];
   $t1=explode("id=",$l);
   $id=$t1[1];
   if ($id) {
   $l="https://proxy.123downloads.today/proxy.php?id=".$id;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER, "https://0123netflix.site/");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   //print_r ($r);
   if (isset($r["src"])) {
   $l=$r["src"];
   $t1=explode("id=",$l);
   $id=$t1[1];
   $link="https://stream.123downloads.today/hls/".$id."/playlist.m3u8";
   }
   }
   } else {
    $link="";
   }
}
if (strpos($filelink,"gomovies.tube") !== false) {
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
   $h=file_get_contents($filelink);
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
   if ($r['type_server'] == "open_load")
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
if (strpos($filelink,"vidcloud.icu") !== false) {
  $filelink=str_replace("streaming.php","load.php",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $t1=explode('embedvideo" src="',$h2);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  if ($l) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $t1=explode("Location:",$h3);
  $t2=explode("\n",$t1[1]);
  $filelink=trim($t2[0]);
  }
}
if (strpos($filelink,"api.vidnode.net") !== false) {
  //https://api.vidnode.net/stream.php?type=openload&sid=8aAiJHQsojM&eid=269051
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
  $t1=explode('Location:',$h2);
  $t2=explode('Expect',$t1[1]); ///?????
  $filelink=trim($t2[0]);
  //echo $filelink;
}
  if (strpos($filelink,"tinyurl.com") !== false) {
  $l=$filelink;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $t1=explode("Location:",$h2);
  $t2=explode("\n",$t1[1]);
  $filelink=trim($t2[0]);
  //echo $cur_link;
  }
  if (strpos($filelink,"bit.ly") !== false) {
  $l=$filelink;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $t1=explode("Location:",$h2);
  $t2=explode("\n",$t1[1]);
  $filelink=trim($t2[0]);
  //echo $cur_link;
  }
  if (strpos($filelink,"goo.gl") !== false) {
  $l=$filelink;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $t1=explode("Location:",$h2);
  $t2=explode("\n",$t1[1]);
  $filelink=trim($t2[0]);
  //echo $cur_link;
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
if (strpos($filelink,"adf.ly") !==false) {
 $h1=file_get_contents($filelink);
 $temp=$filelink;
 $filelink=str_between($h1,"var eu = '","'");
 if (!$filelink)
   $filelink=str_between($h1,"var zzz = '","'");
 if (!$filelink) {
  $filelink=str_between($h1,"var url = '","'");

  if (strpos($filelink,"adf.ly") === false)
    $filelink = "http://adf.ly".$filelink;
 $a = @get_headers($filelink);
 //print_r ($a);
 $l=$a[9];
 $a1=explode("Location:",$l);
 $filelink=trim($a1[1]);
 if (!$filelink)
  $filelink="http".str_between($h1,"self.location = 'http","'");
 }
}
if (strpos($filelink,"moovie.cc") !== false) {
 $a = @get_headers($filelink);
 $l=$a[10];
 $a1=explode("Location:",$l);
$filelink=trim($a1[1]);
}
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
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
function youtube($file) {
//echo $file;
function _splice($a,$b) {
	return  array_slice($a,$b);
}

function _reverse($a,$b) {
	return  array_reverse($a);
}

function _slice($a,$b) {
	$tS = $a[0];
	$a[0] = $a[$b % count($a)];
	$a[$b] = $tS;
	return  $a;
}
$a_itags=array(37,22,18);
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {
  $id = $match[2];
  //print_r ($match);
  $l = "https://www.youtube.com/watch?v=".$id;
  $html="";
  $p=0;
  //echo $l;
  /*
  while($html == "" && $p<10) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:61.0) Gecko/20100101 Firefox/61.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4; Nexus 7 Build/KOT24) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.105 Safari/537.36');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $p++;
  }
  */
  //echo $html;
  $html=@file_get_contents($l);
  $html = str_between($html,'ytplayer.config = ',';ytplayer.load');
  $parts = json_decode($html,1);
  //echo $l;
  //preg_match('#config = {(?P<out>.*)};#im', $html, $out);
  //$parts  = json_decode('{'.$out['out'].'}', true);
  //if ($parts['args']['livestream']== 1) {
  //if ($parts['args']['live_default_broadcast'] == 1) {
    //$r=$parts['args']['hlsvp'];

    $r1=json_decode($parts['args']['player_response'],1);
    //print_r ($r1);
 if (isset($r1['streamingData']["hlsManifestUrl"])) {
    $url=$r1['streamingData']["hlsManifestUrl"];
    //echo $url;
      $ua="Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      $a1=explode("\n",$h);
      //print_r ($a1);
      if (preg_match("/\.m3u8/",$h)) {
       preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
       $max_res=max($m[1]);
       //echo $max_res."\n";
       for ($k=0;$k<count($a1);$k++) {
        if (strpos($a1[$k],$max_res) !== false) {
         $r=trim($a1[$k+1]);
         break;
        }
       }
      }
    return $r;
  } else {
  //include ("y.php");
  $videos = explode(',', $parts['args']['url_encoded_fmt_stream_map']);
  //parse_str($html,$parts);
  //$videos = explode(',',$parts['url_encoded_fmt_stream_map']); 
  foreach ($videos as $video) {
		parse_str($video, $output);

		if (in_array($output['itag'], $a_itags)) break;
	}

	//$path = $output['url'].'&';
	//echo $path;
//  unset($output['url']);

	//if (isset($output['fexp']))          unset($output['fexp']);
	if (isset($output['type']))          unset($output['type']);
	if (isset($output['mv']))            unset($output['mv']);
	if (isset($output['sver']))          unset($output['sver']);
	if (isset($output['mt']))            unset($output['mt']);
	if (isset($output['ms']))            unset($output['ms']);
	if (isset($output['quality']))       unset($output['quality']);
	if (isset($output['codecs']))        unset($output['codecs']);
	if (isset($output['fallback_host'])) unset($output['fallback_host']);
    //print_r ($output);
	//$r=urldecode($path.http_build_query($output));
	if (!isset($output['s'])) {
		//$signature=($output['sig']);
        //print_r ($output);
        $r=$output['url'];
	} else {
  $sA="";
  $s=$output["s"];
  //echo $s;
  $tip=$output["sp"];
  $l = "https://s.ytimg.com".$parts['assets']['js'];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $html1=str_replace("\n","",$html);
  //echo $html1;
  //die();
  //preg_match('/"*signature"*,\s?(?P<name>[a-zA-Z0-9$]+)\(/',$html1,$m);
  preg_match('/([A-Za-z]{2})=function\(a\){a=a\.split\(\"\"\)/',$html1,$m);
  //print_r ($m);  //UK
  //$sig=$m["name"];
  $sig=$m[1];
  $find='/\s?'.$sig.'=function\((?P<parameter>[^)]+)\)\s?\{\s?(?P<body>[^}]+)\s?\}/';
  preg_match($find,$html1,$m1);
  //print_r ($m1);  //UK=function(a){a=a.split("")
  //[parameter] = a
  //[body] = a=a.split("");TK.z6(a,23);TK.p8(a,2);TK.d4(a,55);TK.z6(a,6);return a.join("")
  //z6:function(a,b){var c=a[0];a[0]=a[b%a.length];  ----> _slice($sA,23)
  //var TK={p8:function(a,b){a.splice(0,b)} splice($sA,2)
  //d4:function(a){a.reverse()}}
  //z6:function(a,b){var c=a[0];a[0]=a[b%a.length];  ---> _slice($sA,6);

  // caut foate functiile de genul XY:function(a,b)
  preg_match_all("/\w{2}\:function\(\w,\w\)\{[\w\s\=\[\]\=\%\.\;\(\)\,]*\}/",$html1,$m3);

  $a=array(); // funtii gasite $a[XY]= splice etc
  for ($k=0;$k<count($m3[0]);$k++) {
    preg_match("/(\w{2})(\:function\(\w,\w\)\{)([\w\s\=\[\]\=\%\.\;\(\)\,]*)\}/",$m3[0][$k],$m4);
    //print_r ($m4);
    $a[$m4[1]]=$m4[3];
  }

  // caut toate functiile de genul XY:function(a)
  preg_match_all("/\w{2}\:function\(\w\)\{[\;\.\s\w\,\"\:\(\)\{\{]*\}/",$html1,$m2);
  //print_r ($m2);
  for ($k=0;$k<count($m2[0]);$k++) {
     preg_match("/(\w{2})(\:function\(\w\)\{)([\;\.\s\w\,\"\:\(\)\{\{]*)\}/",$m2[0][$k],$m5);
     $a[$m5[1]]=$m5[3];
  }
  //print_r ($a);

  //$x3 = preg_replace("/\w{2}\./","",$m1["body"][0]);
  $x3 = preg_replace("/\w{2}\./","",$m1["body"]);
  $f=explode(";",$x3);
  //print_r ($f);
  //b.Sl(a)
  for ($k=0;$k<count($f);$k++) {
    if (preg_match("/split/",$f[$k]))
      $sA = str_split($s);
    elseif (preg_match("/join/",$f[$k]))
      $sA = implode($sA);
    elseif (preg_match("/(\w+)\(\w+,(\d+)/",$f[$k],$r1)) { //AT(a,33)
       //print_r ($r);
       if (!$a[$r1[1]]) //daca nu exista nicio functie..... nu cred ca e cazul
          $sA = _slice($sA,$r1[2]); //????
       else {
         if (preg_match("/splice/",$a[$r1[1]]))
            $sA = _splice($sA,$r1[2]);
         elseif (preg_match("/reverse/",$a[$r1[1]]))
            $sA = _reverse($sA,$r1[2]);
         elseif (preg_match("/\w%\w\.length/",$a[$r1[1]]))
            $sA = _slice($sA,$r1[2]);
       }
    }
  }
  $signature = $sA;
  $r=$output['url']."&".$tip."=".$signature;
}


return $r;
}
} else
  return "";
}

//***************Here we start**************************************
$filelink=str_prep($filelink);
//echo $filelink;
if (strpos($filelink,"daclips.") !== false || strpos($filelink,"movpod.") !== false) {
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
} elseif (strpos($filelink,"moviesjoy.net") !== false) {
  //$filelink="https://www.moviesjoy.net/ajax/movie_sources/2203229-30";
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $y=json_decode($h,1);
  //print_r ($r);
  $r=$y["playlist"][0]["sources"];
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
     $link="";
  if (isset($y["playlist"][0]["tracks"][0]["file"]))
     $srt=$y["playlist"][0]["tracks"][0]["file"];
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
  //https://megaxfer.ru/embed/5cea5502e49c0
  $pattern = '@(?:\/\/|\.)(megaxfer\.ru)\/(?:embed)?\/([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$m);

  //echo $l;
  $l="https://megaxfer.ru/player?fid=".$m[2]."&page=embed";   // ??? vidcloud
  $h2=file_get_contents($l);   // ???? why ?????????
  $h2=str_replace("\\","",$h2);
  $h2=str_replace("\n","",$h2);
  $link=str_between($h2,'file":"','"');

} elseif (strpos($filelink,"idtbox.com") !== false) {
  //https://idtbox.com/avslpcj48so9
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
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
  //echo $out;
  if (preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1];
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
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
} elseif (strpos($filelink,"gamovideo.") !== false) {
  //http://gamovideo.com/gd82bzc3i6eq
  //http://gamovideo.com/embed-gd82bzc3i6eq-640x360.html
  $pattern = '@(?:\/\/|\.)(gamovideo\.com)\/(?:embed-)?([a-zA-Z0-9]+)@';
  preg_match($pattern,$filelink,$r);
  $l="http://gamovideo.com/".$r[2];
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
  //print_r ($m);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1];
  } else {
    $link="";
  }
} elseif (strpos($filelink,"flix555.com") !== false) {
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
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h3);
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
} elseif (strpos($filelink,"flixtor.") !== false) {
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  $ua=$user_agent;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://flixtor.ac/movies',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Cookie: approve=1; approve_search=yes'
);

$l=$filelink;

//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://flixtor.ac/movies");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
  $t1=explode('iframe class="',$h2);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://flixtor.ac/movies");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  //echo $h3;
  //die();
if (preg_match_all("/source src=\'(.*?)\'.*?data-res\=\'(\d+)\'/ms",$h3,$m)) {
//print_r ($m);
$bfound=false;
$q=array("1080","720","480","360","240","180");
foreach($q as $v) {
 for ($k=0;$k<count($m[2]);$k++) {
 if ($m[2][$k] == $v) {
   $out=$m[1][$k];
   $bfound=true;
   break;
 }
 if ($bfound) break;
 }
 if ($bfound) break;
}
} else {
  $out="";
}
 $link=$out;
//http://127.0.0.1:8080/scripts/filme/flixtor.php?file=https%3A%2F%2Fdl.flixtor.ac%2Ffiles%2Fyts%2F720p%2F1486254.mp4
  if ($flash == "mp") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $out);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://flixtor.ac/movies");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  $h3 = curl_exec($ch);
  curl_close($ch);
  $t1=explode("Location:",$h3);
  $t2=explode("\n",$t1[1]);
  $l="https:".trim($t2[0]);
   $link="http://127.0.0.1:8080/scripts/filme/flixtor.php?file=".urlencode($l);
  }
  //echo $link;
  //die();
 //https://cdn.flixtor.ac/embed/getfile?id=1486254&res=720p
//$movie="https://dl.flixtor.ac/files/yts/1080p/42389.mp4";

 //echo $out;
$head=array('Accept: video/webm,video/ogg,video/*;q=0.9,application/ogg;q=0.7,audio/*;q=0.6,*/*;q=0.5',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: gzip, deflate, br',
'Range: bytes=0-',
'Referer: https://cdn.flixtor.ac/',
'Connection: keep-alive',
'Cookie: _ga=GA1.2.47924367.1557066380; _gid=GA1.2.1870124090.1557066380; approve=1; _gat=1'
);
  /*
  $out="https://dl.flixtor.ac/files/yts/1080p/51111.mp4";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $out);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://flixtor.ac/movies");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  $h3 = curl_exec($ch);
  curl_close($ch);
  echo $h3;
  */
  //die();
 if (preg_match_all ("/track kind\=\'captions\' src\=\'(https\:\/\/cdn\.flixtor\.ac\/embed\/subs\?id=\d+\&lang\=English)\'/ms",$h3,$s))
  $srt=$s[1][0];
} elseif (strpos($filelink,"flowyourvideo") !== false) {
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
  $t1=explode("Location:",$h4);
  $t2=explode("\n",$t1[1]);
  $link=trim($t2[0]);
} elseif (strpos($filelink,"fembed.") !== false) {
  //https://www.fembed.com/v/4lo0jr-px9q
  if ($flash=="flash")
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $ua = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
     preg_match("/v\/([\w\-]*)/",$filelink,$m);
     //print_r ($m);
     $id=$m[1];
     $l="https://www.fembed.com/api/source/".$id;
  $post="r=";
  //echo $l;
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_REFERER,"https://www.fembed.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close($ch);
  */
$url = $l;
$data = array('r' => '');
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$h3 = @file_get_contents($url, false, $context);
  $r=json_decode($h3,1);
  //print_r ($r);
  //die();
  //https://www.fembed.com/asset/caption/05ol0yz4no6/bloom-s01e01-srt_2019-01-13.srt
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://www.fembed.com/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
    //echo $srt;
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $srt);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_REFERER,"https://www.fembed.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch,CURLOPT_HEADER,1);
  //curl_setopt($ch,CURLOPT_NOBODY,1);
  $h4 = curl_exec($ch);
  curl_close($ch);
  echo $h4;
  */
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $l1="https://www.fembed.com".$r["data"][$c-1]["file"];
  else
   $l1=$r["data"][$c-1]["file"];
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_REFERER,"https://www.fembed.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch,CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h4 = curl_exec($ch);
  curl_close($ch);

  $t1=explode("Location:",$h4);
  $t2=explode("\n",$t1[1]);
  $link=trim($t2[0]);
  */
  $h4=@get_headers($l1);
  //print_r ($h4);
  foreach ($h4 as $key => $value) {
    if (preg_match("/Location/",$value)) {
       $t1=explode("Location:",$value);
       $t2=explode("\n",$t1[1]);
       $link=trim($t2[0]);
       break;
    }
  }
  //echo $l1;
  //$link=$l1;
  //$link=str_replace("https","http",$link);
} elseif (strpos($filelink,"gcloud.live") !== false) {
  //https://gcloud.live/v/1xoq887kxo4
  preg_match("/\/v\/([0-9a-zA-Z]+)/",$filelink,$m);
  $id=$m[1];
  $l="https://gcloud.live/api/source/".$id;

  $post="r=&d=gcloud.live";
  $url = $l;
  $data = array('r' => '');
  $options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
  );

  $context  = stream_context_create($options);
  $h3 = @file_get_contents($url, false, $context);
  $r=json_decode($h3,1);
  //print_r ($r);
  //die();
  //https://www.fembed.com/asset/caption/05ol0yz4no6/bloom-s01e01-srt_2019-01-13.srt
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://gcloud.live/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $l1="https://gcloud.live".$r["data"][$c-1]["file"];
  else
   $l1=$r["data"][$c-1]["file"];
   $link=$l1;
} elseif (strpos($filelink,"xstreamcdn.com") !== false) {
  //https://www.xstreamcdn.com/v/132y5bj8mxx77yz
     preg_match("/\/v\/([0-9a-zA-Z\-\_]+)/",$filelink,$m);
     $id=$m[1];
     $l="https://xstreamcdn.com/api/source/".$id;

  $post="r=";
$url = $l;
$data = array('r' => '');
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$h3 = @file_get_contents($url, false, $context);
  $r=json_decode($h3,1);
  //print_r ($r);
  //die();
  //https://www.fembed.com/asset/caption/05ol0yz4no6/bloom-s01e01-srt_2019-01-13.srt
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://xstreamcdn.com/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $link="https://xstreamcdn.com".$r["data"][$c-1]["file"];
  else
   $link=$r["data"][$c-1]["file"];
} elseif (strpos($filelink,"smartshare.tv") !== false) {
  //https://smartshare.tv/v/1xvqqndmnxv
  //$filelink="https://smartshare.tv/v/1xvqqndmnxv";

     preg_match("/\/v\/([0-9a-zA-Z]+)/",$filelink,$m);
     $id=$m[1];
     $l="https://smartshare.tv/api/source/".$id;

  $post="r=";
$url = $l;
$data = array('r' => '');
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$h3 = @file_get_contents($url, false, $context);
  $r=json_decode($h3,1);
  //print_r ($r);
  //die();
  //https://www.fembed.com/asset/caption/05ol0yz4no6/bloom-s01e01-srt_2019-01-13.srt
  if (isset($r["captions"][0]["path"])) {
   if (strpos($r["captions"][0]["path"],"http") === false)
    $srt="https://smartshare.tv/asset".$r["captions"][0]["path"];
   else
    $srt=$r["captions"][0]["path"];
  }
  $c = count($r["data"]);
  if (strpos($r["data"][$c-1]["file"],"http") === false)
   $l1="https://smartshare.tv".$r["data"][$c-1]["file"];
  else
   $l1=$r["data"][$c-1]["file"];
   $link=$l1;
  /*
  $h4=@get_headers($l1);
  //print_r ($h4);
  foreach ($h4 as $key => $value) {
    if (preg_match("/Location/",$value)) {
       $t1=explode("Location:",$value);
       $t2=explode("\n",$t1[1]);
       $link=trim($t2[0]);
       break;
    }
  }
  */
} elseif (strpos($filelink,"vshare.eu") !== false) {
  //http://vshare.eu/25td5yq2cd6k.htm
  //http://vshare.eu/embed-25td5yq2cd6k-600x300.html
  //https://vshare.eu/iautmgnk1jm3.html
  //echo $filelink;
  $pattern = '/(?:\/\/|\.)(vshare\.eu)\/(?:embed-|)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  $filelink="https://vshare.eu/".$m[2];
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
  $id=str_between($h,'name="id" value="','"');
  $fname=str_between($h,'name="fname" value="','"');
  $referer=str_between($h,'referer" value="','"');
  //$hash=str_between($h,'name="hash" value="','"');
  //$link_post=str_between($h,"method="POST" action='"
  //op=download1&usr_login=&id=rwfwx0jdymas&fname=Insecure+%282016%E2%80%93+%29+S01E01.mkv&referer=http%3A%2F%2Fputlocker.is%2Fwatch-insecure-tvshow-season-1-episode-1-online-free-putlocker.html&hash=227666-82-210-1475052778-cd2dc1bd37c494120754b5f6200349f1&imhuman=Proceed+to+video
  $post="op=download1&usr_login=&id=".$id."&fname=".urlencode($fname)."&referer=&method_free=Proceed+to+video";
  //op=download1&usr_login=&id=25td5yq2cd6k&fname=nympho_aunt.mp4&referer=&method_free=Proceed+to+video
  //$post="op=download1&usr_login=&id=iautmgnk1jm3&fname=Madam.Secretary.S05E17.HDTV.x264-KILLERS.mkv.mp4&referer=&method_free=Proceed+to+video";
  //echo $post;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).''
);
  sleep(1);
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
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
  $pattern = '/(?:\/\/|\.)(thevideobee\.to)\/(?:embed-|)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  $filelink="https://thevideobee.to/".$m[2].".html";
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
  $id=str_between($h,'name="id" value="','"');
  $fname=str_between($h,'name="fname" value="','"');
  $referer=str_between($h,'referer" value="','"');
  $hash=str_between($h,'name="hash" value="','"');
  $usr_login=str_between($h,'usr_login" value="','"');
  //$link_post=str_between($h,"method="POST" action='"
  //op=download1&usr_login=&id=rwfwx0jdymas&fname=Insecure+%282016%E2%80%93+%29+S01E01.mkv&referer=http%3A%2F%2Fputlocker.is%2Fwatch-insecure-tvshow-season-1-episode-1-online-free-putlocker.html&hash=227666-82-210-1475052778-cd2dc1bd37c494120754b5f6200349f1&imhuman=Proceed+to+video
  $post="op=download1&usr_login=".$usr_login."&id=".$id."&fname=".urlencode($fname)."&referer=".$referer."&hash=".$hash."&imhuman=Proceed+to+video";
  //op=download1&usr_login=&id=25td5yq2cd6k&fname=nympho_aunt.mp4&referer=&method_free=Proceed+to+video
  //$post="op=download1&usr_login=&id=iautmgnk1jm3&fname=Madam.Secretary.S05E17.HDTV.x264-KILLERS.mkv.mp4&referer=&method_free=Proceed+to+video";
  //echo $post;
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).''
);
  sleep(1);
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match('/[src="]((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h, $m))
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
  if (preg_match('/[{file:"]((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m))
   $link=$m[1];
  else
   $link="";
} elseif (strpos($filelink,"gorillavid") !== false) {
  //https://gorillavid.in/96ce7ik16aoj
  //http://gorillavid.in/l3hqo5zhd59b
  $pattern = '/(gorillavid\.(?:in|com))\/(?:embed-)?([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$x);
  //print_r ($x);
  $id=$x[2];
  $filelink="https://gorillavid.in/".$id;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:62.0) Gecko/20100101 Firefox/62.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  $csrfmiddlewaretoken=str_between($h,"csrfmiddlewaretoken' value='","'");
  $id=str_between($h, 'id" value="','"');
  $fname=str_between($h,'fname" value="','"');
  $post="csrfmiddlewaretoken=".$csrfmiddlewaretoken."&op=download1&usr_login=&id=".$id."&fname=".$fname."&referer=&channel=&method_free=Free+Download";
  sleep(2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m))
   $link=$m[1];
  else
   $link="";
  //echo $link;
  //die();
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
function abc($a52, $a10)
{
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
    for ($a72 = 0x0; $a72 < 0x100; $a72++) {     //new
        $a54[$a72] = (0x3 + $a72 + pow(0x7c,0x0)) % 0x100;
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
    require_once("JavaScriptUnpacker.php");
    preg_match('/(powvideo|powvideo)\.(net|cc)\/(?:embed-|iframe-|preview-|)([a-z0-9]+)/', $filelink, $m);
    $id       = $m[3];
    $filelink = "https://povvideo.net/embed-" . $id . ".html";
    $ua       = $_SERVER["HTTP_USER_AGENT"];
    $head     = array(
        'Cookie: ref_url=' . urlencode($filelink) . '; BJS0=1; BJS1=1; e_' . $id . '=123456789'
    );
    $l        = "https://powvideo.net/iframe-" . $id . "-954x562.html";
    $ch       = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_REFERER, "https://povvideo.net/preview-" . $id . "-732x695.html");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);

    $h = str_replace("/player7", "https://povvideo.net/player7", $h);
    $h = str_replace("/js", "https://povvideo.net/js", $h);
    //file_put_contents("s1.html",$h);
    //die();

    if (strpos($h, "function getCalcReferrer") !== false) {
       $t1 = explode("function getCalcReferrer", $h);
       $h  = $t1[1];
    }
    $jsu   = new JavaScriptUnpacker();
    $out   = $jsu->Unpack($h);
    if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $out, $m)) {
        $link = $m[1];
        $t1   = explode("/", $link);
        $a145 = $t1[3];
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $xx)) {
        $srt = $xx[1];
    if (strpos("http", $srt) === false && $srt)
        $srt = "https://powvideo.net" . $srt;
    }
    /*
    $c0 fisrt array
    $c1 second array (if exist) but only after replace with function abc
    */

    /* search first array var _0x1107=['asass','ssdsds',.....] */
    if (preg_match("/(var\s+(_0x[a-z0-9]+))\=\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
        $php_code = str_replace($m[1], "\$c0", $m[0]) . ";";
        eval($php_code);
        //print_r ($c0);
        /* rotate with 0xd0 search (_0x1107,0xd0)) */
        $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/";
        if (preg_match($pat, $h, $n)) {
            $x = hexdec($n[1]);
            for ($k = 0; $k < $x; $k++) {
                array_push($c0, array_shift($c0));
            }
        }
        //echo $x;
        $h = str_replace("+", "", $h);
        /* check if exist second array and get replacement for abc function and slice*/
        /* search Array[_0x3504(_0xfcc8('0x22','uSSR'))] */
        if (preg_match("/Array\[(_0x[a-z0-9]+)\((_0x[a-z0-9]+)\(/", $h, $f)) {
            $func  = $f[2];
            $func1 = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat   = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
            if (preg_match_all($pat, $h, $p)) {
                for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], "'".abc($c0[hexdec($p[2][$z])], $p[3][$z])."'", $h);
                }
            }
            //echo $h;
            /* search for second array var _0x13e4=[xcxcxc,xcxc,xcxcx ...] */
            if (preg_match_all("/(var\s+(_0x[a-z0-9]+))\=\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
            //print_r ($m);
            if (isset($m[1][1])) {
                $php_code = $m[0][1];
                $php_code = str_replace($m[1][1], "\$c1", $php_code) . ";";
                /* get second array $c1 and rotate */
                eval($php_code);
                //print_r ($c1);
                //die();
                $pat = "/\(" . $m[2][1] . "\,(0x[a-z0-9]+)/ms";
                if (preg_match($pat, $h, $n)) {
                    $x = hexdec($n[1]);
                    for ($k = 0; $k < $x; $k++) {
                        array_push($c1, array_shift($c1));
                    }
                }
                //print_r ($c1);
                //echo $x;
                /* Variant 1 only decode $c1 and get r.splice.... may be a solution
                $out = "";
                for ($k = 0; $k < count($c1); $k++) {
                $out .= base64_decode($c1[$k]);
                }
                echo $out;
                */
                /* search and replace _0x3504(0x6) etc with second array $c1 */
                $pat = "/" . $func1 . "\(\'(0x[0-9a-f]+)\'\)/ms";
                $pat1   = "/(" . $func1 . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
                if (preg_match_all($pat, $h, $q)) {
                   for ($k = 0; $k < count($q[1]); $k++) {
                    $h = str_replace($q[0][$k], base64_decode($c1[hexdec($q[1][$k])]), $h);
                   }
                } else if (preg_match_all($pat1, $h, $p)) {
                   //print_r ($p);
                   for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], abc($c1[hexdec($p[2][$z])], $p[3][$z]), $h);
                   }
                   //echo $h;
                }
            }
            }
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $let = $e[2];
                /* now is "r" - for future.... */
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
            /* if not second array search Array[_0x5f0b('0x0','9YsV')] */
        } else if (preg_match("/Array\[(_0x[a-z0-9]+)\(\'0x/ms", $h, $f)) {
            $func = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat  = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/ms";
            preg_match_all($pat, $h, $p);
            for ($z = 0; $z < count($p[0]); $z++) {
                $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
            }
            //echo $h;
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        }
        /* $out can like this r.splice( "3", 1);$("body").data("f 0",197);r[$("body").data("f 0")&15]=r.splice($("body").data("f 0")>>(33), 1 */


    } else if (preg_match("/(function\s?(_0x[a-z0-9]+)\(\)\{return)\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
        $php_code = str_replace($m[1], "\$c0=", $m[0]) . ";";
        eval($php_code);
        $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/";
        if (preg_match($pat, $h, $n)) {
            $x = hexdec($n[1]);
            for ($k = 0; $k < $x; $k++) {
                array_push($c0, array_shift($c0));
            }
        }
        $h = str_replace("+", "", $h);
        if (preg_match("/Array\[(_0x[a-z0-9]+)\(\'0x/ms", $h, $f)) {
            $func = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat  = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/ms";
            preg_match_all($pat, $h, $p);
            for ($z = 0; $z < count($p[0]); $z++) {
                $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
            }
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        } else if (preg_match("/Array\[(_0x[a-z0-9]+)\((_0x[a-z0-9]+)\(/", $h, $f)) {
            $func  = $f[2];
            $func1 = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat   = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
            if (preg_match_all($pat, $h, $p)) {
                for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
                }
            }
            /* search for second array var _0x13e4=[xcxcxc,xcxc,xcxcx ...] */
            if (preg_match("/(var\s+(_0x[a-z0-9]+))\=\[([a-zA-Z0-9\=\+\/]+\,?)+\]/ms", $h, $m)) {
                $php_code = str_replace(",", "','", $m[0]);
                $php_code = str_replace("[", "['", $php_code);
                $php_code = str_replace("]", "']", $php_code);
                $php_code = str_replace($m[1], "\$c1", $php_code) . ";";
                /* get second array $c1 and rotate */
                eval($php_code);
                $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/ms";
                if (preg_match($pat, $h, $n)) {
                    $x = hexdec($n[1]);
                    for ($k = 0; $k < $x; $k++) {
                        array_push($c1, array_shift($c1));
                    }
                }
                /* Variant 1 only decode $c1 and get r.splice.... may be a solution
                $out = "";
                for ($k = 0; $k < count($c1); $k++) {
                $out .= base64_decode($c1[$k]);
                }
                echo $out;
                */
                /* search and replace _0x3504(0x6) etc with second array $c1 */
                $pat = "/" . $func1 . "\((0x[0-9a-f]+)\)/ms";
                if (preg_match_all($pat, $h, $q)) {
                    for ($k = 0; $k < count($q[1]); $k++) {
                        $h = str_replace($q[0][$k], base64_decode($c1[hexdec($q[1][$k])]), $h);
                    }
                }
            }
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $let = $e[2];
                /* now is "r" - for future.... */
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        }
    }
    /* $out */
    $out=str_replace("(Math.round(","",$out);
    $out=str_replace("))","",$out);
    if (preg_match_all("/\(\"body\"\)\.data\(\"(\w\s*\d)\"\,(\d+)\)/", $out, $u)) {
        for ($k = 0; $k < count($u[0]); $k++) {
            $out = str_replace("$" . $u[0][$k] . ";", "", $out);
            $out = str_replace('$("body").data("' . $u[1][$k] . '")', $u[2][$k], $out);
        }
    }
    $out = str_replace('"', "", $out);
    /* now is like array_splice($r, 3, 1);$r[388&15]=array_splice($r,388>>(3+3), 1, $r[388&15])[0]; etc */
    $d   = str_replace("r.splice(", "array_splice(\$r,", $out);
    $d   = str_replace("r[", "\$r[", $d);

    if (preg_match("/(array\_splice(.*))\;/", $d, $f)) {
        $d = $f[0];
    }
    $r = str_split(strrev($a145));
    eval($d);
    $x    = implode($r);
    $link = str_replace($a145, $x, $link);
} else {
    $link = "";
}
//
//http://54.36.123.6:8777/muohozetfaikkfn2mb4p6ocnnt233glcfupowuwsjnq5zghyftfl6hxvce/l28hwji0d8v4_n.ts?video=135
//http://54.36.123.6:8777/ecvxh6lftfyhgz5qnjswuwopufclg332tnnco6p4bm2nfkkiaftezohs2oum/v.mp4
////_0x16f256[_0x4c7c('0x8', 'SGjY')](0x3, 0x2); ???????????
} elseif (strpos($filelink,"vcstream.to") !== false) {
  $cookie=$base_cookie."vcstream.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:61.0) Gecko/20100101 Firefox/61.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1 = explode("url: '/",$h);
  $t2=explode("'",$t1[1]);
  $l1="https://vcstream.to/".$t2[0];

$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:61.0) Gecko/20100101 Firefox/61.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: gzip, deflate, br');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $h=str_replace("\\","",$h);
  $t1=explode('file":"',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];


  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
  $srt=$m[1];
  //echo $srt;
} elseif (strpos($filelink,"clipwatching") !== false) {
  //https://clipwatching.com/embed-afw5jbvb8hqm.html
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
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m);
  $link=$m[1];
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
  
} elseif (strpos($filelink,"streamplay1.to") !== false || strpos($filelink,"streamplay1.me") !== false) {
  //http://streamplay.to/f774b3pzd7iy
  if (!file_exists($base_script."/filme/streamplay.txt")) die();
  preg_match('/(?:\/\/|\.)(streamplay\.(?:to|club|top|me))\/(?:embed-|player-)?([0-9a-zA-Z]+)/',$filelink,$m);
  $filelink="https://streamplay.me/player-".$m[2]."-920x360.html";
  $ua=$_SERVER["HTTP_USER_AGENT"];
  //$ua="";
  //echo $ua;
  require_once("JavaScriptUnpacker.php");
  $h=file_get_contents("streamplay.html");
  //echo $h;
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $x)) {
    //src:"/srt/00686/ic19hoyeob1d_Italian.vtt"
    $srt=$x[1];
    if (strpos("http",$srt) === false && $srt) $srt="http://streamplay.to".$srt;
    //print_r ($x);
    //echo $srt;
  }
  $h2=file_get_contents("streamplay.txt");
  //echo $out;
  //$link=unpack_DivXBrowserPlugin(1,$h);
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $h2, $m);
  $link=$m[1];
  //x,a,5,1,j,b,f,g,7,u,h,e,k,a,l,h,u,l,o,c,t,e,i,6,g,j,q,7,v,b,y,7,n,2,j,o,e,b,l,u,r,k,c,r,v,7,e,c,c,e,m,o,l,z,x,b,5,q,v,m,q
  //qmvq5bxzlomecce7vrckrulbeoj2n7ybv7qjg6ietcoluhlakehu7gfbj15ax
//var _0x302cc5=_0x218e('0x6','3u&i')+_0x218e('0x7','FS@G')+_0x218e('0x8','kmQ0')+_0x218e('0x9','J%Du')+_0x218e('0xa','7@Ri')+_0x218e('0xb','LPFL')
//var _0xfc4e55=_0x4552('0x6','tjP7')+_0x4552('0x7','75@D')+_0x4552('0x8','Lbbd')+_0x4552('0x9','kQlg')+_0x4552('0xa','Q(ZR')+_0x4552('0xb','FRDo');
  //r.splice( 3 , 1);r[3]=r.splice(5 , 1, r[3])[0];r[8]=r.splice(4 , 1, r[8])[0];r[0]=r.splice(9 , 1, r[0])[0];r[7]=r.splice(1 , 1, r[7])[0];
  //r.splice( 3 , 1);r[0]=r.splice(2 , 1, r[0])[0];r[8]=r.splice(9 , 1, r[8])[0];r[1]=r.splice(5 , 1, r[1])[0];r[3]=r.splice(6 , 1, r[3])[0];
//r.splice( 3 , 1);
//r[3]=r.splice(5 , 1, r[3])[0];
//r[8]=r.splice(4 , 1, r[8])[0];
//r[0]=r.splice(9 , 1, r[0])[0];
//r[7]=r.splice(1 , 1, r[7])[0];
/*
$t1=explode("/",$link);
$a145=$t1[3];
$r=str_split(strrev($a145));
array_splice($r, 3 , 1);
$r[3]=array_splice($r,5 , 1, $r[3])[0];
$r[8]=array_splice($r,4 , 1, $r[8])[0];
$r[0]=array_splice($r,9 , 1, $r[0])[0];
$r[7]=array_splice($r,1 , 1, $r[7])[0];
$x=implode($r);
$link=str_replace($a145,$x,$link);
*/
} elseif (strpos($filelink,"streamplay.to") !== false || strpos($filelink,"streamplay.me") !== false) {
require_once("JavaScriptUnpacker.php");
function abc($a52, $a10) {
            $a54 = array();
                $a55 = 0x0;
                $a56 = '';
                $a57 = '';
                $a58 = '';
            $a52 = base64_decode($a52);
            $a52=mb_convert_encoding($a52,'ISO-8859-1', 'UTF-8');
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
    for ($a72 = 0x0; $a72 < 0x100; $a72++) {     //new
        $a54[$a72] = (0x3 + $a72 + pow(0x7c,0x0)) % 0x100;
    }
            for ($a72 = 0x0; $a72 < 0x100; $a72++) {
                $a55 = ($a55 + $a54[$a72] + ord($a10[($a72 % strlen($a10))])) % 0x100;
                $a56 = $a54[$a72];
                $a54[$a72] = $a54[$a55];
                $a54[$a55] = $a56;
            }
            $a72 = 0x0;
            $a55 = 0x0;
            for ($a100 = 0x0; $a100 < strlen($a52); $a100++) {
                $a72 = ($a72 + 0x1) % 0x100;
                $a55 = ($a55 + $a54[$a72]) % 0x100;
                $a56 = $a54[$a72];
                $a54[$a72] = $a54[$a55];
                $a54[$a55] = $a56;
                $xx = $a54[($a54[$a72] + $a54[$a55]) % 0x100];
                $a57 .= chr(ord($a52[$a100]) ^ $xx);
            }
            return $a57;
}
preg_match('/(?:\/\/|\.)(streamplay\.(?:to|club|top|me))\/(?:embed-|player-)?([0-9a-zA-Z]+)/', $filelink, $m);
$filelink = "https://streamplay.me/player-" . $m[2] . "-920x360.html";
$ua       = $_SERVER["HTTP_USER_AGENT"];
$head     = array(
    'Cookie: lang=1; ref_yrp=http%3A%2F%2Fcecileplanche-psychologue-lyon.com%2Fshow%2Fthe-good-cop%2Fseason-1%2Fepisode-2; ref_kun=1'
);
$ch       = curl_init($filelink);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_ENCODING, "");
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_REFERER, $filelink);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
if (strpos($h, "function getCalcReferrer") !== false) {
    $t1 = explode("function getCalcReferrer", $h);
    $h  = $t1[1];
}
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
        $srt = "https://streamplay.to" . $srt;
    }
    /*
    $c0 fisrt array
    $c1 second array (if exist) but only after replace with function abc
    */

    /* search first array var _0x1107=['asass','ssdsds',.....] */
    if (preg_match("/(var\s+(_0x[a-z0-9]+))\=\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
        $php_code = str_replace($m[1], "\$c0", $m[0]) . ";";
        eval($php_code);
        //print_r ($c0);
        /* rotate with 0xd0 search (_0x1107,0xd0)) */
        $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/";
        if (preg_match($pat, $h, $n)) {
            $x = hexdec($n[1]);
            for ($k = 0; $k < $x; $k++) {
                array_push($c0, array_shift($c0));
            }
        }
        //echo $x;
        $h = str_replace("+", "", $h);
        /* check if exist second array and get replacement for abc function and slice*/
        /* search Array[_0x3504(_0xfcc8('0x22','uSSR'))] */
        if (preg_match("/Array\[(_0x[a-z0-9]+)\((_0x[a-z0-9]+)\(/", $h, $f)) {
            $func  = $f[2];
            $func1 = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat   = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
            if (preg_match_all($pat, $h, $p)) {
                for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], "'".abc($c0[hexdec($p[2][$z])], $p[3][$z])."'", $h);
                }
            }
            //echo $h;
            /* search for second array var _0x13e4=[xcxcxc,xcxc,xcxcx ...] */
            if (preg_match_all("/(var\s+(_0x[a-z0-9]+))\=\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
            //print_r ($m);
            if (isset($m[1][1])) {
                $php_code = $m[0][1];
                $php_code = str_replace($m[1][1], "\$c1", $php_code) . ";";
                /* get second array $c1 and rotate */
                eval($php_code);
                //print_r ($c1);
                //die();
                $pat = "/\(" . $m[2][1] . "\,(0x[a-z0-9]+)/ms";
                if (preg_match($pat, $h, $n)) {
                    $x = hexdec($n[1]);
                    for ($k = 0; $k < $x; $k++) {
                        array_push($c1, array_shift($c1));
                    }
                }
                //print_r ($c1);
                //echo $x;
                /* Variant 1 only decode $c1 and get r.splice.... may be a solution
                $out = "";
                for ($k = 0; $k < count($c1); $k++) {
                $out .= base64_decode($c1[$k]);
                }
                echo $out;
                */
                /* search and replace _0x3504(0x6) etc with second array $c1 */
                $pat = "/" . $func1 . "\(\'(0x[0-9a-f]+)\'\)/ms";
                $pat1   = "/(" . $func1 . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
                if (preg_match_all($pat, $h, $q)) {
                   for ($k = 0; $k < count($q[1]); $k++) {
                    $h = str_replace($q[0][$k], base64_decode($c1[hexdec($q[1][$k])]), $h);
                   }
                } else if (preg_match_all($pat1, $h, $p)) {
                   //print_r ($p);
                   for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], abc($c1[hexdec($p[2][$z])], $p[3][$z]), $h);
                   }
                   //echo $h;
                }
            }
            }
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $let = $e[2];
                /* now is "r" - for future.... */
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
            /* if not second array search Array[_0x5f0b('0x0','9YsV')] */
        } else if (preg_match("/Array\[(_0x[a-z0-9]+)\(\'0x/ms", $h, $f)) {
            $func = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat  = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/ms";
            preg_match_all($pat, $h, $p);
            for ($z = 0; $z < count($p[0]); $z++) {
                $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
            }
            //echo $h;
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        }
        /* $out can like this r.splice( "3", 1);$("body").data("f 0",197);r[$("body").data("f 0")&15]=r.splice($("body").data("f 0")>>(33), 1 */


    } else if (preg_match("/(function\s?(_0x[a-z0-9]+)\(\)\{return)\[(\'[a-zA-Z0-9\=\+\/]+\'\,?)+\]/ms", $h, $m)) {
        $php_code = str_replace($m[1], "\$c0=", $m[0]) . ";";
        eval($php_code);
        $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/";
        if (preg_match($pat, $h, $n)) {
            $x = hexdec($n[1]);
            for ($k = 0; $k < $x; $k++) {
                array_push($c0, array_shift($c0));
            }
        }
        $h = str_replace("+", "", $h);
        if (preg_match("/Array\[(_0x[a-z0-9]+)\(\'0x/ms", $h, $f)) {
            $func = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat  = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/ms";
            preg_match_all($pat, $h, $p);
            for ($z = 0; $z < count($p[0]); $z++) {
                $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
            }
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        } else if (preg_match("/Array\[(_0x[a-z0-9]+)\((_0x[a-z0-9]+)\(/", $h, $f)) {
            $func  = $f[2];
            $func1 = $f[1];
            /* find and replace _0xfcc8('0x24','EOVX') with abc(a,b) */
            $pat   = "/(" . $func . ")\(\'(0x[a-z0-9]+)\',\s?\'(.*?)\'\)/"; //better
            if (preg_match_all($pat, $h, $p)) {
                for ($z = 0; $z < count($p[0]); $z++) {
                    $h = str_replace($p[0][$z], abc($c0[hexdec($p[2][$z])], $p[3][$z]), $h);
                }
            }
            /* search for second array var _0x13e4=[xcxcxc,xcxc,xcxcx ...] */
            if (preg_match("/(var\s+(_0x[a-z0-9]+))\=\[([a-zA-Z0-9\=\+\/]+\,?)+\]/ms", $h, $m)) {
                $php_code = str_replace(",", "','", $m[0]);
                $php_code = str_replace("[", "['", $php_code);
                $php_code = str_replace("]", "']", $php_code);
                $php_code = str_replace($m[1], "\$c1", $php_code) . ";";
                /* get second array $c1 and rotate */
                eval($php_code);
                $pat = "/\(" . $m[2] . "\,(0x[a-z0-9]+)/ms";
                if (preg_match($pat, $h, $n)) {
                    $x = hexdec($n[1]);
                    for ($k = 0; $k < $x; $k++) {
                        array_push($c1, array_shift($c1));
                    }
                }
                /* Variant 1 only decode $c1 and get r.splice.... may be a solution
                $out = "";
                for ($k = 0; $k < count($c1); $k++) {
                $out .= base64_decode($c1[$k]);
                }
                echo $out;
                */
                /* search and replace _0x3504(0x6) etc with second array $c1 */
                $pat = "/" . $func1 . "\((0x[0-9a-f]+)\)/ms";
                if (preg_match_all($pat, $h, $q)) {
                    for ($k = 0; $k < count($q[1]); $k++) {
                        $h = str_replace($q[0][$k], base64_decode($c1[hexdec($q[1][$k])]), $h);
                    }
                }
            }
            /* now $h contain  var _0x1d4745=r.splice ..... eval(_0x1d4745) */
            if (preg_match("/((\w)\.splice.*?)eval/ms", $h, $e)) {
                $let = $e[2];
                /* now is "r" - for future.... */
                $out = str_replace(";;", ";", $e[1]);
            } else {
                $out = "";
            }
        }
    }
    /* $out */
    $out=str_replace("(Math.round(","",$out);
    $out=str_replace("))","",$out);
    if (preg_match_all("/\(\"body\"\)\.data\(\"(\w\s*\d)\"\,(\d+)\)/", $out, $u)) {
        for ($k = 0; $k < count($u[0]); $k++) {
            $out = str_replace("$" . $u[0][$k] . ";", "", $out);
            $out = str_replace('$("body").data("' . $u[1][$k] . '")', $u[2][$k], $out);
        }
    }
    $out = str_replace('"', "", $out);
    /* now is like array_splice($r, 3, 1);$r[388&15]=array_splice($r,388>>(3+3), 1, $r[388&15])[0]; etc */
    $d   = str_replace("r.splice(", "array_splice(\$r,", $out);
    $d   = str_replace("r[", "\$r[", $d);

    if (preg_match("/(array\_splice(.*))\;/", $d, $f)) {
        $d = $f[0];
    }
    $r = str_split(strrev($a145));
    eval($d);
    $x    = implode($r);
    $link = str_replace($a145, $x, $link);
} else {
    $link = "";
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
} elseif (strpos($filelink,"vidcloud.co") !== false || strpos($filelink,"vidcloud.online") !== false) {
  //https://vidcloud.co/v/5bcb1e672dce2/Blindspot.S04E02.HDTV.x264-SVA.mkv.mp4
  $pattern = '/(?:\/\/|\.)((?:vidcloud\.co|vidcloud\.online|loadvid\.online))\/(?:embed|v)\/([0-9a-zA-Z]+)/';
  preg_match($pattern,$filelink,$m);
  $id=$m[2];
  $l="https://vidcloud.co/player?fid=".$id."&page=video";
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  */
  $h2=file_get_contents($l);   // ???? why ?????????
  $h2=str_replace("\\","",$h2);
  $h2=str_replace("\n","",$h2);
  $link=str_between($h2,'file":"','"');
} elseif (strpos($filelink,"vidcloud.icu") !== false) {
  $filelink=str_replace("streaming.php","load.php",$filelink);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $h2=str_between($h2,'sources:[',']');
  //echo $h2;
  $t1=explode("file: '",$h2);
  $t2=explode("'",$t1[count($t1)-1]);
  $link=$t2[0];
} elseif (strpos($filelink,"cdnfile.info") !== false) {
  //https://hls26xx.cdnfile.info/stream_new/e9e68d7f44b73e1738773a5e84175cb4/i-love-my-mum.mp4
  $link=$filelink;
} elseif (strpos($filelink,"streamango.") !== false || strpos($filelink,"fruithosts.") !== false) {
 //echo $filelink;
 $pattern = '/(?:\/\/|\.)(streamango\.(?:io|com)|(fruithosts\.net))\/(?:embed|f)\/([0-9a-zA-Z-_]+)/';
 preg_match($pattern,$filelink,$m);
 //print_r ($m);
 $id=$m[3];
 $filelink="https://streamango.com/embed/".$id;
 //echo $filelink;
function indexOf($hack,$pos) {
    $ret= strpos($hack,$pos);
    return ($ret === FALSE) ? -1 : $ret;
}
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
  //echo $h2;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $m))
    $srt=$m[1];
$t1=explode('video/mp4"',$h2);
$t2=explode("src:d('",$t1[1]);
$t3=explode("'",$t2[1]);
$a16=$t3[0];
$t4=explode(",",$t3[1]);
$t5=explode(")",$t4[1]);
$a17=(int) $t5[0];
$a86=0x0;
$a84=explode("|","4|6|5|0|7|3|2|1|8");

 for ($zz=0;$zz<count($a84);$zz++)
		{
		switch($a84[$a86++])
			{
			case'0':
            //$a89,$a90,$a91,$a92;
            $a92=0;
            $a89=0;
            $a91=0;
            $a92=0;
			continue;
			case'1':
     //echo $a94;
             //die();
             while ( $a94 < strlen($a16))
				{
				$a96 = explode("|","6|2|9|8|5|4|7|10|0|3|1");
				$a98=0;
                for ($yy=0;$yy<count($a96);$yy++)
					{
					switch($a96[$a98++])
						{
						case'0':
                         $a101=$a101.chr($a104);
						continue;
						case'1':
                         if($a92!=0x40)
							{
							$a101=$a101.chr($a110);
						}
						continue;
						case'2':
                         //a90=k[c2('0xb')](a16[c2('0xc')](a94++));
                         //$a90 = k[indexOf.a16.charAt(a94++)] ????????
                         $a90=indexOf($k,$a16[$a94++]);
						continue;
						case'3':
                         if ($a91!=0x40)
							{
							$a101=$a101.chr($a119);
						}
						continue;
						case'4':
                          //a119=a18[c2('0xe')](a18[c2('0xf')](a18[c2('0x10')](a90,0xf),0x4),a18[c2('0x11')](a91,0x2));
                          //a119 = a45|a46   a50<<a51  a55&a56   a60>>a61  a50<<a51=a90&0xf << 0x4
                          //a119 = (a90&0xf << 0x4)|(a91>>0x02) ceva = x1 << a90&0xf
                          $a119 = (($a90&0xf) << 0x4)|($a91>>0x02);
						continue;
						case'5':
                          //a104=a18[c2('0x12')](a18[c2('0xf')](a89,0x2),a18[c2('0x11')](a90,0x4));
                          $a104 = ($a89<<0x2)|($a90>>0x4);
						continue;
						case'6':
                          //a89=k[c2('0xb')](a16['charAt'](a94++));
                          //k[indexOf.a16.charAt(a94++)]
                          $a89=indexOf($k,$a16[$a94++]);
                          //echo $a89."\n";
                          //die();
						continue;
						case'7':
                          //a110=a18[c2('0x13')](a18[c2('0x14')](a91,0x3),0x6)|a92;
                          //a91&0x3<<0x6|a92
                          $a110=(($a91&0x3)<<0x6)|$a92;
						continue;
						case'8':
                          //a92=k['indexOf'](a16['charAt'](a94++));
                          $a92=indexof($k,$a16[$a94++]);
						continue;
						case'9':
                          //a91=k['indexOf'](a16[c2('0xc')](a94++));
                          $a91=indexof($k,$a16[$a94++]);
						continue;
						case'10':
                          //a104=a18[c2('0x15')](a104,a17); a104 = a104^a17
                          $a104 = $a104^$a17;
						continue;
					}
					//break
				}
			}
			continue;
			case'2':
              //a16=a16[c2('0x16')](/[^A-Za-z0-9\+\/\=]/g,''); replace
              $a16=preg_replace("/[^A-Za-z0-9\+\/\=]/",'',$a16);
			  continue;
			case'3':
              //k=k[c2('0x4')]('')[c2('0x17')]()['join'](''); split reverse
              $k="=/+9876543210zyxwvutsrqponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFEDCBA";
			continue;
			case'4':
              $k="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			  continue;
			case'5':
              //$a104,$a119,$a110;
              $a104=0;
              $a119=0;
              $a110=0;
			  continue;
			case'6':
              $a101='';
			  continue;
			case'7':
              $a94=0x0;
			  continue;
			case'8':
              $dec = $a101;
			  continue;
		}
		//break
	}
	$link=$dec;
	if ($link) {
	if (strpos($link,"http") === false) $link="https:".$link;
	}
  /*
  if ($flash =="mp") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //                                                                                                                                                                                                                                                                                                                                                                                                                                                                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h1 = curl_exec($ch);
  curl_close($ch);
  //echo $h1;

  //Location: http://217.20.153.80/?sig=02ca7b380f40ffbed19cfb057c06ac49382f5d00&ct=0&urls=217.20.145.39%3B217.20.157.204&expires=1442125983098&clientType=1&id=59340163817&type=2
  $t1=explode("Location:",$h1);
  $t2=explode("\n",$t1[1]);
  $link=trim($t2[0]);
  if ($link && strpos($link,".mp4")=== false) $link=$link.".mp4";
  //if ($link) $link=$link."&type=.mp4";
  }
  */
} elseif (strpos($filelink,"vidlox") !== false) {
//echo $filelink;
//die();
$filelink=str_replace("https","http",$filelink);
$filelink=str_replace("http","https",$filelink);
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
  //echo $h2;
  //die();
  preg_match('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h2, $m);
  $link=$m[1];
  $link=str_replace("https","http",$link);
  //echo $link;
  //die();
  //http://c15.vidlox.tv/oudvgagbkvtk2yixv62oedz6lifolllekf5cskapxzby5shmchek7hgzqqba/v.mp4
  //http://c15.vidlox.tv/oudvgagbkvtk2yixv62oedz6lifolllekf5cskapxzby5qyw4qhvikfl5fda/v.mp4
  //http://c23.vidlox.tv/oudvh2n7kvtk2yixv62oeabudx46hnwvzeo34uafy3nmt5boba24dyyc5x3a/v.mp4
  //http://c19.vidlox.tv/oudvhawdkvtk2yixv6z6eadrl23qaxpjbsigre3gvplsmshmchek7hgzqqba/v.mp4
  //http://c19.vidlox.tv/oudvhawdkvtk2yixv6z6eadrl23qaxpjbsigre3gvplsmjtskyk2gwi7suaa/v.mp4
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h2, $m))
  $srt=$m[1];
  //die();
} elseif (strpos($filelink,"fastplay.cc") !== false || strpos($filelink,"fastplay.to") !== false) {
//echo $filelink;
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
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  //echo $out;
  //echo $out;
  //die();
  $out .=$h2;
  //echo $out;
  if (preg_match_all('/[file:"]([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $out, $m)) {
  $link=$m[1][count($m[1]) -1];
  if (preg_match('/([http|https]?[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $out, $s))
  $srt=$s[1]; if (strpos($srt,"http") === false) $srt="https://fastplay.to".$srt;
  } else
    $link="";
} elseif (strpos($filelink,"cloudvideo") !== false) {
  //https://cloudvideo.tv/wpdo30vv84c0
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
  preg_match('/((http|https)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $h2, $m);
  $link=$m[1];
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
} elseif (strpos($filelink,"vidoza.net") !== false) {
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
} elseif (strpos($filelink,"hqq.tv") !== false || strpos($filelink,"hqq.watch") !== false || strpos($filelink,"waaw.tv") !== false || strpos($filelink,"waaw1.tv") !== false  || strpos($filelink,"hindipix.in") !== false) {
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

preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$x,$m);

$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m);
$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

$t1=explode(";;",$h2);
$h2=$t1[1];
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m);
$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
//echo $h2;
//die();
$y=$x." ".$h2;
//echo $h;
//echo $h;
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$y,$m);
//print_r ($m);

$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
//echo $h2;
//die();
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m);
$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

$t1=explode(";;",$h2);
$h2=$t1[1];
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h2,$m);
$h2= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
return $h2;
}

  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //if ($flash== "mpc") $user_agent="VLC";
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
//echo $l;
//$l="http://hqq.watch/player/embed_player.php?vid=UXZUYWRacWFnTmZ2RUswa2M3bkh6Zz09&autoplay=no";

$cookie=$base_cookie."hqq.txt";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://hqq.tv");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      //curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);

preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
$t1=explode(";;",$h);
$h=$t1[1];
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

$l="http://hqq.tv/player/ip.php?type=json";
$x=file_get_contents($l);
//echo $x;
//die();
$iss=str_between($x,'ip":"','"');
$vid=str_between($h,"videokeyorig='","'");
$at=str_between($h,"attoken='","'");
$http_referer=str_between($h,"server_referer='","'");
//echo $vid."<BR>".$at."<BR>".$http_referer."<BR>";
//die();
$l="http://hqq.tv/sec/player/embed_player_9331445831509874.php?vid=".$vid."&need_captcha=1&iss=".$iss."&vid=".$vid."&at=".$at."&autoplayed=yes&referer=on&http_referer=".$http_referer."&pass=&embed_from=&need_captcha=0&hash_from=&secured=0&token=03";
$l_ref=$l;
//echo $l;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://hqq.watch");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_HEADER,1);
      //curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);

$h=urldecode($h);
//preg_match("/var link_m3u8 \= \"(.*?)\"/",$h,$m);
//file_put_contents("result.txt",$h);
//echo $h;
//die();

//$t1=explode('</head>',$h);
//$h=$t1[1];
//$h=file_get_contents("result.txt");
//echo $h;
if (preg_match("/get_md5/",$h)){
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m)) {
  $srt=$m[1];
  $srt=urldecode(str_replace("https","http",$srt));
  }
$vid=str_between($h,'videokey = "','"');
preg_match_all("/eval\(function\(w\,i\,s\,e\)(.*?)\<\/script/ms",$h,$r);
//print_r ($r);
//die();
////////////////////////////////////////////////////////////////////////////////

$e=decode_wise($r[1][0]);
preg_match('/at\s*=\s*"([^"]*?)"/ms',$e,$m);
$at=$m[1];
/////////////////////////////////////////////////////////////////////////////////
$e=decode_wise($r[1][1]);
preg_match("/server_2=\"\s*\+*encodeURIComponent\(([^\)]+)/",$h,$m);
$pat='/'.$m[1].'\s*=\s*"([^"]*?)"/ms';
//echo $pat;
preg_match($pat,$e,$m);
$vid_server=$m[1];
//$vid_server=trim(str_between($h,'server_1:',','));
//link_1="+encodeURIComponent(
preg_match("/link_1=\"\s*\+*encodeURIComponent\(([^\)]+)/ms",$h,$m);
$pat='/'.$m[1].'\s*=\s*"([^"]*?)"/ms';
preg_match($pat,$e,$m);
$vid_link=$m[1];
//$vid_link=trim(str_between($h,'link_1:',','));
//echo $vid_link;

//echo $r;
//echo urldecode(urldecode($h));
$l="https://hqq.tv/player/get_md5.php?at=".$at."&adb=0%2F&b=1&link_1=".$vid_link."&server_2=".$vid_server."&vid=".$vid;
$l="http://hqq.tv/player/get_md5.php?at=".$at."&adb=0%2F&b=1&link_1=".$vid_link."&server_2=".$vid_server."&vid=".$vid;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Accept: */*',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest'
    ));
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:42.0) Gecko/20100101 Firefox/42.0');
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_REFERER, $l_ref);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $l;
  //echo $h;
  //die();
  $x=json_decode($h,1);
$file=str_between($h,'file":"','"');
$file= $x["obf_link"];
//echo $file;
$y=decodeUN($file);
//echo $y;
if (strpos($y,"http") === false && $y) $y="https:".$y;
//$link=$y;
if ($y)
$link=$y.".mp4.m3u8";
else
$link="";
} else {
 $link="";
}
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

} elseif (strpos($filelink,"raptu.com") !==false || strpos($filelink,"bitporno.com") !==false) {
  //https://www.raptu.com/embed/qhqHATdD
  //https://www.bitporno.com/embed/qhqHATdD
  //echo $filelink;
  //die();
      preg_match("/(e\/|embed\/|v=)(\w+)/",$filelink,$m);
      $id=$m[2];
      $filelink="https://www.raptu.com/embed/".$id;
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
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;

      preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m);
      //print_r ($m);
      $n=count($m[1]);
      $link=$m[1][$n-1];
      if (preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
      $srt=$m[0][0];
      //die();
} elseif (strpos($filelink,"rapidvideo.com") !==false) {
  //echo $filelink;
//die();
//https://www.raptu.com/embed/GgJwjRXD
//https://www.rapidvideo.com/?v=GgJwjRXD
//https://www.rapidvideo.com/embed/21ocj7atN
//$filelink="https://www.rapidvideo.com/?v=21ocj7atN";
      preg_match("/(e\/|embed\/|v=|e\/)(\w+)/",$filelink,$m);
      $id=$m[2];
      $filelink="https://www.rapidvideo.com/?v=".$id;
      //$filelink="https://www.raptu.com/?v=".$id;
      $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      //die();
      /*
      $t1=explode("jwplayer.key",$h);
      $t2=explode('loadsrt.php',$t1[1]);
      $t3=explode('"',$t2[1]);
      $srt="https://www.rapidvideo.com/loadsrt.php".str_replace("\\","",$t3[0]);
      //echo $srt;
      $t4=explode("sources",$t1[1]);
      $t5=explode('file":"',$t4[1]);
      $t6=explode('"',$t5[1]);
      $link= str_replace("\\","",$t6[0]);
      */
      $t1=explode("jwplayer.key",$h);
      $t2=explode("</script",$t1[1]);
      $t3=str_replace("\/","/",$t2[0]);
      //echo $t3;
      //echo $h;
      preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m);

      //print_r ($m);
      //die();
      $n=count($m[1]);
      $link=$m[1][$n-1];
      //$link=str_replace("https","http",$link);
      if (preg_match_all('/([\.\d\w\=\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
      $srt=$m[0][0];
      if (strpos($srt,"http") === false) $srt="https://www.rapidvideo.com".$srt;
      //echo $srt;
      //if ($srt && strpos($srt,"http") === false) $srt="https://www.raptu.com/".$srt;
      //$link=str_replace("https","http",$link);
} elseif (strpos($filelink,"openload") !==false || strpos($filelink,"oload") !==false) {
//include ("jj.php");
//echo 2 - -1;
//echo $filelink;
//die();
function decode_code($code){
    return preg_replace_callback(
        "@\\\(x)?([0-9a-f]{2,3})@",
        function($m){
            return chr($m[1]?hexdec($m[2]):octdec($m[2]));
        },
        $code
    );
}
function calc($equation)
{
    // Remove whitespaces
    $equation = preg_replace('/\s+/', '', $equation);
    $equation=str_replace("--","+",$equation);
    $equation=str_replace("-+","-",$equation);
    $equation=str_replace("+-","-",$equation);
    $equation=str_replace("++","+",$equation);
    //echo "$equation\n";

    $number = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|p)'; // What is a number

    $functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg
|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
    $operators = '[\/*\^\+-,]'; // Allowed math operators
    $regexp = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

    if (preg_match($regexp, $equation))
    {
        $equation = preg_replace('!pi|p!', 'pi()', $equation); // Replace pi with pi function
        //echo "$equation\n";
        eval('$result = '.$equation.';');
    }
    else
    {
        $result = false;
    }
    return $result;
}
function base10toN($num, $n){
    $num_rep = array(
               '10' => 'a',
               '11' => 'b',
               '12' => 'c',
               '13' => 'd',
               '14' => 'e',
               '15' => 'f',
               '16' => 'g',
               '17' => 'h',
               '18' => 'i',
               '19' => 'j',
               '20' => 'k',
               '21' => 'l',
               '22' => 'm',
               '23' => 'n',
               '24' => 'o',
               '25' => 'p',
               '26' => 'q',
               '27' => 'r',
               '28' => 's',
               '29' => 't',
               '30' => 'u',
               '31' => 'v',
               '32' => 'w',
               '33' => 'x',
               '34' => 'y',
               '35' => 'z');
    $new_num_string = '';
    $current = $num;
    while ($current != 0) {
        $remainder = $current % $n ;
        //echo $remainder."<BR>";
        if ($remainder < 36 && $remainder > 9)
            $remainder_string = $num_rep[$remainder];
        elseif ($remainder >= 36)
            $remainder_string = '(' .$remainder. ')';
        else
            $remainder_string = $remainder;
        $new_num_string = $remainder_string . $new_num_string;
        $current = (int)($current / $n);
        //echo $current;
    }
    return $new_num_string;
}
//echo base10toN(20128311,30);
//die();
function dec_text($in) {
$in = str_replace("%EF%BE%9F%D0%94%EF%BE%9F%29%5B%EF%BE%9F%CE%B5%EF%BE%9F%5D%2B%28o%EF%BE%9F%EF%BD%B0%EF%BE%9Fo%29%2B+%28%28c%5E_%5Eo%29-%28c%5E_%5Eo%29%29%2B+%28-%7E0%29%2B+%28%EF%BE%9F%D0%94%EF%BE%9F%29+%5B%27c%27%5D%2B+%28-%7E-%7E1%29%2B","",$in);
       $h = str_replace("(\xef\xbe\x9f\xd0\x94\xef\xbe\x9f)[\xef\xbe\x9f\xce\xb5\xef\xbe\x9f]+(o\xef\xbe\x9f\xef\xbd\xb0\xef\xbe\x9fo)+ ((c^_^o)-(c^_^o))+ (-~0)+ (\xef\xbe\x9f\xd0\x94\xef\xbe\x9f) ['c']+ (-~-~1)+","",$h);
$s = str_replace("%28%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29+%2B+%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29+%2B+%28%EF%BE%9F%CE%98%EF%BE%9F%29%29", "9",$in);
$s = str_replace("%28%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29+%2B+%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29%29", "8",$s);
$s = str_replace("%28%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29+%2B+%28o%5E_%5Eo%29%29", "7",$s);
$s = str_replace("%28%28o%5E_%5Eo%29+%2B%28o%5E_%5Eo%29%29", "6",$s);
$s = str_replace("%28%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29+%2B+%28%EF%BE%9F%CE%98%EF%BE%9F%29%29", "5",$s);
$s = str_replace("%28%EF%BE%9F%EF%BD%B0%EF%BE%9F%29", "4",$s);
$s = str_replace("%28%28o%5E_%5Eo%29+-+%28%EF%BE%9F%CE%98%EF%BE%9F%29%29", "2",$s);

$s = str_replace("%28o%5E_%5Eo%29", "3",$s);
$s = str_replace("%28%EF%BE%9F%CE%98%EF%BE%9F%29", "1",$s);
$s = str_replace("%28%2B%21%2B%5B%5D%29", "1",$s);
$s = str_replace("%28c%5E_%5Eo%29", "0",$s);
$s = str_replace("%280%2B0%29", "0",$s);
$s = str_replace("%28%EF%BE%9F%D0%94%EF%BE%9F%29%5B%EF%BE%9F%CE%B5%EF%BE%9F%5D", "\\",$s);
$s = str_replace("%283+%2B3+%2B0%29", "6",$s);
$s = str_replace("%283+-+1+%2B0%29", "2",$s);
$s = str_replace("%28%21%2B%5B%5D%2B%21%2B%5B%5D%29", "2",$s);
$s = str_replace("%28-%7E-%7E2%29", "4",$s);
$s = str_replace("%28-%7E-%7E1%29", "3",$s);

$s=str_replace("%28-%7E0%29", "1",$s);
$s=str_replace("%28-%7E1%29", "2",$s);
$s=str_replace("%28-%7E3%29", "4",$s);
$s=str_replace("%280-0%29", "0",$s);

$s= urldecode($s);

$s=str_replace("+","",$s);
$s=str_replace(" ","",$s);
$s=str_replace("\\/","/",$s);
//echo $s;

preg_match_all("/(\d{2,3})/",$s,$m);
//print_r ($m[0]);
$n=count($m[0]);
//echo $s;
$out1="";
for ($k=0; $k<$n; $k++) {
$out1=$out1.chr(intval($m[0][$k],8));
}
/*
//echo $out1;
//if (strpos($out1,"toString") !== false) {
preg_match('/toString\\(a\\+(\\d+)/',$out1,$m);
$base=$m[1];
preg_match_all('/(\\(\\d[^)]+\\))/',$out1,$m);
//print_r ($m);
preg_match_all('/(\\d+),(\\d+)/',$out1,$m1);
//print_r ($m1);
//die();
$p=count($m[0]);
for ($k=0; $k<$p;$k++) {
  $base1=$base + $m1[1][$k];
  $rep = base10toN($m1[2][$k],$base1);
  $out1=str_replace($m[0][$k],$rep,$out1);
}
$out1=str_replace("+","",$out1);
$out1=str_replace('"',"",$out1);
//}
return $out1;
*/
return $out1;
}
//https://oload.stream/f/xFIHqcrLRnM
//https://verystream.com/e/9TMLcpuCbF1
   $filelink=str_replace("openload.co/f/","openload.co/embed/",$filelink);

$t1=explode("/",$filelink);
$filelink="https://openload.co/embed/".$t1[4];
   //if (substr($filelink, -1) == "/") $filelink=$filelink."v.mp4";
   //echo $filelink;
   //https://openload.co/stream/1ZP8bc17IQw~1445495360~82.210.0.0~3qH1PNZ6?mime=true
   //https://openload.co/stream/1ZP8bc17IQw~1445410166~82.210.0.0~3qH1PNZ6?mime=true
/*
//$h=str_between($h1,"<video","</script");
if (strpos($h,"videocontainer") === false) {
   $filelink=str_replace("openload.co/embed/","openload.co/f/",$filelink);
   //$filelink="https://openload.co/f/cCTPkXAbNpA/Columbo_-_S11E01.720p.BluRay.x264-HDCLUB.mp4";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $h1 = curl_exec($ch);
      curl_close($ch);
   $t1=explode('kind="captions"',$h1);
   $t2=explode('src="',$t1[1]);
   $t3=explode('"',$t2[1]);
   if ($t3[0]) {
    if (strpos($t3[0],"openload") === false)
     $srt="https://openload.co".$t3[0];
    else
     $srt=$t3[0];
   }
$h=str_between($h1,"Click to start Download","</script");
//$h=str_between($h1,'<script type="text/javascript">(',"</script");
//echo $out;
//die();
$out=dec_text($h);

if (strpos($out,"toString") !== false) {
preg_match('/toString\\(a\\+(\\d+)/',$out,$m);
$base=$m[1];
preg_match_all('/(\\(\\d[^)]+\\))/',$out,$m);
//print_r ($m);
preg_match_all('/(\\d+),(\\d+)/',$out,$m1);
//print_r ($m1);
//die();
$p=count($m[0]);
for ($k=0; $k<$p;$k++) {
  $base1=$base + $m1[1][$k];
  $rep = base10toN($m1[2][$k],$base1);
  $out=str_replace($m[0][$k],$rep,$out);
}
$out=str_replace("+","",$out);
$out=str_replace('"',"",$out);
//$out=str_replace("0","",$out);
preg_match('(http[^\\}]+)',$out,$l);
$link = $l[0];
} else {
  $link=str_between($out,"vr='","'");
}
//echo $link;
}
*/
// de analizat https://openload.co/embed/IE0mwWpRuo4/
//for ($z=1;$z<11;$z++) {
//echo $filelink;
$t1=explode("/",$filelink);
$filelink="https://openload.co/embed/".$t1[4];
//$filelink="https://oload.stream/embed/".$t1[4];
//$filelink="https://verystream.com/e/PsP9iGR9N3y";
//echo $filelink;
//die();
//$filelink="http://openload.co/embed/xJeJBuulA9w";
$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://openload.co/");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
//echo $h1;
//die();
/*
   $t1=explode('kind="captions',$h1);
   //if (sizeof($t1[1] >1)) {
   $t2=explode('src="',$t1[1]);

   $t3=explode('"',$t2[1]);
   */
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h1, $m))
      $srt=$m[1];
   if ($srt) {
    if (strpos($srt,"http") === false)
     $srt="https://openload.co".$srt;
   }
   //}
include ("ol.php");
//echo $srt;
//die();
//split("^"),0,{}))
function openload($c,$z) {
return chr(($c<="Z" ? 90:122) >= ($c=ord($c)+$z) ? $c : $c-26);
}
$pattern = '/(embed|f)\/([0-9a-zA-Z-_]+)/';
preg_match($pattern,$filelink,$m);
$id=$m[2];
$t1=explode('p id="',$h1);
$t2=explode(">",$t1[1]);
$t3=explode("<",$t2[1]);
$enc_t=$t3[0];
if (preg_match("/[a-z0-9]{40,}/",$h1,$r))
   $enc_t=$r[0];
//$t1=explode('id="videolink">',$h1);
//$t2=explode('<',$t1[1]);
//$enc=$t2[0];
$x=decode_code($h1);
//$x=$h1;
//echo $x;
$x=str_replace(";",";"."\n",$x);
preg_match_all("/case\'3\'(.*)/",$x,$m);
//print_r ($m);
//case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,(parseInt('60305005205',8)-719+0x4)/(11-0x8)),d0)
$t1=explode("parseInt('",$m[0][1]);
$t8=explode("0x4",$t1[1]);
$t9=explode(')',$t8[1]);
$ch7=$t9[0];
$t2=explode("'",$t1[1]);
$t4=explode("-",$t1[1]);
$t5=explode("+",$t4[1]);
$t6=explode("/(",$t1[1]);
$t7=explode("-",$t6[1]);
$ch1=$t2[0];
$ch4= $t5[0];
$ch5=$t7[0];
//echo $ch1;
preg_match_all("/case\'11\'(.*)/",$x,$m);
$t1=explode("parseInt('",$m[0][0]);
$t2=explode("'",$t1[1]);
$ch2=$t2[0];
$t1=explode(")",$m[0][0]);
$t2=explode(";",$t1[1]);
$ch6=trim($t2[0]);
$ch1=str_replace("0x","",$ch1);
$ch2=str_replace("0x","",$ch2);
preg_match_all("/case\'4\'(.*)/",$x,$m);
$t1=explode("]",$m[0][2]);
preg_match("/(\d+)((\-|\+)(\d+))/",$t1[1],$m);
$ch3=$m[2];
$dec=ol($enc_t,$ch1,$ch2,$ch3,$ch4,$ch5,$ch6,$ch7);
if (strpos($dec,$id) === false) {
$l="https://api.openload.co/pair";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h2 = curl_exec($ch);
      curl_close($ch);
$l="https://api.openload.co/1/streaming/get?file=".$id;
//echo $l;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h2 = curl_exec($ch);
      curl_close($ch);
//echo $h2;
//die();
$t1=explode('url":"',$h2);
$t2=explode("?",$t1[1]);
if ($t1) $link=str_replace("\\","",$t2[0]).".mp4";
} else {
  $link="https://openload.co/stream/".$dec."?mime=true";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://openload.co/");
      curl_setopt($ch, CURLOPT_NOBODY,1);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("?",$t1[1]);
      $link=urldecode(trim($t2[0]));
      $movie_file=substr(strrchr(urldecode($link), "/"), 1);
      $movie_file1=substr($movie_file, 0, -4);
      $movie_file2 = preg_replace('/[^A-Za-z0-9_]/','_',$movie_file1);
      $link=str_replace($movie_file1,$movie_file2,$link);
      $link=str_replace("https","http",$link).".mp4";
  //echo $link;
}
//die();
//echo $link;
//die();
/*
preg_match_all("/}\s*\(\s*(.*?)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*\((.*?)\).split\((.*?)\)/",$h1,$m);
//preg_match("/}\('(.*)', *(\d+), *(\d+), *'(.*?)'\.split\('\|\)/",$h1,$m);
//print_r ($m);
$pattern = "/=\"([^\"]+).*}\s*\((\d+)\)/";
preg_match($pattern,$m[0][1],$a);
//print_r ($a);
$o="";
for ($k=0;$k<strlen($a[1]);$k++) {
  if (preg_match("/[a-zA-Z]/",$a[1][$k]))
     $o .= openload($a[1][$k],$a[2]);
  else
     $o .= $a[1][$k];
}
$o=urldecode($o);
$rep="j^_^__^___";
$r=explode("'",$m[4][1]);
$rep=$r[1];
$t1=explode("^",$rep);
$k=count($t1);
//$out=str_replace("+","",$out);
for ($i=0;$i<$k;$i++) {
  $o=str_replace($i,$t1[$i],$o);
}
$out = jjdecode($o);
if (strpos($out,"y.length") === false)
  $h_index=str_between($out,'var x = $("#','"');
else
  $h_index=str_between($out,'var y = $("#','"');
//echo $h_index;
if (!$h_index) {
$sPattern = '/<script type="text\/javascript">([a-z]=.+?\(\)\)\(\);)/';
preg_match($sPattern,$h1,$m);
$j=str_replace('<script type="text/javascript">',"",$m[0]);
$out = jjdecode($j);
if (strpos($out,"y.length") === false)
  $h_index=str_between($out,'var x = $("#','"');
else
  $h_index=str_between($out,'var y = $("#','"');
}
if (!$h_index) {
$t1=explode('<script type="text/javascript">',$h1);
$n=count($t1);
$y=explode("</script",$t1[$n - 1]);

$out=dec_text(urlencode($y[0]));

if (strpos($out,"y.length") === false)
  $h_index=str_between($out,'var x = $("#','"');
else
  $h_index=str_between($out,'var y = $("#','"');
}

if (!$h_index) $index=0;
//echo $out;
preg_match_all("/function\s*(\w+)/",$out,$m);
//print_r ($m);
$t1=explode("function ".$m[1][0],$out);
$t3=explode("return",$t1[1]);
$t2=explode(";",$t3[1]);
$x1=calc($t2[0]);
$out=str_replace($m[1][0]."()",$x1,$out);

$t1=explode("function ".$m[1][3],$out);
$t3=explode("return",$t1[1]);
$t2=explode(";",$t3[1]);
$x1=calc($t2[0]);
$out=str_replace($m[1][3]."()",$x1,$out);

$t1=explode("function ".$m[1][1],$out);
$t3=explode("return",$t1[1]);
$t2=explode(";",$t3[1]);
$x1=calc($t2[0]);
$out=str_replace($m[1][1]."()",$x1,$out);

$t1=explode("function ".$m[1][2],$out);
$t3=explode("return",$t1[1]);
$t2=explode(";",$t3[1]);
$x1=calc($t2[0]);
$out=str_replace($m[1][2]."()",$x1,$out);

//echo $out;

$t1=explode('charCodeAt(0) +',$out);
$t2=explode(")",$t1[1]);
$index=trim($t2[0]);
$t1=explode("length -",$out);
$t2=explode(")",$t1[1]);
$index1=trim($t2[0]);
$out="";
if (!$h_index) {
$x1=explode('id="hiddenurl">',$h1);
$x2=explode("<",$x1[1]);
$hiddenurl1=$x2[0];
if ($hiddenurl1) {
$x3=explode("<span",$x1[1]);
$x4=explode(">",$x3[1]);
$x5=explode("<",$x4[1]);
$hiddenurl2=$x5[0];
} else {
$x1=explode('<span id=',$h1);
$x3=explode(">",$x1[1]);
$x2=explode("<",$x3[1]);
$hiddenurl1=$x2[0];
$x3=explode(">",$x1[2]);
$x2=explode("<",$x3[1]);
$hiddenurl2=$x2[0];
}

//echo $hiddenurl1."\n".$hiddenurl2;
if (substr($hiddenurl1, 0, -2) == substr($hiddenurl2, 0, -2))
   $hiddenurl = $hiddenurl2;
else
   $hiddenurl = $hiddenurl1;
//$hiddenurl = str_replace("&amp;","&",$hiddenurl); // ???????
$hiddenurl = $hiddenurl1;
} else {
$x1=explode('<span id="'.$h_index,$h1);
$x3=explode(">",$x1[1]);
$x2=explode("<",$x3[1]);
$hiddenurl=$x2[0];
}
$hiddenurl = htmlspecialchars_decode($hiddenurl);

$c=strlen($hiddenurl);
for ($k=0;$k<$c;$k++) {
  $j=ord($hiddenurl[$k]);
  if (($j>=33)&&($j<=126))
    $out=$out.chr(33+(($j+14)%94));
  else
    $out=$out.chr($j);
}
$part1=substr($out,0,-1*$index1);
$part2=substr($out,-1*$index1+1);
//echo "\n".$index1."\n".$index."\n";
if ($index1==1) $part2="";
$part3=chr(ord(substr($out, -1*$index1, 1)) + $index);
//echo "\n".$part1."\n".$part2."\n".$part3."\n";
$out=$part1.$part3.$part2;
$link="https://openload.co/stream/".$out."?mime=true";
$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";


      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://openload.co/");
      curl_setopt($ch, CURLOPT_NOBODY,true);
      curl_setopt($ch, CURLOPT_HEADER,1);
      $ret = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$ret);
      $t2=explode("?",$t1[1]);
      $link=trim($t2[0]);
      //if (strpos($link,"Komp+1.mp4") === false) break;
//}

      $link=str_replace("https","http",$link).".mp4";
//echo $link;
//die();
      //$link=$link."?mime=true";
/*
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, $filelink);
      curl_setopt($ch, CURLOPT_NOBOBY,1);
      curl_setopt($ch, CURLOPT_HEADER,1);
      $ret = curl_exec($ch);
      curl_close($ch);

      //echo $ret;

      echo $ret;
      echo $link;
      die();
*/
   /*
   preg_match('/openload\.co\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $filelink, $match);
   $file = $match[2];
   //$key="0cPTBgLt";
   //$login="34168b2bede5f7d6";
   $key="UebmYlZN";
   $login="de2a2a3fe31fdb89";
   $f=$base_cookie."ticket.dat";
   $captcha="";
   $invalid_t=false;
   if (file_exists($f)) {
     $t_f=file_get_contents($f);
     if (strpos($t_f,$file) === false) $invalid_t=true;
   }
   if (!file_exists($f) || $invalid_t) {
   $ticket="https://api.openload.co/1/file/dlticket?file=".$file."&login=".$login."&key=".$key;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $ticket);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      $ret = curl_exec($ch);
      curl_close($ch);
  $t=str_between($ret,'ticket":"','"');
  //echo $ret;
  //die();
  } else {
    $t=file_get_contents($f);
    $captcha=file_get_contents($base_cookie."captcha.dat");
  }

  $dl="https://api.openload.co/1/file/dl?file=".$file."&ticket=".$t."&captcha_response=".$captcha;
  //die();
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $dl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      $ret = curl_exec($ch);
      curl_close($ch);
      //echo $ret;
      //die();
  $link=str_between($ret,'url":"','"');
  $link=str_replace("\/","/",$link);
  */
} elseif (strpos($filelink,"verystream")) {
  $t1=explode("/",$filelink);
  $filelink="https://verystream.com/e/".$t1[4];
  $ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://verystream.com");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h1, $m))
         $srt=$m[1];
      if ($srt) {
         if (strpos($srt,"http") === false)
         $srt="https://verystream.com".$srt;
      }
      $t1=explode('id="videolink">',$h1);
      if (isset($t1[1])) {
      $t2=explode('<',$t1[1]);
      $id=$t2[0];
      $l="https://verystream.com/gettoken/".$id."?mime=true";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://verystream.com");
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_NOBODY,1);
      $h2 = curl_exec($ch);
      curl_close($ch);
      $t1=explode("Location:",$h2);
      $t2=explode("?",$t1[1]);
      $link=urldecode(trim($t2[0]));
      $movie_file=substr(strrchr(urldecode($link), "/"), 1);
      $movie_file1=substr($movie_file, 0, -4);
      $movie_file2 = preg_replace('/[^A-Za-z0-9_]/','_',$movie_file1);
      $link=str_replace($movie_file1,$movie_file2,$link);
      $link=str_replace("https","http",$link).".mp4";
      } else {
        $link="";
      }
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
  //if ($link) $link=$link."&video/video.mp4";
} elseif (strpos($filelink,"entervideos.com") !==false) {
   //http://entervideos.com/embed-luex1rbugf7m-590x360.html
   //http://entervideos.com/vidembed-wlsuh0mcoe0d
   if (strpos($filelink,"vidembed") !== false) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, $filelink);
   curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $t1=explode("Location:",$h);
   $t2=explode("\n",$t1[1]);
   $link=trim($t2[0]);
   } else {
   $h=file_get_contents($filelink);
   $link=str_between($h,'file: "','"');
   }
} elseif (strpos($filelink,"entervideo.net") !==false) {
   //http://entervideo.net/watch/4752dfc86f5df23
   $h=file_get_contents($filelink);
   $link=str_between($h,'source src="','"');
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h, $m))
      $srt=$m[1];
} elseif (strpos($filelink,"primatv.ro/seriale/trasnitii") !==false) {
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('video/mp4" src="',$h);
  $t2=explode('"',$t1[1]);
  $link="https://www.primatv.ro".$t2[0];
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
   $link1=explode("?",$link);
   //rtmp://50.7.120.138:1935/play/mp4:/d4/d4901939832534f418c027726cb2a9a5
   //if (strpos($filelink,"streamango") === false)
     $movie_file=substr(strrchr($link1[0], "/"), 1);
   //else {
      //$movie_file=substr(strrchr($link1[0], "/"), 1);
      //$movie_file=str_replace("&type=.mp4","",$movie_file);
   //}
   if (preg_match("/m3u8/",$movie_file))
   $srt_name = substr($movie_file, 0, -4).".srt";
   else if (preg_match("/mp4|flv/",$movie_file))
   $srt_name = substr($movie_file, 0, -3).".srt";
   else
   $srt_name= $movie_file.".srt";
   //if (strpos($filelink,"streamango") !== false) $srt_name=urldecode($srt_name);
   //echo $srt_name."<BR>".$link;
   //$srt_name="Madam.Secretary.-.S01.E01.-.Pilot.(720p.HDTV).srt";
   //$srt_name = urldecode($srt_name);
   $srt_name = rawurldecode($srt_name);
   if (strpos($srt_name,".srt") === false)  $srt_name=$srt_name.".srt";
   $srt_name=str_replace("..srt",".srt",$srt_name);
   //if (preg_match("/mp4|flv|m3u8/",$link)) {
   $new_file = $base_sub.$srt_name;
   if (!file_exists($base_sub."sub_extern.srt")) {
   //echo $srt;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   //curl_setopt($ch,CURLOPT_REFERER,"http://roshare.info");
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
   } else {
    $h=file_get_contents($base_sub."sub_extern.srt");
   }
   if ($h) {
   //$h=mb_convert_encoding($h, 'UTF-8');

   //echo $enc;
   //die();
   $h=str_replace("\n","\r\n",$h);
   //file_put_contents($base_sub."default.srt",$h);
if ($link) {
 if (function_exists("mb_convert_encoding")) {
    if (mb_detect_encoding($h, 'UTF-8', true)== false) $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
    //$h=str_replace("",'"',$h);
    //$h=str_replace("",'"',$h);
    //se întoarce Khlyen pentru asta? 
    //"Ce rau ar putea face scornelile?"
//00:29:56,655 --> 00:30:00,053
//�ntrebarea mea este
//?se �ntoarce Khlyen pentru asta?
    /*
    $h = str_replace("ª","Ş",$h);
    $h = str_replace("º","ş",$h);
    $h = str_replace("Þ","Ţ",$h);
    $h = str_replace("þ","ţ",$h);
	$h = str_replace("ã","ă",$h);
	//$h = str_replace("Ã","Ă",$h);
    //$h=str_replace("Ã",

    $h = str_replace("Å£","ţ",$h);
    $h = str_replace("Å¢","Ţ",$h);
    $h = str_replace("Å","ş",$h);
	$h = str_replace("Ă®","î",$h);
	$h = str_replace("Ă¢","â",$h);
	$h = str_replace("Ă","Î",$h);
	//$h = str_replace("Ã","Â",$h);
	$h = str_replace("Ä","ă",$h);
	*/
} else {
    $h = str_replace("�","S",$h);
    $h = str_replace("�","s",$h);
    $h = str_replace("�","T",$h);
    $h = str_replace("�","t",$h);
    $h=str_replace("�","a",$h);
	$h=str_replace("�","a",$h);
	$h=str_replace("�","i",$h);
	$h=str_replace("�","A",$h);
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
   //echo $h1;
   //if ($flash=="mpc") $h = mb_convert_encoding($h, 'ISO-8859-1', 'UTF-8');;
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h);
   fclose($fh);
}
//}
}
$movie=$link;
if (strpos($movie,"http") === false) $movie="";
//$flash = "mp";
//echo $movie;
//if (strpos($filelink,"ok.ru") !== false) $flash="flash";
if ($flash== "mpc") {
//if(file_exists($base_sub.$srt_name)) echo "OK";
//die();
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$movie.'"';
  if (strpos($filelink,"ok1.ru") !== false || strpos($filelink,"raptu") !== false || strpos($filelink,"rapidvideo") !== false || strpos($filelink,"hqq.tv") !== false || strpos($filelink,"google") !== false || strpos($filelink,"blogspot") !== false) {
  $mpc=trim(file_get_contents($base_pass."vlc.txt"));
  $c = '"'.$mpc.'" --fullscreen --sub-language="ro,rum,ron" --sub-file="'.$base_sub.$srt_name.'" "'.$movie.'"';
  }
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
  //pastori_%C8%99i_m%C4%83celari.srt
  //D:/EasyPHP/data/localweb/mobile/scripts/subs/pastori_%C8%99i_m%C4%83celari.mp4.srt
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
$hed = "headers="."{'Cookie: approve=1'}";
if (!preg_match("/hqq\.|putload\.|thevideobee\.|flixtor\.|0123netflix|mangovideo/",$filelink)) // HW=1;SW=2;HW+=4
$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";b.decode_mode=1;end";
//$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";
//$c="intent:".$movie."#Intent;type=video/mp4;S.title=".urlencode($pg).";end";
else
$c="intent:".$movie."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($pg).";end";
//intent:https://cdn.flixtor.ac/embed/getfile?id=47489&res_yts=1080p#Intent;type=video/mp4;package=com.mxtech.videoplayer.pro;S.headers=%7B%27Cookie%3A+approve%3D1%27%7D;S.title=Elsa+%26+Fred;end
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
"sources": [{"file": "'.$movie.'", "type":"'.$type.'"}],
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
