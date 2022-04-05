<?php
error_reporting(0);
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$ua = $_SERVER['HTTP_USER_AGENT'];
$filelink = $_GET["file"];
$link_f =  array();
$type = "mp4";
$pg="";
// hqq
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
//
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
   } else
     $msg_captcha="";
} else {
   $msg_captcha="";
}
/////////////////////////////
if (file_exists($base_pass."debug.txt"))
 $debug=true;
else
 $debug=false;
if (isset($_GET["title"]))
  $pg=unfix_t(urldecode($_GET["title"]));
else {
  $t1=explode(",",$filelink);
  $filelink = urldecode($t1[0]);
  $pg = urldecode($t1[1]);
}
if (!$pg) $pg = "play now...";
$pg=unfix_t($pg);

$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    if (preg_match($indirect,$filelink)) {
     if (!preg_match("/sub_extern\.srt/",$l))
      unlink($l);
    } else {
     unlink($l);
    }
}
/*
if (file_exists("../../cookie/max_time_hqq.txt")) {
   $time_exp=file_get_contents("../../cookie/max_time_hqq.txt");
   $time_now=time();
   if ($time_exp > $time_now) {
     $minutes = intval(($time_exp-$time_now)/60);
     $seconds= ($time_exp-$time_now) - $minutes*60;
     if ($seconds < 10) $seconds = "0".$seconds;
     $msg_captcha=" | Expira in ".$minutes.":".$seconds." min.";
   } else
     $msg_captcha="";
} else {
   $msg_captcha="";
}
*/
/**####################################**/
if (preg_match("/(.*?)\s+\(?((1|2)\d{3})\)?\s+(\d+)x(\d+)/",$pg,$m)) { // series and year
  $tip="series";
  $tit=$m[1];
  $year=$m[2];
  $sez=$m[4];
  $ep=$m[5];
  $ep_tit=$sez."x".$ep;
} else if (preg_match("/(.*?)\s+(\d+)x(\d+)/",$pg,$m)) { //series no year
  $tip="series";
  $tit=$m[1];
  $year="";
  $sez=$m[2];
  $ep=$m[3];
  $ep_tit=$sez."x".$ep;
} else if (preg_match("/(.*?)\s+\(?((1|2)\d{3})\)?/",$pg,$m)) { // movie and year
  $tip="movie";
  $tit=$m[1];
  $year=$m[2];
  $sez="";
  $ep="";
  $ep_tit="";
} else { // movie no year
  $tip="movie";
  $tit=$pg;
  $year="";
  $sez="";
  $ep="";
  $ep_tit="";
}
$from="";
$imdbid="";
$tit=urldecode(str_replace("%E2%80%99",urlencode("'"),urlencode($tit)));
$tit_serial=$tit; // ?????????   %3F
$link_page="";
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit_serial))."&link=".$link_page."&ep_tit=".$ep_tit."&year=".$year;
//echo $sub_link;
//echo $filelink;
/**####################################**/
/** Here we start.......**/
if (preg_match("/filmeonlinegratis\.org/",$filelink)) {
//if (strpos($filelink,"filmeonlinegratis.org") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_NOBODY,0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $html = urldecode(str_replace("@","%",$html));
//} elseif (strpos($filelink,"fsgratis.") !== false) {
} elseif (preg_match("/classicmovieshd/",$filelink)) {
  // https://www.classicmovieshd.com/ajax/embed
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:82.0) Gecko/20100101 Firefox/82.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  if (1==2) {
  preg_match_all("/data\-embed\=\"(\d+)/",$html,$m);
  //print_r ($m);
  $html="";
  $l="https://www.classicmovieshd.com/ajax/embed";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_POST,1);
  for ($k=0;$k<count($m[1]);$k++) {
   $post="id=".$m[1][$k];
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Origin: https://www.classicmovieshd.com',
  'Connection: keep-alive',
  'Referer: https://www.classicmovieshd.com');
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  $html .='<iframe src="'.curl_exec($ch).'">';
  }
  curl_close($ch);
  }

  //echo $html;
} elseif (preg_match("/fsonline\.to/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:82.0) Gecko/20100101 Firefox/82.0";
  if (preg_match("/id\=/",$filelink)) {
  $t1=explode("id=",$filelink);
  $id=$t1[1];
  } else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $t1=explode('postid-',$html);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  }
  $l="https://fsonline.to/wp-admin/admin-ajax.php";
  $post="action=lazy_player&movieID=".$id;
  //echo $post;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).'',
  'Origin: https://fsonline.to',
  'Connection: keep-alive',
  'Referer: https://fsonline.to/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  $videos=explode('data-vs="',$h);
  unset($videos[0]);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_REFERER,$filelink);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $l=$t1[0];
    curl_setopt($ch, CURLOPT_URL,$l);

    $h1 = curl_exec($ch);
    //echo $h1;
  if (preg_match("/Location\:\s+(.+)/i",$h1,$m)) {
  $h .='<iframe src="'.trim($m[1]).'"> ';
  }
  }
  curl_close ($ch);
  $html=$h;
} elseif (preg_match("/serialeturcesti\.biz/",$filelink)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);

  $t1=explode('name="content-protector-token',$html);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $token=$t3[0];
  $t1=explode('name="content-protector-ident',$html);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $ident=$t3[0];
  $post="content-protector-captcha=1&content-protector-token=".$token."&content-protector-ident=".$ident."&content-protector-submit.x=338&content-protector-submit.y=206&content-protector-password=...";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded',
  'Content-Length: '.strlen($post).'',
  'Origin: https://serialeturcesti.biz',
  'Connection: keep-alive',
  'Referer: https://serialeturcesti.biz');
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  $html = curl_exec($ch);
  curl_close($ch);
} elseif (preg_match("/fsgratis\.|filmeserialegratis\.org/",$filelink)) {
  //https://fsgratis.com/miss-me-this-christmas/
  $host=parse_url($filelink)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_NOBODY,0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = htmlspecialchars_decode(curl_exec($ch));
  curl_close($ch);
  $html=$h;
  //echo $h;
  //preg_match_all("/trembed(\S+)\"/msi",$h,$m);
  $r=array();
  if (preg_match_all("/playerembed(\S+)\"/",$h,$m))
   $r=array_unique($m[1]);
  foreach($r as $key=>$value) {
    $l="https://".$host."/playerembed".$value;
    //echo $l;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_NOBODY,0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    //echo $h1;

    $t1=explode("tid=",$h1);
    $t2=explode('"',$t1[1]);
    $id = substr($t2[0], 0, -1);
    $id=strrev($id);
    $l="https://".$host."/?trhide=1&trhex=".$id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_REFERER, "https://".$host."/?trhide=1&tid=".strrev($id));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h2 = curl_exec($ch);
    curl_close($ch);
    //echo $h2;
    if (preg_match("/location:\s*(\S+)/i",$h2,$p))
      $html .='<iframe src="'.$p[1].'"> ';
    if (preg_match("/src\=\"(.*?)\"/",$h1,$p))
      $html .='<iframe src="'.$p[1].'"> ';
  }
} elseif (strpos($filelink,"filme-bune.info") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,0);
  curl_setopt($ch, CURLOPT_NOBODY,0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $html="";
  $videos = explode('player_preload.php?v=',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode("&",$video);
    $html .='<iframe src="'.base64_decode($t1[0]).'"> ';
  }
} elseif (strpos($filelink,"desenefaine.") !== false) {

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://desenefaine.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $html = urldecode(str_replace("@","%",$h));
  //$html=str_replace("player.desenefaine.io","hqq.tv",$html);
  //echo $html;


  $t1=explode('embed_url: "',$html);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://desenefaine.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $html = urldecode(str_replace("@","%",$h));
  //echo $html;
  $html=str_replace("player.desenefaine.io","hqq.tv",$html);
  //$html=str_replace("https://desenefaine.ro/embed.php?vid=","https://hqq.tv/embed.php?vid=",$html);
  //$html=str_replace("https://desenedublate.xyz/player/embed_player.php?vid=","https://hqq.tv/player/embed_player.php?vid=",$html);

} elseif (strpos($filelink,"pornhdo.com") !== false || strpos($filelink,"porndbs.com") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://pornhdo.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('var code="',$h);
  $t2=explode('"',$t1[1]);
  $code=$t2[0];
  $res="";
  for ($i=0;$i<strlen($code);$i++) {
      $res .=chr(ord($code[$i])^2);
  }
  $html = $h.urldecode($res);
} elseif (strpos($filelink,"filmeonline2019.us") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $html="";
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
  $t1=explode('showsrv" data-id="',$h);
  $t2=explode('"',$t1[1]);
  $embd=$t2[0];
  $videos = explode('b data-id="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $id=$t1[0];
    $l="https://filmeonline2019.us/embed/".$embd."/".$id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch,CURLOPT_REFERER,$filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    $html .=$h1;
  }
} elseif (strpos($filelink,"filmenoihd.biz") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $html="";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

  $videos = explode('atob("', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode('"',$video);
   //echo base64_decode($t1[0]);
   $h='<iframe src="'.base64_decode($t1[0]).'"> ';
   $html .=$h;
  }
} elseif (strpos($filelink,"divxfilmeonline.") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://divxfilmeonline.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $html=$h;
  if (preg_match_all("/player.php\?id\=([\w\-]*)/",$h,$m)) {
  for ($k=0;$k<count($m[1]);$k++) {
  $l="https://divxfilmeonline.org/script/myplayer/player.php?id=".$m[1][$k];
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match_all("/Location\:\s+(.+)/i",$h,$m)) {
  $html .='<iframe src="'.trim($m[1]).'"> ';
  }
  }
  }
} elseif (strpos($filelink,"pefilme.") !== false) {
  $html="";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://pefilme.net");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $videos = explode('"https://s1.pefilme.biz/?', $h);
  unset($videos[0]);
  $videos = array_values($videos);
  //print_r ($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $l=base64_decode($t1[0]);
    if (strpos($l,'atob("') !== false) {
    $t1=explode('atob("',$l);
    $t2=explode('"',$t1[1]);
    $html .=base64_decode($t2[0]).'" ';
    } else {
    $html .=$l;
    }
  }
  $html .=$h;
  //echo $html;
} elseif (strpos($filelink,"veziseriale.online") !== false || strpos($filelink,"veziserialeonline.") !== false) {
  //preg_match("/veziserialeonline.net|
  $headers = array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Encoding: deflate',
   'Accept-Language: en-US,en;q=0.5',
   'Cookie: recaptcha_validate=1; _popfired=2'
  );
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:62.0) Gecko/20100101 Firefox/62.0');
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
} elseif (strpos($filelink,"filmeserialeonline.org") !== false) {
//echo $filelink;
//die();
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close ($ch);
  $id=str_between($h2,'post_id":"','"');
  //echo $h2;
  if (preg_match("/id:\s+(\d+)/",$h2,$m)){
   $id=$m[1];
  }
//wp-content/themes/grifus/includes/single/second.php
  if (strpos($h2,"grifus/includes") !== false) {
    //$id=str_between($h2,'data: {id: ',')');
    // wp-content/themes/grifus/includes/single/second.php
    $tip=1;
    $post="id=".$id."&logat=1";
    $post="id=".$id;
    $l="http://www.filmeserialeonline.org/wp-content/themes/grifus/includes/single/second.php";
    $l="http://www.filmeserialeonline.org/wp-content/themes/grifus/includes/single/second.php";
  } else {
    //$id=str_between($h2,'data: {id: ','}');
    $tip=2;
    $post="id=".$id;
    $l="http://www.filmeserialeonline.org/wp-content/themes/grifus/loop/second.php";
  }
  //$post="call=03";
  //$post="id=".$id."&logat=1";
  //echo $l.$post;
$headers=array('Accept: text/html, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post).'',
'Origin: http://www.filmeserialeonline.org',
'Connection: keep-alive',
'Referer: '.$filelink.''
);
//print_r ($headers);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  if (strpos($html,"second_id.php") !== false) {
    $videos=explode('url: "',$html);
    unset($videos[0]);
    $videos = array_values($videos);
    foreach($videos as $video) {
      $t1=explode('"',$video);
      $l="http://www.filmeserialeonline.org/".$t1[0];
      //echo $l;
      $ch = curl_init($l);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
      curl_setopt($ch,CURLOPT_REFERER,$filelink);
      //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      //curl_setopt ($ch, CURLOPT_POST, 1);
      //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
      //curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h3 = curl_exec($ch);
      curl_close ($ch);
      //echo $h3."\n";
      if (strpos($h3,"filmeserialeonline.org") !== false) {
        $t1=explode('src="',$h3);
        $t2=explode('"',$t1[1]);
        $l=$t2[0];
        //echo $l."==============";
        $ch = curl_init($l);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch,CURLOPT_REFERER,$filelink);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $h3 = curl_exec($ch);
        curl_close ($ch);
        //echo $h3."========><><>";
      }
      $html .=$h3;
    }
  }
  //echo $html;
} elseif (strpos($filelink,"filmehd.to") !== false) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //curl_close ($ch);
  $videos=explode('data-vs="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $l=$t1[0];
    curl_setopt($ch, CURLOPT_URL,$l);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    $h1 = curl_exec($ch);
    //echo $h1;
  if (preg_match("/Location\:\s+(.+)/i",$h1,$m)) {
  $h .='<iframe src="'.trim($m[1]).'"> ';
  }
  }
  curl_close ($ch);
  $html=$h;
  //echo $html;
} elseif (strpos($filelink,"filmehd.se") !== false) {
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
$head=array('Cookie: cf_clearance=fbbe8f9c57520019735eaa5525d4a3d03c74eb0b-1598774324-0-1z49401450z1b78c8d4z7a4b72cc-150');
$cookie=$base_cookie."hdpopcorns.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);


  if (preg_match_all("/data\-src\=\"(.*?)\"/ms",$html,$m)) {
   $n=array_unique($m[1]);
   //print_r ($n);
   foreach ($n as $key => $value) {
    curl_setopt($ch, CURLOPT_URL, "https://filmehd.se".$value);
    $h = curl_exec($ch);
    $html .=$h;
   }
   curl_close ($ch);
 }
} elseif (strpos($filelink,"tvhub.") !== false || strpos($filelink,"serialeonlinesubtitrate.") !== false) {
  //echo $filelink;
  //print_r (parse_url($filelink));
  require_once("JavaScriptUnpacker.php");
  $cookie=$base_cookie."hdpopcorns.dat";
  $host=parse_url($filelink)['host'];
  $scheme="https";
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close ($ch);
  //echo $h1;
  $id=str_between($h1,"post_ID' value='","'");
  $id=str_between($h1,'data-id="','"');
  $t1=explode('eval(function(p,a,c,k,e,d)',$h1);
  $h2="eval(function(p,a,c,k,e,d)".$t1[count($t1)-1];
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  $out .=$h1;
  $post="post_id=".$id;
  $t0=explode('var postId',$out);
  if (preg_match("/url\:\s*\"(.*?)\"/",$t0[1],$m))
   $url=$m[1];
  else
   $url="/wp-content/themes/vizer/inc/parts/single/field-ajax.php";
// https://tvhub.org/wp-content/themes/grifus/includes/single/field-ajax3.php
  $l=$scheme."://".$host.$url;
  if (preg_match("/tvhub/",$host))
   $l="https://".$host."/wp-content/themes/grifus/includes/single/field-ajax3.php";
  else if (preg_match("/serialeonlinesubtitrate/",$host))
   $l="https://serialeonlinesubtitrate.ro/wp-content/themes/hdvix/field-ajax.php";
  //echo $post;
  //$post="post_id=2869118";
  $headers=array('Origin: '.$scheme.'://'.$host.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //curl_close ($ch);
  //echo $h;
  //die();
  $videos=explode('data-server="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $id_s=$t1[0];
    $post="post_id=".$id."&server_nr=".$id_s;
    //echo $post;
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    $h .= curl_exec($ch);
    //echo $h;
  }
  curl_close ($ch);
  $html=$h;
  //echo $html;
  //echo $h;
} elseif (strpos($filelink,"filme--online.") !== false) {
//echo $filelink;
  require_once("JavaScriptUnpacker.php");
  $cookie=$base_cookie."hdpopcorns.dat";
  $host=parse_url($filelink)['host'];
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://filme--online.ro");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close ($ch);
  //echo $h1;
  $id=str_between($h1,"post_ID' value='","'");
  $id=str_between($h1,'data-id="','"');
  //$t1=explode('eval(function(p,a,c,k,e,d)',$h1);
  //$h2="eval(function(p,a,c,k,e,d)".$t1[count($t1)-1];
  //$jsu = new JavaScriptUnpacker();
  //$out = $jsu->Unpack($h2);
  $post="post_id=".$id;
  $t0=explode('var postId',$h1);
  if (preg_match("/url\:\s*\"(.*?)\"/",$t0[1],$m))
   $url=$m[1];
  else
   $url="/wp-content/themes/vizer/inc/parts/single/field-ajax.php";
  $l="https://".$host.$url;
  //echo $l;
  //echo $post;
  //$l="http://filme--online.ro/wp-content/themes/vizer/inc/parts/single/field-ajax.php";
  //$post="post_id=2845285";
  $headers=array('Origin: https://'.$host.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://".$host);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //curl_close ($ch);
  //echo $h;
  $videos=explode('data-server="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $id_s=$t1[0];
    $post="post_id=".$id."&server_nr=".$id_s;
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    $h .= curl_exec($ch);
  }
  curl_close ($ch);
  $html=$h;
} elseif (strpos($filelink,"vezi-online.eu") !== false) {
//echo $filelink;
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://vezi-online.eu");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $videos = explode("id='player-option-", $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode("data-post='",$video);
   $t2=explode("'",$t1[1]);
   $id=$t2[0];
   $t1=explode("data-nume='",$video);
   $t2=explode("'",$t1[1]);
   $nume=$t2[0];
   $t1=explode("data-type='",$video);
   $t2=explode("'",$t1[1]);
   $tip=$t2[0];
   $l="https://vezi-online.eu/wp-admin/admin-ajax.php";
   $post="action=doo_player_ajax&post=".$id."&nume=".$nume."&type=".$tip;
   //echo $post;
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch,CURLOPT_REFERER,"https://vezi-online.eu");
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   //curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close ($ch);
   $r=json_decode($h,1);
   $l1=$r['embed_url'];
   $html .=' "'.$l1.'" ';
   //echo $h;
  }
} elseif (strpos($filelink,"filmeserialeonline.biz") !== false) {
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"filmeserialeonline.biz");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $videos = explode("dooplay_player_option", $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode("data-post=",$video);
   $t2=explode(" ",$t1[1]);
   $id=$t2[0];
   $t1=explode("data-nume=",$video);
   $t2=explode(">",$t1[1]);
   $nume=$t2[0];
   $t1=explode("data-type=",$video);
   $t2=explode(" ",$t1[1]);
   $tip=$t2[0];
   $l="https://filmeserialeonline.biz/wp-admin/admin-ajax.php";
   $post="action=doo_player_ajax&post=".$id."&nume=".$nume."&type=".$tip;
   //echo $post;
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch,CURLOPT_REFERER,"https://filmeseriale-hd.com");
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   //curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $html .=$h;
  }
//echo $h;
} elseif (strpos($filelink,"filmeonline2016.biz") !== false || strpos($filelink,"filmeonline.st") !== false) {
  $ua=$_SERVER['HTTP_USER_AGENT'];

  //echo $filelink;
  //https://filmeonline.st/spider-man-far-from-home-2019/
  $filelink=$filelink."?show_player=true";
  //echo $filelink;
  //$filelink="https://filmeonline.st/mortal-kombat-2021/?show_player=true";
  $head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Moz: prefetch',
'Connection: keep-alive',
'Referer: https://jurnalul.info/');
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT,$ua);
  //curl_setopt($ch,CURLOPT_REFERER,"http://www.filmeonline2016.biz/");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  if (preg_match_all("/atob\(\"(.*?)\"\)/",$html,$m)) {
  //print_r ($m);
  for ($k=0;$k<count($m[1]);$k++) {
    $html .='<iframe src="'.base64_decode($m[1][$k]).'"> ';
  }
  }

  if (preg_match("/script src\=\"(.*?)\" data\-minify/ms",$html,$m)) {
  $ch = curl_init($m[1]);
  curl_setopt($ch, CURLOPT_USERAGENT,$ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.filmeonline2016.biz/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close ($ch);
  $videos = explode('atob("', $h2);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode('"',$video);
   //echo base64_decode($t1[0]);
   $h='<iframe src="'.base64_decode($t1[0]).'"> ';
   //echo $h;
   $html .=$h;
  }
 }

//echo $html;
} elseif (strpos($filelink,"topfilmeonline.net") !== false) {
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"http://topfilmeonline.net/");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  // echo $html;
  $videos = explode('class="play_button', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode('data-singleid="',$video);
   $t2=explode('"',$t1[1]);
   $id=$t2[0];
   $t1=explode('data-server="',$video);
   $t2=explode('"',$t1[1]);
   $s=$t2[0];
   $l="https://topfilmeonline.net/wp-admin/admin-ajax.php";
   $post="action=samara_video_lazyload&singleid=".$id."&server=".$s;
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch,CURLOPT_REFERER,"http://topfilmeonline.net/");
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   //curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $html .=$h;
  }
//echo $html;
} elseif (strpos($filelink,"filmeonline.biz") !== false || strpos($filelink,"filmecinema.net") !== false) {

  $cookie=$base_cookie."biz.dat";
  $ua=$_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $html=urldecode($html);
} elseif (strpos($filelink,"f-hd.") !== false) {
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://f-hd.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $videos = explode('class="play_button', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode('data-singleid="',$video);
   $t2=explode('"',$t1[1]);
   $id=$t2[0];
   $t1=explode('data-server="',$video);
   $t2=explode('"',$t1[1]);
   $s=$t2[0];
   $l="https://f-hd.biz/wp-admin/admin-ajax.php";
   $post="action=samara_video_lazyload&singleid=".$id."&server=".$s;
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch,CURLOPT_REFERER,"https://f-hd.net");
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   //curl_setopt($ch, CURLOPT_HEADER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $html .=$h;
  }
} elseif (strpos($filelink,"filmeserialehd.") !== false) {
  require_once("JavaScriptUnpacker.php");
  $cookie=$base_cookie."hdpopcorns.dat";
  $host=parse_url($filelink)['host'];
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($filelink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close ($ch);

  $id=str_between($h1,"post_ID' value='","'");
  $id=str_between($h1,'data-id="','"');
  $t1=explode('eval(function(p,a,c,k,e,d)',$h1);
  $h2="eval(function(p,a,c,k,e,d)".$t1[count($t1)-1];
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h2);
  $post="post_id=".$id;
  $t0=explode('var postId',$out);
  if (preg_match("/url\:\s*\"(.*?)\"/",$h1,$m))
   $url=$m[1];
  elseif (preg_match("/url\:\s*\"(.*?)\"/",$out,$m))
   $url=$m[1];
  else
   $url="/wp-content/themes/serialenoi/field-ajax3.php";
  $l="https://".$host.$url;
  $headers=array('Origin: https://'.$host.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);

  $videos=explode('data-server="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $id_s=$t1[0];
    $post="post_id=".$id."&server_nr=".$id_s;
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
    $h .= curl_exec($ch);
  }
  curl_close ($ch);
  $html=$h;
  //echo $html;
} elseif (strpos($filelink,"filmserialonline.org") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('iframe src="',$h);
  $t2=explode('"',$t1[1]);
  $l="https:".$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
} elseif (strpos($filelink,"vezifs.") !== false) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  preg_match("/post_id\s*\:\s*(\d+)/",$html,$m);
  $id=$m[1];
  preg_match("/s3id\:\s*\'(tt\d+)\'/",$html,$m);
  $s3=$m[1];
  $post="action=show_server&post_id=".$id."&s3id=".$s3;
  //echo "\n".$post."\n";
  $l="https://vezifs.com/wp-admin/admin-ajax.php";
  $l="https://vezifs.com/wp-admin/admin-ajax.php";
   $ch = curl_init($l);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
   curl_setopt($ch,CURLOPT_REFERER,"https://vezifs.com");
   curl_setopt ($ch, CURLOPT_POST, 1);
   curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
   //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   //echo $h;
   $html =$h;
} elseif (strpos($filelink,"filmele-online.com") !== false) {
$ua = $_SERVER['HTTP_USER_AGENT'];
//echo $filelink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('class="movieplay"><iframe src="',$h);
  $t2=explode('"',$t1[1]);
  $filelink="https://filmele-online.com".$t2[0];
  $html='<iframe src="'.$filelink.'">';
} elseif (preg_match($indirect,$filelink)) {
  $html='<iframe src="'.$filelink.'">';
} elseif (preg_match("/gomostream\.com|gomo\.to\/movie/",$filelink)) {
  include("multilink.php");
  $html="";
  $x=multilink($filelink,$base_cookie."multilink.dat");
  foreach ($x as $key => $value) {
    $html .='<iframe src="'.$value.'">';
  }
} else {
  //echo $filelink;
  $filelink=str_replace(" ","%20",$filelink);
  //echo $filelink;
  //echo base64_decode("Ly93d3cub2sucnUvdmlkZW9lbWJlZC83ODU3NTA2MjQ4Nzk=");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch,CURLOPT_REFERER,"http://www.topvideohd.com/");
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html=curl_exec($ch);
  curl_close($ch);
  $html = urldecode(str_replace("@","%",$html));
}
//$html .=' "https://drive.google.com/file/d/1yNs4OjXCugk0CddF07xvaIEasxrLkb8V/view" ';
/**################ All links ################**/
//echo $html;
//die();
$h_debug=$html;
$html=str_replace("https","http",$html);
$html=str_replace("&#038;","&",$html);
$html=str_replace("&amp;","&",$html);
$html=str_replace("&quot;","'",$html);
//echo $html;
/* alias */
$html=str_replace('http://realyplayonli.xyz',"http://hqq.to",$html);
$html=str_replace('http://player.filmehd.se',"http://hqq.to",$html);
/* end alias */
if(preg_match_all("/(\/\/.*?)(\"|\'|\s)+/si",$html,$matches)) {
$links=$matches[1];
//print_r ($links);
}
//die();
//$links=array_unique($links);
//sort($links);
//print_r ($links);
$s="/adf\.ly|vidxden\.c|divxden\.c|vidbux\.c|movreel\.c|videoweed\.(c|e)|novamov\.(c|e)|vk\.com|gounlimited\.to";
$s=$s."|movshare\.net|youtube\.com|youtube-nocookie\.com|flvz\.com|rapidmov\.net|putlocker\.com|";
$s=$s."mixturevideo\.com|played\.to|";
$s=$s."peteava\.ro\/embed|peteava\.ro\/id|content\.peteava\.ro|divxstage\.net|divxstage\.eu|thevideo\.me|";
$s=$s."grab\.php\?link1=";
$s=$s."|vimeo\.com|googleplayer\.swf|filebox\.ro\/get_video|vkontakte\.ru|megavideo\.com|videobam\.com|";
$s=$s."vidzi\.tv|estream\.to|briskfile\.com|playedto\.me";
$s=$s."|fastupload|video\.rol\.ro|zetshare\.net\/embed|ufliq\.com|stagero\.eu|ovfile\.com|videofox\.net|";
$s=$s."fastplay\.cc|watchers\.to|fastplay\.to";
$s=$s."|trilulilu|proplayer\/playlist-controller.php|viki\.com|modovideo\.com|roshare|rosharing|ishared\.eu|";
$s=$s."stagevu\.com|vidup\.me|vidup\.io";
$s=$s."|filebox\.com|glumbouploads\.com|uploadc\.com|sharefiles4u\.com|zixshare\.com|uploadboost\.com|";
$s=$s."netu\.wiztube\.xyz|hqq\.tv|hqq\.to|hqq\.watch|waaw1?\.|waaws|hindipix\.in|pajalusta\.club|vidtodo\.com|vshare\.eu|bit\.ly";
$s=$s."|nowvideo\.eu|nowvideo\.co|vreer\.com|180upload\.com|dailymotion\.com|nosvideo\.com|vidbull\.com|";
$s=$s."purevid\.com|videobam\.com|streamcloud\.eu|donevideo\.com|upafile\.com|docs\.google|mail\.ru|";
$s=$s."superweb|moviki\.ru|entervideos\.com";
$s=$s."|indavideo\.hu|redfly\.us|videa\.hu|videakid\.hu|mooshare\.biz|streamin\.to|kodik\.biz|videomega\.tv|";
$s=$s."ok\.ru|realvid\.net|up2stream\.com|openload|allvid\.ch|oload|verystream|";
$s=$s."vidoza\.net|spankbang\.com|sexiz\.net|streamflv\.com|streamdefence\.com|veehd\.com|coo5shaine\.com|";
$s=$s."divxme\.com|movdivx\.com|thevideobee\.to|speedvid\.net|streamango|fruithosts|streamplay\.|";
$s=$s."gorillavid\.in|daclips\.in|movpod\.in|vodlocker\.com|filehoot\.com|bestreams\.net|vidto\.me|";
$s=$s."cloudyvideos\.com|allmyvideos\.net|goo\.gl|cloudy\.ec|rapidvideo\.com|megavideo\.pro|raptu\.com|";
$s=$s."vidlox|flashservice\.xvideos\.com|xhamster\.com|entervideo\.net|vcstream\.to|vev\.io|vidcloud\.icu|";
$s=$s."powvideo|povvideo|cloudvideo|vidtodo|vidcloud\.co|flashx\.|vidcloud\d*";
$s=$s."|putload\.|event\.2target\.net|fembed\.com|streamcherry\.com|hideiframe\.com|";
$s=$s."filmeonlinehd\.tv\/sharemovie|rovideo\.net\/video|flix555\.com|gamovideo\.com|playhd\.fun|idtbox\.com|";
$s=$s."bitporno\.com|thevideobee\.to|mangovideo\.|smartshare\.tv|datoporn\.co|xstreamcdn\.com|onlystream\.tv|";
$s=$s."database\.seriale|drive\.google\.com|videomega\.|vidload\.co|mixdrop\.|mystream\.to|mstream\.cloud";
$s=$s."|hxload\.|bazavox\.com|cloud\.vidhubstr\.org|vidia\.tv|gomostream\.com|viduplayer\.com|leaked-celebrities\.";
$s=$s."|prostream\.to|videobin\.co|upstream\.to|playtvid\.com|jetload\.net|vidfast\.co|clipwatching\.";
$s=$s."|(video|player)\.filmeserialeonline\.org|streamwire\.|cloudvid\.icu|mstream\.xyz|streamhoe\.online|videyo\.";
$s=$s."|fastvid\.co|vidload\.net|rovideo\.net\/embed|eplayvid\.com|dood\.|mediashore\.org|uptostream\.com";
$s=$s."|movcloud\.net|dogestream\.|streamtape\.|jawcloud\.|evoload\.|sendvid\.|easyload\.io|okstream\.";
$s=$s."|youdbox\.com|filmele-online\.com|playtube\.|ninjastream\.to|userload\.co|goplayer\.online|videovard\.|cloudemb\.|streamlare\./i";
/////////////////////////////////////////////
//$x=preg_grep($s,$links);
//print_r ($x);
$links=array_unique($links);
sort($links);
//print_r ($links);
////////////////////////////////////////////
for ($i=0;$i<count($links);$i++) {
 if (preg_match($s,$links[$i])) {
  $cur_link="http:".$links[$i];
  if (preg_match("/adf\.ly/",$links[$i])) { // very old links
    if (strpos($links[$i],"http") !== false) {
      $t1=explode("http",$links[$i]);
      $cur_link="http".$t1[1];
    } else {
      $cur_link="";
    }
  }
  //echo $cur_link."\n";
  if (preg_match("/bit\.ly|goo\.gl|hideiframe\.com|(video|player)\.filmeserialeonline\.org/",$links[$i])) {
   $l=trim("https:".$links[$i]);
   //echo $l;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
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
   if (preg_match("/location\:\s*(.+)/i",$h2,$m)) {
    $cur_link=trim($m[1]);
    if (strpos($cur_link,"http") === false) $cur_link="https:".$cur_link;
   } else
    $cur_link="";
  }
   if (preg_match("/leaked-celebrities\./",$links[$i])) {
   $l=trim("https:".$links[$i]);
   //echo $l."\n";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
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
    $cur_link=trim($m[1]);
   else
    $cur_link="";
  }
  //echo $cur_link;
  if (strpos($links[$i],"fastvid.co") !== false) {
   $l=trim("https:".$links[$i]);
   //echo $filelink;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
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
   if (preg_match("/location\:\s*(.+)/i",$h2,$m)) {
     $l1=$m[1];
     if (strpos($l1,"database.seriale") !== false) {
      $links[$i]=$l1;
     } else {
      $cur_link=$l1;
     }
   } else
    $cur_link="";
  }
  if (strpos($links[$i],"cloudvid.icu") !== false) {
   $l=trim("https:".$links[$i]);
   //echo $l;
   $q=parse_str(parse_url($l)['query'],$output);
   if (isset($output['sub'])) $srt=$output['sub'];
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch,CURLOPT_REFERER,"https://serialeonline.to");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_HEADER,1);
   curl_setopt($ch, CURLOPT_NOBODY,1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h2 = curl_exec($ch);
   curl_close($ch);
   //echo $h2;
   if (preg_match("/Location:\s*(.+)/i",$h2,$m)) {
     $l1=$m[1];
     if (strpos($l1,"database.seriale") !== false) {
      $links[$i]=$l1;
     } else {
      $cur_link=$l1;
     }
   } else {
     $cur_link="";
   }
 }
  //if (strpos($links[$i],"database.serialeonline.to") !== false) {
  if (preg_match("/database\.serialeonline\.to|database\.seriale\-online\.net/",$links[$i])) {
  if (strpos($links[$i],"https://database") === false)
  $l=trim("https:".$links[$i]);
  else
  $l=$links[$i];
  //echo $l."\n";
  //$s=array();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://serialeonline.to");
  //curl_setopt($ch,CURLOPT_REFERER,"https://filmehd.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  $h2 = curl_exec($ch);
  curl_close($ch);
  //echo $h2;
   if (preg_match("/location\:\s*(.+)/i",$h2,$m)) {
    $cur_link=trim($m[1]);
    //echo html_entity_decode(urldecode($cur_link))."\n";
    if (strpos($cur_link,"database.seriale") !== false) {
     $cur_link="";
    }
   } else
    $cur_link="";
  }
  if (strpos($links[$i],"2target.net") !== false) {
    //https://event.2target.net/jc1M
    $l="https:".$links[$i];
    $cookie=$base_cookie."event.dat";
    if (file_exists($cookie)) unlink ($cookie);
    $ua     =   $_SERVER['HTTP_USER_AGENT'];
    $head = array(
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    'Accept-Encoding: deflate',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $h1 = curl_exec($ch);
    $t1 = explode('class="timer">',$h1);
    $t2 = explode('<',$t1[1]);
    $sec = $t2[0];
    if (!$sec) $sec=2;
  $t1=explode('<form method="post',$h1);
  $t2=explode("</form",$t1[1]);
  $xx='<form method="post'.$t2[0]."</form>";

  $t1=explode('name="_csrfToken',$h1);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $csrfToken=$t3[0];
  $t1=explode('name="ad_form_data',$h1);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $ad_form_data=$t3[0];
  //die();
  //echo "aa=".base64_decode($ad_form_data)."\n";
  $t1=explode('name="_Token[fields]',$h1);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $token1=$t3[0];

  $t1=explode('name="_Token[unlocked]',$h1);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $token2=$t3[0];
  $data = array('_method' => 'POST',
  '_csrfToken' => $csrfToken,
  'ad_form_data' => $ad_form_data,
  '_Token[fields]' => $token1,
  '_Token[unlocked]' => $token2
  );
  $post =  http_build_query ($data);

  $l2="https://event.2target.net/links/go";
  $head=array('Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: en-US,en;q=0.5',
  'Accept-Encoding: deflate',
  'X-CSRF-Token: '.$csrfToken.'',
  'Referer: https://event.2target.net/jc1M',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post).''
  );
  sleep ($sec);
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,0);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  $x = curl_exec($ch);
  curl_close($ch);
  //echo $x;
  $r=json_decode($x,1);
  if (isset($r['url']))
    $cur_link = $r['url'];
  else
    $cur_link = "";
  }
  //echo $cur_link;
  $t1=explode(" ",$cur_link);     //vezi-online
  $cur_link=$t1[0];
  $t1=explode("&stretching",$cur_link);    //vezi-online
  $cur_link=$t1[0];
  if (strpos($cur_link,"entervideos.com/vidembed") !==false) {
  $t1=explode("&",$cur_link);    //
  $cur_link=$t1[0];
  }
  $cur_link=str_replace(urldecode("%0D"),"",$cur_link);
  //print_r(parse_url($cur_link));
  $t1=explode("&img=",$cur_link);
  $cur_link=$t1[0];
  //echo $cur_link."<BR>";
  //if (preg_match($s,$cur_link)) {
     $t1=explode("proxy.link=",$cur_link); //vezi-filme
   if (sizeof ($t1) > 1 ) {
     if ($t1[1]) {
       $t2=explode("&",$t1[1]);
       $cur_link=trim($t2[0]);
     }
   }
   /* try to remove links like http://abc.com or http://abc.com/ but not http://abc.com?x=y or http://abc.com/?x=y*/
   //print_r (parse_url($cur_link));
   if (isset(parse_url($cur_link)["path"]))
      $path=parse_url($cur_link)["path"];
   else
      $path="";
   if (!$path && !isset(parse_url($cur_link)["query"])) $cur_link="";
   if ($path=="/" && !isset(parse_url($cur_link)["query"])) $cur_link="";
   //echo "c=".$cur_link;
   $pat="/hqq\.watch|xopenload\.me|hqq\.tv\/player\/script\.php|top\.mail\.ru|facebook|twitter|player\.swf";
   $pat .="|img\.youtube|youtube\.com\/user|radioarad|\.jpg|\.png|\.gif|jq\/(js|css)";
   $pat .="|fsplay\.net\?s|changejplayer\.js|validateemb\.php|restore_google\.php|clicksud\.biz|123formbuilder\.com|";
   $pat .="ExoLoader.addZone|js\/api\/share\.js|hindipix\.in\/(js|style)|share\.php\?|brave\.com|affiliate\.rusvpn\.com/i";
   if (!preg_match($pat,$cur_link)) {
     $cur_link=str_replace(urldecode("%0A"),"",$cur_link);
     if ($cur_link) $link_f[]=$cur_link;
   }
  //echo $cur_link."\n";
 }
}

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
//print_r ($link_f);
$link_f=array_unique($link_f);
//print_r ($link_f);
//$n= count($link_f);
$find_hqq = false;
if (count ($link_f) > 0) {
echo '
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

      <meta charset="utf-8">
      <title>Alege varianta</title>
   	  <link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<script type="text/javascript">
function playtube(title,link) {
  document.getElementById("server").innerHTML = '."'".'<font size="6" color="red">Asteptati..................</font>'."'".';
     //document.getElementById("fancy").href="playtube.php?link=" + link + "&title=" + title;
     //document.getElementById("fancy").click();
     document.getElementById("myframe").src="playtube.php?link=" + link + "&title=" + title;
}
function ajaxrequest2(title, link) {
  var request =  new XMLHttpRequest();
  var the_data = "title="+ title +"&file="+link;
  var php_file="link1.php?" + the_data;
  document.getElementById("server").innerHTML = '."'".'<font size="6" color="lightblue">Alegeti un server</font>'."'".';
  window.open(php_file, "_blank");
}
// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest(title, link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  document.getElementById("server").innerHTML = '."'".'<font size="6" color="red">Asteptati..................</font>'."'".';
  var the_data = "title="+ title +"&link="+link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       //alert (request.responseText);
       ';
      if ($debug) echo "document.getElementById('debug').innerHTML = request.responseText.match(/http.+#/g);"."\r\n";
      echo '
       document.getElementById("server").innerHTML = '."'".'<font size="6" color="lightblue">Alegeti un server</font>'."'".';
       document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function ajaxrequest1(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  document.getElementById("server").innerHTML = '."'".'<font size="6" color="red">Asteptati..................</font>'."'".';
  var the_data = link;
  var php_file="hqq2.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       //alert (request.responseText);
       document.getElementById("server").innerHTML = '."'".'<font size="6" color="lightblue">Alegeti un server</font>'."'".';

    }
  }
}

   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     //alert (charCode);
     if (charCode == "49") {
      document.getElementById("opensub").click();
     } else if (charCode == "50") {
      document.getElementById("titrari").click();
     } else if (charCode == "51") {
      document.getElementById("subs").click();
     } else if (charCode == "52") {
      document.getElementById("subtitrari").click();
     } else if (charCode == "53") {
      document.getElementById("viz").click();
     } else if (charCode == "55") {
      document.getElementById("opensub1").click();
     } else if (charCode == "56") {
      document.getElementById("titrari1").click();
     } else if (charCode == "57") {
      document.getElementById("subs1").click();
     } else if (charCode == "48") {
      document.getElementById("subtitrari1").click();
     }
   }
document.onkeypress =  zx;
</script>
<style>
td.link {
    font-style: bold;
    font-size: 25px;
    text-align: left;
}
</style>
<!--font-family: Arial, Helvetica, sans-serif;-->
</head>
<body>';
echo '<iframe id="myframe" src="" style="display: none;"></iframe>';
echo '<a id="fancy" data-fancybox data-type="iframe" href=""></a>';
//$out1="http://127.0.0.1:8080/scripts/subs/out.m3u";
//$title="play...";
//$c="intent:".$out1."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
//alert (request.responseText);
$c="";

echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<h2>'.$pg."</H2><BR>";
echo '<label id="server"><font size="6" color="lightblue">Alegeti un server</font></label><BR>';
echo '<table border="0" width="90%">'."\n\r";
$cap=0;
foreach($link_f as $k=>$val) {
$server="";
$server = parse_url($link_f[$k])["host"];
if (preg_match("/hqq\.|waaw1?|netu|pajalusta|hindipix\.|goplayer\.online|netu\.wiztube\.xyz/",$link_f[$k])) {
  if (preg_match("/\?vid\=http/",$link_f[$k])) {
   $t1=explode("?vid",$link_f[$k]);
   $link_f[$k]=$t1[1];
  }
    $link_f[$k]=str_replace($server,"hqq.tv",$link_f[$k]);
    $l1=str_replace("/f/","/e/",$link_f[$k]);
    $l1=str_replace("/e/","/watch_video.php?v=",$l1);
$pattern = "@(?:\/\/|\.)((?:waaw1?|netu|hqq|hindipix)\.(?:tv|watch|in))\/(?:watch_video\.php\?v|.+?vid)=([a-zA-Z0-9]+)@";
//echo $link_f[$k];
  if (preg_match($pattern,$l1,$m))
    $vid=$m[2];
  elseif (preg_match("/(hqq|netu)(\.tv|\.watch)\/player\/hash\.php\?hash=\d+/",$link_f[$k])) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l1);
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
    $find_hqq=true;
    $cap++;
    //echo $vid;
}
 if ($flash != "mp")  {
   //echo $link_f[$k];
   if (strpos($link_f[$k],"hqq.") !== false ) {
    echo '<TR><td class="link"><a href="link1.php?file='.urlencode($link_f[$k]).'&title='.urlencode($pg).'" target="_blank">'.$server.'</a>
    <!--<a id="fancy" data-fancybox data-type="iframe" href="hqq_sh.php?vid='.$vid.'">| get sh!</a>-->
    <a href="hqq.html" target="_blank"><font color="lightblue"> | Captcha</font></a>
    <a href="http://hqq.tv/sec/player/embed_player.php?gtoken=03&vid=1" target="_blank"><font color="lightblue"> | Captcha (cookie.txt add-onn)</font></a>
    <!--<script type="application/javascript" src="hqq_sh1.php?vid='.$vid.'"></script>-->
    ';
if ($cap == 1)  echo '<label id="hqq_msg">'.$msg_captcha."</label>";
    echo '</TD></TR>';
   } elseif (strpos($link_f[$k],"thevideo.") !== false || strpos($link_f[$k],"vev.") !== false)
    echo '<TR><td class="link"><a href="link1.php?file='.urlencode($link_f[$k]).'&title='.urlencode($pg).'" target="_blank">'.$server.'</a> <a href="https://vev.io/pair" target="_blank"><font color="lightblue"> | Pair IP (4 ore)</font></a></TD></TR>';
   elseif (strpos($link_f[$k],"vidup.io") !== false)
    echo '<TR><td class="link"><a href="link1.php?file='.urlencode($link_f[$k]).'&title='.urlencode($pg).'" target="_blank">'.$server.'</a> <a href="https://vidup.io/pair" target="_blank"><font color="lightblue"> | Pair IP (4 ore)</font></a></TD></TR>';
   elseif (preg_match("/playtube22\./",$link_f[$k])) {
//echo $link_f[$k];
    echo '<TR><td class="link"><a onclick="playtube('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".$server.'</a></TD></TR>';
   } else
    echo '<TR><td class="link"><a href="link1.php?file='.urlencode($link_f[$k]).'&title='.urlencode($pg).'" target="_blank">'.$server.'</a></TD></TR>';
  } else {  //== "mp"
   if (strpos($link_f[$k],"hqq.") !== false) {
   echo '<TR><td class="link"><a onclick="ajaxrequest('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".$server.'</a>
   <!--<a id="fancy" data-fancybox data-type="iframe" href="hqq_sh.php?vid='.$vid.'">| get sh!</a>-->
   <!--<a href="hqq_captcha.php" target="_blank"><font color="lightblue"> | Captcha</font></a>-->
   <!--<a href="intent:http://127.0.0.1:8080/scripts/filme/hqq_captcha.php#Intent;package=org.mozilla.firefox;S.title=Captcha;end" target="_blank"><font color="lightblue"> | Captcha (firefox)</font></a>-->
   <a href="intent:http://hqq.tv/sec/player/embed_player.php?gtoken=03&vid=1#Intent;package=org.mozilla.firefox;S.title=Captcha;end" target="_blank"><font color="lightblue"> | Captcha</font></a>
   <!--<a href="intent:http://127.0.0.1:8080/scripts/filme/hqq.html#Intent;package=org.mozilla.firefox;S.title=Captcha;end" target="_blank"><font color="lightblue"> | Captcha (v2)</font></a>-->
   <!--<script type="application/javascript" src="hqq_sh1.php?vid='.$vid.'"></script>-->
   ';
if ($cap == 1)  echo '<label id="hqq_msg">'.$msg_captcha."</label>";
    echo '</TD></TR>';
   } elseif (strpos($link_f[$k],"thevideo.") !== false || strpos($link_f[$k],"vev.") !== false)
   echo '<TR><td class="link"><a onclick="ajaxrequest('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".''.$server.'</a> <a href="https://vev.io/pair" target="_blank"><font color="lightblue"> | Pair IP (4 ore)</font></a></TD></TR>';
    elseif (strpos($link_f[$k],"vidup.io") !== false )
   echo '<TR><td class="link"><a onclick="ajaxrequest('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".''.$server.'</a> <a href="https://vidup.io/pair" target="_blank"><font color="lightblue"> | Pair IP (4 ore)</font></a></TD></TR>';
   elseif (preg_match("/playtube22\./",$link_f[$k])) {
     echo '<TR><td class="link"><a onclick="playtube('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".$server.'</a></TD></TR>';
   } else
     echo '<TR><td class="link"><a onclick="ajaxrequest('."'".urlencode($pg)."', '".urlencode($link_f[$k])."')".'"'." style='cursor:pointer;'>".$server.'</a></TD></TR>';
   }
}
echo '</TABLE>';
//echo '<img id="load" src= "load.jpg" width="450px" height="450px">';


echo '<BR><table border="1" width="100%">';
echo '<TR>';
echo '<TD class="mp"><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1em" align="center" colspan="4">Alegeti o subtitrare (cauta imdb id)</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub1" href="opensubtitles1.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari1" href="titrari_main1.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs1" href="subs_main1.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari1" href="subtitrari_main1.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<br>
<table border="0px" width="100%">
<TR>
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari
<BR>Scurtaturi: 7=opensubtitles, 8=titrari, 9=subs, 0=subtitrari (cauta imdb id)<BR>
Instructiuni pentru "Captcha" gasiti <a href="hqq_exp.html">aici.</a>
</b></font></TD></TR></TABLE>
';
echo '
<div id="debug" style="vertical-align:top;height:auto;width:100%;word-wrap: break-word;"></div>
<BR>
';
echo '<br></div></body>';
echo '</html>';
} else {
echo '
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

      <meta charset="utf-8">
      <title>Alege varianta</title>
   	  <link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>';
echo '<h2>'.$pg."</H2>";
echo "<H2>Nu s-au gasit servere!?</H2>";
echo '<br></div></body>
</html>';
}
if (file_exists($base_pass."debug.txt")) {
echo '<BR>';
//echo $h_debug;
preg_match_all("/\<iframe(.*?)src\=(\"|\')(.*?)(\"|\')/msi",$h_debug,$m);
//print_r ($m);
for ($k=0;$k<count($m[3]);$k++) {
  echo $m[3][$k]."<BR>";
}
}
?>
<!--
table {
  table-layout:fixed;
}
-->
