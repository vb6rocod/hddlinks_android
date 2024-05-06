<?php
//set_time_limit(0);
error_reporting(0);
$hw=2;
include ("../common.php");
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
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists($base_sub.".srt")) unlink ($base_sub.".srt");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function getSiteHost($siteLink) {
		// parse url and get different components
		$siteParts = parse_url($siteLink);
		$port=$siteParts['port'];
		if (!$port || $port==80)
          $port="";
        else
          $port=":".$port;
		// extract full host components and return host
		return $siteParts['scheme'].'://'.$siteParts['host'].$port;
}
include ("../filme/youtube.php");
include ("../filme/yt.php");
//http://gradajoven.es/spicenew.php
//http://edge3.spicetvnetwork.de:1935/live/_definst_/mp4:spicetv/ro/6tv.stream/chunklist_w2087458837.m3u8?c=176&u=52409&e=1398753453&t=298944a96a9161b2300ae3ae072b85f4&d=android&i=1.30
//http://edge1.spicetvnetwork.de:1935/live/_definst_/mp4:spicetv/ro/6tv.streamchunklist_w2087458837.m3u8?c=176&u=52560&e=1398777448&t=3869972b307e53bfd2e048f093fd5f1c&d=site&i=Android%2C+Safari
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (isset($_POST["link"])) {
$link = unfix_t(urldecode($_POST["link"]));
$link=str_replace(" ","%20",$link);
$title = unfix_t(urldecode($_POST["title"]));
$mod=$_POST["mod"];
$from=$_POST["from"];
} else {
$link = unfix_t(urldecode($_GET["link"]));
//echo $link;
$link=str_replace(" ","%20",$link);
$title = unfix_t(urldecode($_GET["title"]));
$mod=$_GET["mod"];
$from=$_GET["from"];
//$filelink_mpc="link1.php?file=".urlencode($filelink)."&title=".urlencode($pg)."&flash=mpc";
$filelink_mpc="direct_link.php?link=".urlencode($link)."&title=".urlencode($title)."&from=".$from."&mod=".$mod."&flash=mpc";
if (isset($_GET['flash'])) $flash="mpc";
}
$link=str_replace(urldecode("%0D%0A"),"",$link);
//$link="https://cdn.drm.protv.ro/avod/2019/01/08/man:a121b208f4d2ceb191be29119e3a23b4:ef955898665ba80da15610d4c7b04804-7d017cb346b477351d97fe79378b111c.ism/man:a121b208f4d2ceb191be29119e3a23b4:ef955898665ba80da15610d4c7b04804-7d017cb346b477351d97fe79378b111c.m3u8";
//$link="http://89.136.209.30:1935/liveedge/TVRMOLDOVA.stream/playlist.m3u8";
//$link=urldecode("https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3Dr_d4ryn9UsA&title=Gaming%20Music%20Radio%20%E2%9A%A1%2024/7%20NCS%20Live%20Stream%20%E2%9A%A1%20Trap,%20Chill,%20Electro,%20Dubstep,%20Future%20Bass,%20EDM");
//$mod="direct";
if ($from=="alieztv") {
$q=parse_url($link)['query'];
parse_str($q,$r);
//print_r ($r);
if (preg_match("/aliez/",$r["t"])) {
 $link="https://emb.apl340.me/player/live.php?id=".$r["c"];
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/pl\.init\(\'([^\']+)/",$h,$n);
  //print_r ($n);
  $link="https:".$n[1];
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
 } elseif (preg_match("/youtube/",$r['t'])) {
  $link="https://youtube.com/embed/".$r["c"];
  //echo $link;
  //die();
 } elseif (preg_match("/ifr/",$r['t'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<table OnMouseOver=',$h);
  $h1=$t1[1];
  preg_match("/\<iframe.+src\=\"([^\"]+)\"/",$h1,$m);
  $link=fixurl($m[1]);
  //https://sons-stream.com/tvsxxx.php?hd=322
  //https://embed.tvcom.cz/59f15d65-ea2f-43ee-b7eb-89347b64a9bb/720/
  //https://wikisport.se/court/t3.php
  //https://ustream.pro/us22.php todo
  //https://antennasports.ru/matchpremier.php
  //https://maxsport.one/matchpremier.php
  //https://poscitechs.shop/live/stream-573.php
  //https://d.daddylivehd.sx/embed/stream-41.php
  //https://popcdn.day/go.php?stream=TNT1UK
  //https://player.qazcdn.com/WdbXg0jXfc/6m2pa7f
  //https://lato.sx/ch33
  //https://voodc.com/embed/1/858a928ba084889387998388938c98858891.html
  //https://fullassia.com/live/sport24/?lang=en todo
  //https://emb.apl341.me/player/live.php?id=222408&w=700&h=480
  //https://soccerstream100.co/embed/ch-42.php
  //https://aliezstream.pro/live/dazn2_german.php
  //https://espoplay.com/ch2.htm  ???
  //https://www.goal19.biz/livetv/ch31.php
  //https://tv.livegoal.site/2024/05/ch_3.html?id=bein4fr
  //echo $link;
 }
}
if (preg_match("/espoplay\.com/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://espoplay.com/');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  //echo $h;
  $t1=explode("channel='",$h);
  $t2=explode("'",$t1[1]);
  $ch=$t2[0];
  $t1=explode("g='",$h);
  $t2=explode("'",$t1[1]);
  $g=$t2[0];
  $l="https://one.myball.online/hembedplayer/".$ch."/".$g."/700/480";
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  //echo $h;
}
if (preg_match("/aliezstream\.pro/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: http://livetv.sx/');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  preg_match("/\<iframe.+src\=\"([^\"]+)\"/",$h,$m);
  $link=fixurl($m[1]);
  $link=str_replace(".php",".json",$link);
  curl_setopt($ch, CURLOPT_URL, $link);
  $h = curl_exec($ch);
  $id=json_decode($h,1)['id'];
  curl_close($ch);
  $link="https://emb.apl340.me/player/live.php?id=".$id;
}
if (preg_match("/emb\.apl\d+\.me\/player/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: http://livetv.sx/');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/pl\.init\(\'([^\']+)/",$h,$n);
  //print_r ($n);
  $link="https:".$n[1];
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  $link=get_max_res($h,$link);
}
if (preg_match("/voodc\.com/",$link)) {
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
   'Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: https://voodc.com/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,0);
  $h = curl_exec($ch);
  $t1=explode('src="//voodc.com',$h);
  $t2=explode('"',$t1[1]);
  $l= "https://voodc.com".$t2[0];
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  $t1=explode('embedded+"',$h);
  $t2=explode('"',$t1[1]);
  $l="https://voodc.com/play/d".$t2[0];
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);

  preg_match("/file\"\:\s*\'([^\s\']+)\'/",$h,$m);
  $link=$m[1];
  curl_close($ch);
}
if (preg_match("/player\.qazcdn\.com/",$link)) {
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
   'Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,0);
  $h = curl_exec($ch);

  preg_match("/source\s+\=\s+\"([^\"]+)\"/",$h,$m);
  $link=$m[1];
  curl_setopt($ch, CURLOPT_URL, $link);
  $h = curl_exec($ch);
  //echo $h;
  //$link=get_max_res($h,$link);
  curl_close($ch);
}
if (preg_match("/popcdn\.day/",$link)) {
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
   'Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  //https://love2live.wideiptv.top/TNT1UK/index.fmp4.m3u8?token=e16bc1e281902c605a0a95a66dae3cb9a7412555-5c68f02e58c6ddbc21e7449337b2d1e8-1714656379-1714645579
  //https://love2live.wideiptv.top/TNT1UK/embed.html?token=49951d5347cd117b28ef2b41226b54c9d820be36-609da27a497fab85089a8d4b13eb1196-1714655990-1714645190&remote=no_check_ip
  $link=str_replace("embed.html","index.fmp4.m3u8",$l3);
}
if (preg_match("/daddylivehd\.sx\/embed\//",$link)) { ////////////////////////////////
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
   'Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]); //https://weblivehdplay.ru/premiumtv/daddyhd.php?id=41
//echo $l3;
   $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: gzip, deflate, br',
   'Origin: https://livehdplay.ru',
   'Referer: https://livehdplay.ru/',
   'Connection: keep-alive',
   'Upgrade-Insecure-Requests: 1',
   'Sec-Fetch-Dest: iframe',
   'Sec-Fetch-Mode: navigate',
   'Sec-Fetch-Site: cross-site');
  curl_setopt($ch, CURLOPT_URL, $l3);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/source\:\s*\'([^\']+)\'/",$h,$m))
    $link=$m[1];
  else
    $link="";
    //echo $link;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://livehdplay.ru/")."&Origin=".urlencode("https://livehdplay.ru");
}
if (preg_match("/(antennasports|maxsport|poscitechs|soccerstream100)\./",$link)) {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/\<iframe.+src\=\"([^\"]+)\"/",$h,$m);
  $link=fixurl($m[1]);
  //echo $link;
  //echo $h;
}
if (preg_match("/(ustream|instream)\.pro/",$link)) {
 ////https://ustream.pro/us22.php
 ////ustream.pro/hls.php?stream=u3Zz9Ifdy7XL
 //https://instream.pro/hls2.php?stream=d3d8ydMiJVxg
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: application/json, text/plain, */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  //echo $h;
  if (!preg_match("/stream=/",$link)) {
  preg_match("/\<iframe.+src\=\"([^\"]+)\"/",$h,$m);
  $link=fixurl($m[1]);
  curl_setopt($ch, CURLOPT_URL, $link);
  $h = curl_exec($ch);
  }
  curl_close($ch);
  preg_match("/source\:\s+\"([^\"]+)\"/",$h,$m);
  $link=fixurl($m[1]);
}
if (preg_match("/(wikisport|stream\.crichd|cdnssd|lavents|lato|virazo)\./",$link)) {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: http://livetv.sx/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('fid="',$h);
  $t2=explode('"',$t1[1]);
  $fid=$t2[0];
  $l="https://fiveyardlab.com/wiki.php?player=desktop&live=".$fid;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://wikisport.click/',
  'Origin: https://wikisport.click');
  if (!$fid) {
   $t1=explode("fid='",$h);
   $t2=explode("'",$t1[1]);
   $fid=$t2[0];
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://cdnssd.ru/',
  'Origin: https://cdnssd.ru');
   $l="https://locatedinfain.com/embed2.php?player=desktop&live=".$fid;
  }
  //$l="https://fiveyardlab.com/wiki.php?player=desktop&live=".$fid;

  //echo $l;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://fiveyardlab.com/")."&Origin=".urlencode("https://fiveyardlab.com");
}
if (preg_match("/tvcom.cz/",$link)) {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: application/json, text/plain, */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://embed.tvcom.cz/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/application\/x\-mpegURL\'\,\s+src\:\s+\'([^\']+)/",$h,$m);
  $link=$m[1];
}
if (preg_match("/livematch\.ge/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://livematch.ge/',
  'Origin: https://livematch.ge'
  );
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/\/ch(\d+)/",$h,$m)) {
    $l="https://viwlivehdplay.ru/mono.php?id=".$m[1];
    
    curl_setopt($ch, CURLOPT_URL, $l);
    $h = curl_exec($ch);
    if (preg_match("/source\:\'([^\']+)/",$h,$n)) {
     $link=$n[1];
     if ($flash <> "flash")
      $link=$link."|Referer=".urlencode("https://viwlivehdplay.ru/")."&Origin=".urlencode("https://viwlivehdplay.ru")."&User-Agent=".urlencode($ua);
    }
  }
  curl_close($ch);
}
if (preg_match("/viwlivehdplay\./",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://livematch.ge/',
  'Origin: https://livematch.ge'
  );
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
    if (preg_match("/source\:\'([^\']+)/",$h,$n)) {
     $link=$n[1];
  //$link="https://webufffit.onlinehdhls.ru/lb/primamatchpremier/index.m3u8";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/125.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: gzip, deflate, br',
  'Origin: https://viwlivehdplay.ru',
  'Connection: keep-alive',
  'Referer: https://viwlivehdplay.ru/',
  'Sec-Fetch-Dest: empty',
  'Sec-Fetch-Mode: cors',
  'Sec-Fetch-Site: cross-site');
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/125.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location:\s*(.+)/i",$h,$m))
   $link=trim($m[1]);
  else
   $link=get_max_res($h,$link);
     if ($flash <>"flash")
     $link=$link."|Referer=".urlencode("https://viwlivehdplay.ru/")."&Origin=".urlencode("https://viwlivehdplay.ru")."&User-Agent=".urlencode($ua);
    }
}
if (preg_match("/porstream\.de|sportybite\.top|sporstream\.de/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://porstream.de/',
  'Origin: https://porstream.de'
  );
  //echo $link;
  //$link="https://sportybite.top/blackpool-nottingham-forest-live-stream?id=4185";
  //$link="https://sportybite.top/blackpool---nottingham-forest--live-stream?id=4185
  //$link="https://sportybite.top/blackpool-nottingham-forest--live-stream?id=4185";
  //$link="https://sportybite.top/blackpool---nottingham-forest-4185-live-streaming-online-free";
  //$link="https://sportybite.top/event-live-stream?id=4185";
  /*
  if (preg_match("/(\d+)-live-streaming-online-free/",$link,$m)) {
  $link=str_replace("---","-",$link);
  $link=str_replace("--","",$link);
  $link=str_replace("-&-","-",$link);
  echo $link;
  $link=str_replace($m[0],"live-stream?id=".$m[1],$link);

  }
  */
  //echo $link;
  //https://sportybite.top/brighton-&-hove-albion-nottingham-forest-live-stream?id=5477
  //https://sportybite.top/brighton-hove-albion-nottingham-forest-live-stream?id=5477
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m))
   $link = fixurl($m[1]);
  else
   $link="";
  //echo $link;
}
if (preg_match("/primasport\.one/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m))
   $link = $m[1];
  else
   $link="";
  //echo $link;
}
if (preg_match("/stream2watch\./",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://stream2watch.pk/',
  'Origin: https://stream2watch.pk'
  );
  //https://stream2watch.pk/895tv
  //https://stream2watch.pk/live/605tv
  $l=str_replace("stream2watch.pk","stream2watch.pk/live",$link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode('live score" src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0]; // https://dlhd.sx/tele/stream-62.php
  //echo $link;
}
if (preg_match("/dlhd\.sx/",$link)) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://dlhd.sx/',
  'Origin: https://dlhd.sx'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m))
   $link = $m[1];
  else
   $link="";
  //echo $link;
}
if (preg_match("/play\.stirilepe\.net/",$link)) {
 if ($flash <> "flash") $link .="|Referer=".urlencode("https://rds.live/");
}

if ($from=="tvonline") {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: https://tvonline123.net',
  'Connection: keep-alive',
  'Referer: https://tvonline123.net/');
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0";
  $cookie=$base_cookie."tvonline.txt";
  //?url=fia-formula-2
  $a = substr(strrchr($link, "/"), 1);
  $id=str_replace(".html","",$a);
  //echo $link;
  $t1=explode("url=",$link);
  $id=$t1[1];
  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_POST,1);
  //curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);

  //curl_setopt($ch, CURLOPT_URL, $link);
  //$h = curl_exec($ch);
  //echo $h;
  //preg_match("/method\=\"post\"\s+action\=\"([^\"]+)\"/",$h,$m);
  //https://www.tvonline123.com/tvlive/?url=digi-sport-1
  //https://www.tvonline123.com/tvlive/?url=digi-sport-1
  /*
  $t1=explode("url=",$link);
  $id=$t1[1];
  $l="https://tvhdonline.net/tv-live/?url=".$id;
  */
  //$l=$m[1];
  $l=$link;
  $post="tvgratis=".$id."&content-protector-submit.x=355&content-protector-submit.y=175";
  //echo $l;
  //$l="https://tvonline123.net/tvlive/?url=".$id;
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  $h = curl_exec($ch);
  //echo $h;
  //if (preg_match("/\&s\=(\d+)/",$h,$m)) {
  if (preg_match("/data-server-id\=\"(\d+)/",$h,$m)) {
  $l="https://www.tvonline123.com/player/".$id."/".$m[1];
  //https://www.tvonline123.com/player/digi-sport-1/161
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/file:\"([^\"]+)\"/",$h,$n)) {
   $link=$n[1];
   if ($flash <> "flash")
     $link .="|Referer=".urlencode("https://www.tvonline123.com/")."&Origin=".urlencode("https://www.tvonline123.com")."&User-Agent=".urlencode($ua);
  } else {
    $link="";
  }

  }
}
if ($from=="tvhdonline") {
//  'Origin: https://tvhdonline.org',
  function dF($s){
   $s1=urldecode(substr($s,0,strlen($s)-1));
   $t='';
   for($i=0;$i<strlen($s1);$i++) {
    $t .=chr(ord($s1[$i])- substr($s,strlen($s)-1,1));
   }
   return urldecode($t);
  }
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://manutv.org/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo urldecode($h);
  $t1=explode("dF('",$h);
  $t2=explode("')",$t1[1]);
  $h1=dF($t2[0]);
  //echo $h1;
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h1,$m) || preg_match("/window\.open\(\'([^\']+)\'/",$h,$m)) {
  $l=$m[1];
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/tvhdonline.org/",$l)) {
    if (preg_match("/LIVEAPP_URL\s*\=\s*\'([^\']+)/",$h,$s))
     $link=$s[1];
    else
     $link="";
  } else {
    $h2=urldecode($h);
    //echo $h2;
  $t1=explode("dF('",$h);
  $t2=explode("')",$t1[1]);
  $h1=dF($t2[0]);
  //echo $h1;
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h1,$m)) {
  $l=$m[1];
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  }
  //echo "enc=".mb_detect_encoding($h,"UTF-16");
    $h=urlencode($h);
    echo urldecode($h);
    //die();
    $h=str_replace("%00","",$h);
    //echo $h;
    $out = preg_replace_callback(
     "(%5Cx([0-9a-z]{2}))i",
     function($a) {return chr("0x".($a[1]));},
     $h
    );
    //echo $out;
   $z=urldecode($out);
   //echo $z;
   if(preg_match("/http[^\"]+\.m3u8?/",$z,$r))
    $link=$r[0];
   else
    $link="";
  }
  }
  curl_close($ch);
  if ($link && $flash <> "flash")
   $link .="|Referer=".urlencode("https://tvhdonline.org/");
}
if ($from == "time4tv") {
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate'
);
//https://ddolahdplay.xyz
//https://www.sports-stream.click
//https://sportybite.top/
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  $link="";
  preg_match("/allowfullscreen src\=\"([^\"]+)\"/i",$h,$z);
  $l=fixurl($z[1]);
  $l_1=$l;
  //echo $l."\n";;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  //echo $m[1]."\n";
  $l=fixurl($m[1],$l);
  //$l=str_replace("///","//",$l);
  //echo $l."\n";
  //die();
  if (preg_match("/player\.licenses4\.me/",$l)) {
    $link=$l;
    $l1="";
  } elseif (preg_match("/123ecast\./",$l)) {
    $link=$l;
    $l1="";
  } elseif (preg_match("/cricplay/",$l)) {
    $link=$l;
    $l1="";
  } elseif (preg_match("/extrafreetv/",$l)) {
  $chh=parse_url($l)['query'];
  $l2="https://tvpclive.com/embed/stream-".$chh.".php";
  curl_setopt($ch, CURLOPT_REFERER,"https://extrafreetv.com");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $link=fixurl($m[1]);
  //echo $link;
  } elseif (preg_match("/sportskart\./",$l)) {
  $chh=parse_url($l_1)['query'];
  $l2="https://sportskart.click/embed/stream-".$chh.".php";
  curl_setopt($ch, CURLOPT_REFERER,"https://sportskart.click");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  if (preg_match("/godzlive\.com|ddolahdplay\./",$l3)) {
   $link=$l3;
  } else {
   $link="";;
  }
  } elseif (preg_match("/tvpclive\./",$l)) {
  $chh=parse_url($l_1)['query'];
  $l2="https://tvpclive.com/embed/stream-".$chh.".php";
  curl_setopt($ch, CURLOPT_REFERER,"https://tvpclive.com");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  if (preg_match("/godzlive\.com|ddolahdplay\./",$l3)) {
   $link=$l3;
  } else {
   $link="";;
  }
  } elseif (preg_match("/sports\-stream\./",$l)) {
  curl_setopt($ch, CURLOPT_REFERER,"https://www.sports-stream.click");
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1],$l);
  curl_setopt($ch, CURLOPT_URL, $l3);
  $h = curl_exec($ch);
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $link=fixurl($m[1]);
  $l1="";
  } elseif (preg_match("/embedstream\./",$l)) { //https://extrafreetv.com/embed/espnusa/2.php //https://embedstream.me/espn-stream-1
  curl_setopt($ch, CURLOPT_REFERER,"https://extrafreetv.com");
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1],$l);
  //echo $l3;
  } elseif (preg_match("/1l1l\./",$l)) {
  curl_setopt($ch, CURLOPT_REFERER,"https://extrafreetv.com");
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  $t1=explode("fid='",$h);
  $t2=explode("'",$t1[1]);
  $fid=$t2[0];
  $l="https://jewelavid.com/embed2.php?player=desktop&live=".$fid;
  //echo $l;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://1l1l.to/',
  'Origin: https://1l1l.to');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://1l1l.to")."&Origin=".urlencode("https://1l1l.to");
  } elseif (preg_match("/wikisport\./",$l)) {
  //echo $l;
  curl_setopt($ch, CURLOPT_REFERER,"https://extrafreetv.com");
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  $t1=explode('fid="',$h);
  $t2=explode('"',$t1[1]);
  $fid=$t2[0];
  $l="https://fiveyardlab.com/wiki.php?player=desktop&live=".$fid;
  //echo $l;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://wikisport.click/',
  'Origin: https://wikisport.click');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://fiveyardlab.com/")."&Origin=".urlencode("https://fiveyardlab.com");
  } else {
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l1=fixurl($m[1]);
  }
  //echo $l1."\n";
  if (preg_match("/godzlive\.com/",$l1)) { ///////////////////////////////////
   $link=$l1;
  } elseif (preg_match("/daddylivehd\./",$l1)) { ////////////////////////////////
    //https://daddylivehd.sx/embed/stream-'+channel+'.php
   $chh=parse_url($l)['query'];
   $l2="https://daddylivehd.sx/embed/stream-".$chh.".php";
   curl_setopt($ch, CURLOPT_REFERER,"https://daddylivehd.sx");
   curl_setopt($ch, CURLOPT_URL, $l2);
   $h = curl_exec($ch);
   preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
   $l3=fixurl($m[1]); //https://livehdplay.ru/premiumtv/daddyhd.php?id=113
  curl_setopt($ch, CURLOPT_REFERER,"https://daddylivehd.sx");
  curl_setopt($ch, CURLOPT_URL, $l3);
  $h = curl_exec($ch);
  if (preg_match("/source\:\s*\'([^\']+)\'/",$h,$m))
    $link=$m[1];
  else
    $link="";
    //echo $link;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://livehdplay.ru/")."&Origin=".urlencode("https://livehdplay.ru");
  } elseif (preg_match("/maxsport\.one/",$l1)) { ////////////////////////////////
  $chh=parse_url($l)['query'];
  $l2="https://maxsport.one/".$chh;
  curl_setopt($ch, CURLOPT_REFERER,"https://extrafreetv.com");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  if (preg_match("/godzlive\.com|ddolahdplay\./",$l3)) {
   $link=$l3;
  } else {
   $link="";
  }
  } elseif (preg_match("/sportskart\.click/",$l1)) {  //////////////////////////////////////////////
  $chh=parse_url($l)['query'];
  $l2="https://sportskart.click/embed/stream-".$chh.".php";
  curl_setopt($ch, CURLOPT_REFERER,"https://sportskart.click");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  //echo $l3;
  if (preg_match("/godzlive\.com|ddolahdplay\./",$l3)) {
   $link=$l3;
  } else {
   $link="";;
  }
  } elseif (preg_match("/streamingnow\./",$l1)) {  ///////////////////////////////////////////
  $chh=parse_url($l)['query'];
  $l2="https://streamingnow.pro/tvon.php?hd=".$chh;

  curl_setopt($ch, CURLOPT_REFERER,"https://streamingnow.pro");
  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);

  $t1=explode('fid="',$h);
  $t2=explode('"',$t1[1]);
  $fid=$t2[0];
  $link="https://b4ucast.com/dhonka.php?player=desktop&live=".$fid;
  } elseif (preg_match("/lovesomecommunity\./",$l1)) {  ///////////////////////////////////////////
  curl_setopt($ch, CURLOPT_REFERER,"https://www.sports-stream.click/");
  curl_setopt($ch, CURLOPT_URL, $l1);
  $h = curl_exec($ch);
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://www.sports-stream.click")."&Origin=".urlencode("https://www.sports-stream.click");
  }

  curl_close($ch);
  //die();
}
///////////// link-ri
if (preg_match("/tutele\d+\.|tutlehd\.xyz/",$link)) {
  //https:///tutele1.net/onlinemo.php?a=40951

  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  //print_r ($m);
  $l=$m[1];
  $l = preg_replace("/\'.*?\'/",urlencode($link),$l);
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Referer: https://tutlehd.xyz/',
  'Accept-Encoding: deflate',
  'Origin: https://tutlehd.xyz'
  );
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  
  preg_match("/auth\"\:\"([^\"]+)/",$h,$a);
  $auth=$a[1];
  if (preg_match("/id\=\"crf__\" value\=\'([^\']+)\'/",$h,$m)) {
    $link=base64_decode($m[1]);
/////////////////////////

  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Referer: https://tutlehd.xyz/',
  'Accept-Encoding: deflate',
  'Xauth: '.$auth,
  'Origin: https://tutlehd.xyz'
  );
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  */
//////////////////////////////////////
    if ($flash <> "flash") {
      $link .="|Referer=".urlencode("https://tutlehd.xyz/")."&Origin=".urlencode("https://tutlehd.xyz")."&Xauth=".$auth;
      $link .="&User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0");
    }
  }
}
if (preg_match("/streamingnow\.|freeviplive\.|sons\-stream\.com|b5yucast\.com/",$link)) {
  //https://streamingnow.pro/stream.php?hd=20
  //https://freeviplive.com/stream.php?hd=20
  //https://freeviplive.com/tvon.php?hd=71
  //https://freeviplive.com/tvon.php?hd=301
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('fid="',$h);
  $t2=explode('"',$t1[1]);
  $fid=$t2[0];
  //https://b4ucast.com/dhonka2.php?player=
  $link="https://b4ucast.com/dhonka.php?player=desktop&live=".$fid;  // to next step
 //echo $link;
}
if (preg_match("/sportskart\.click/",$link)) { // to second link!
  //https://sportskart.click/embed/stream-501.php
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m))
    $link=fixurl($m[1]);
  else
    $link="";
}
if (preg_match("/sportsonline\./",parse_url($link)['host'])) {
  //https://sportsonline.so/channels/hd/hd1.php
  //echo $link;
  //$hw=1;    trebuie alta solutie.....
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  if (preg_match("/\<iframe.*?src\=\"((https?:)?\/\/[^\"]+)\"/i",$h,$m)) {
   $l=fixurl($m[1]);
  }
  //print_r ($m);
  //echo $l;
  $ref="https://".parse_url($l)['host'];
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://sportsonline.so/',
  'Origin: https://sportsonline.so'
  );
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  require_once("../filme/JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  if (preg_match("/var src\=\"([^\"]+)\"/",$out,$m))
   $link=$m[1];
  else
   $link="";
  if ($link && $flash <> "flash")
   $link=$link."|Referer=".urlencode($l)."&Origin=".urlencode($ref)."&User-Agent=".urlencode($ua);
}
if (preg_match("/iweb\.\w+\.shop/",parse_url($link)['host'])) {
  // https://iweb.ijttgbt.shop/embed/e8GplfEKWQOE
  // iweb.hctnzec.shop
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  require_once("../filme/JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  if (preg_match("/source\:\s*\"([^\"]+)\"/",$out,$m))
    $link=$m[1];
  else
    $link="";
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://iweb.ijttgbt.shop/")."&Origin=".urlencode("https://iweb.ijttgbt.shop");
}
if (preg_match("/truyenxalo\./",parse_url($link)['host'])) {
  //https://vwrc.truyenxalo.com/embed/ysu7YZKDyF5L
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ref="https://".parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  require_once("../filme/JavaScriptUnpacker.php");
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  if (preg_match("/source\:\s*\"([^\"]+)\"/",$out,$m))
    $link=$m[1];
  else
    $link="";
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode($ref."/")."&Origin=".urlencode($ref);
}
if (preg_match("/livehdplay\.ru/",parse_url($link)['host'])) {
  // $l="https://livehdplay.ru/maxsport.php?id=cnmyfeed21";
  //https://olalivehdplay.ru/premiumtv/daddylivehd.php?id=194
  //echo $link;
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0";
  if (preg_match("/daddylive/",$link))
   $ref="https://dlhd.sx";
  else
   $ref="https://primasport.one";
   $ref="https://1.dlhd.sx/";
  $host="https://".parse_url($link)['host'];
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$ref.'/',
  'Origin: '.$ref
  );
  //print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/[^\/]source:\s*\'([^\']+)/",$h,$m))
   $link=trim($m[1]);
  else
   $link="";
   //echo $link;
   //$link="https://webhdrus.onlinehdhls.ru/lb/premium36/index.m3u8";
   //$link="https://salamus2023.onlinehdhls.ru/ddh1/premium34/playlist.m3u8";
   //$link="https://salamus2023.onlinehdhls.ru/ddh1/premium34/tracks-v1a1/mono.m3u8";
   //$ref="https://claplivehdplay.ru";
   //$link="https://salamus2023.onlinehdhls.ru/ddh2/premium36/tracks-v1a1/mono.m3u8";
   $ref="https://claplivehdplay.ru";
  $head=array('User-Agent: '.$ua,
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: '.$ref,
  'Connection: keep-alive',
  'Referer: '.$ref.'/',
  );
  $head1=array('User-Agent: '.$ua,
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: https://claplivehdplay.ru',
'Connection: keep-alive',
'Referer: https://claplivehdplay.ru/',
);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/location:\s*(.+)/i",$h,$m))
   $link=trim($m[1]);
  //echo $h;
  //echo $h;
  $t1=explode("#EXTM3U",$h);
  $h=$t1[1];
  //echo $h;
  //echo $link;
  //$link=get_max_res($h,$link);

  //$link="https://salamus2023.onlinehdhls.ru/ddh1/premium34/playlist.m3u8";
  //$link="https://salamus2023.onlinehdhls.ru/ddh2/premium34/tracks-v1a1/mono.m3u8";
  //$link="https://salamus2023.onlinehdhls.ru/ddh2/premium36/playlist.m3u8";
   $host="https://claplivehdplay.ru";
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode($host."/")."&Origin=".urlencode($host)."&User-Agent=".urlencode($ua);
    //$link=$link."&Sec-Fetch-Dest=empty&Sec-Fetch-Mode=cors&Sec-Fetch-Site=cross-site";
}
if (preg_match("/tvpclive\./",parse_url($link)['host'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://tvpclive.com/'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/\<iframe.*?src\=\"([^\"]+)\"/i",$h,$m);
  $l3=fixurl($m[1]);
  if (preg_match("/godzlive\.com|ddolahdplay\./",$l3)) {
   $link=$l3;
  } else {
   $link="";;
  }
}
//echo $link;
if (preg_match("/lovesomecommunity\./",parse_url($link)['host'])) {  ///////////////////////////////////////////
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Referer: https://primasport.one/',
  'Accept-Encoding: deflate'
  );
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://lovesomecommunity.com")."&Origin=".urlencode("https://lovesomecommunity.com");
}
if (preg_match("/123ecast\.xyz/",parse_url($link)['host'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/source\:\s*\'([^\']+)\'/",$h,$m))
    $link=$m[1];
  else
    $link="";
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://123ecast.xyz")."&Origin=".urlencode("https://123ecast.xyz");
}
if (preg_match("/cricplay/",parse_url($link)['host'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: null',
  'Referer: https://fsro.io');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //curl_close($ch);
  $t1=explode('fid="',$h);
  $t2=explode('"',$t1[1]);
  $fid=$t2[0];
  $l="https://lovesomecommunity.com/embedcr.php?player=desktop&live=".$fid;
  //echo $l;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://cricplay2.xyz/',
  'Origin: https://cricplay2.xyz');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://cricplay2.xyz")."&Origin=".urlencode("https://cricplay2.xyz");
}
if (preg_match("/player\.licenses4\.me/",parse_url($link)['host'])) {
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: null',
  'Referer: https://fsro.io');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/source\:\s*\"([^\"]+)\"/",$h,$m))
   $link=$m[1];
  if ($link && $flash <> "flash")
    $link=$link."|Referer=".urlencode("https://player.licenses4.me")."&Origin=".urlencode("https://player.licenses4.me");
}

if (preg_match("/ddolahdplay\./",parse_url($link)['host'])) {

//https://ddolahdplay.xyz
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://sportskart.click/',
  'Origin: https://sportskart.click');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/source\:\'([^\']+)\'/",$h,$m))
    $link=$m[1];
  if ($link && $flash<>"flash")
   $link .="|Referer=".urlencode("https://ddolahdplay.xyz/")."&Origin=".urlencode("https://ddolahdplay.xyz");
}
if (preg_match("/godzlive\.com|b\ducast\.com/",parse_url($link)['host'])) {
  $ref="https://".parse_url($link)['host'];
  //$ref="https://b4yucast.com";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://b5yucast.com/');
  //echo $link;
  //b5yucast.com
  //$link=str_replace("dhonka.php","dhonka2.php",$link);
  //https://b4ucast.com/dhonka2.php?player=desktop&live=bbtsp1
  //https://b4ucast.com/dhonka2.php?player=desktop&live=bbtsp1
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, trim($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("return([",$h);
  $t2=explode("]",$t1[1]);
  $t3=explode(",",$t2[0]);
  $x=implode("",$t3);
  //echo $x;
  $x=str_replace('"',"",$x);
  $x=str_replace("\\","",$x);
  $x=str_replace("////","//",$x);
  //echo $x;
  $link=$x;
  if ($link && $flash<>"flash")
   $link .="|Referer=".urlencode($ref."/")."&Origin=".urlencode($ref);
}
if ($from == "rds.live") {
$host="https://".parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  //echo $h;
  curl_close($ch);
  $t1=explode('nonce":"',$h);
  $t2=explode('"',$t1[1]);
  $nonce=$t2[0];
  $t1=explode('div id="player" class="content" data-id="',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0";
  $l=$host."/wp-admin/admin-ajax.php";
  $post="action=show_player&id=".$id."&nonce=".$nonce;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$link,
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'X-Requested-With: XMLHttpRequest',
  'Content-Length: '.strlen($post),
  'Origin: '.$host);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
      curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h1 = curl_exec($ch);
      curl_close($ch);
      $t1=explode("src: '",$h1);
      $t2=explode("'",$t1[1]);
      $link=$t2[0];
      if ($flash <> "flash") $link .="|Referer=".urlencode($host."/");
}
if (strpos($link,"streamwat.ch") !== false) {
      $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);
      $t1=explode('playM3u8("',$h);
      $t2=explode('"',$t1[1]);
      $link=$t2[0];
}
if (strpos($link,"facebook") !== false) {
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
preg_match($pattern,$link,$m);
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
}
if (preg_match("/media\.cms\.protvplus\.ro/",$link)) {
//$link="https://media.cms.protvplus.ro/embed/9w1VHN18dnM?autoplay=any";
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head = array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Origin: http://protvplus.ro',
   'Referer: https://protvplus.ro/'
  );
//echo urldecode($head[3]);
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $h=str_replace("\\","",$h);
  $t1=explode('src":"',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  //echo $l;
// https://cmero-ott-live-web-avod-sec.ssl.cdn.cra.cz/b_gEKFdTF9H_BLKjZ8_IWQ==,1623321957/channels/cme-ro-pro2/playlist-live_lq-live_mq-live_hq.m3u8
// https://cmero-ott-live-web-avod-sec.ssl.cdn.cra.cz/b_gEKFdTF9H_BLKjZ8_IWQ==,1623321957/channels/cme-ro-pro2/playlist/rum/live_mq.m3u8?offsetSeconds=0&url=0
$t1=explode("/playlist",$l);
$l=$t1[0]."/playlist/rum/live_hq.m3u8?offsetSeconds=0&url=0";
$link=$l;
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
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
} else {
  $link=$l;
}
} else {
  $link=$l;
}
*/
if ($flash <> "flash")
 $link=$link."|Referer=".urlencode("https://media.cms.protvplus.ro")."&Origin=".urlencode("https://media.cms.protvplus.ro");
}
if (preg_match("/looksport\.1616\.ro/",$link)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
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
  $t1=explode('source src="',$h);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
}
if (preg_match("/bypassiptv\.eu/",$link)) {
  if ($flash <> "flash")
  $link=$link."|Referer=".urlencode("https://romanialive.online")."&Origin=".urlencode("https://romanialive.online");
}
if (preg_match("/www\.exclusivtv\.ro/",$link)) {
 $l="https://www.exclusivtv.ro/";
 $h=file_get_contents($l);
 // https://www.youtube.com/watch?v=YOlHZXwpL10
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $h, $match)) {
  $id = $match[2];
  $link="https://youtube.com/embed/".$id;
    $link1=youtube_nou1($link);
    //die();
    //$link="";
    if (!$link1)
      $link1=youtube($link);
    $link=$link1;
}
}
if (preg_match("/realiptv\.eu/",$link)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://realiptv.eu");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    $t1=explode(">",$h);
    $t2=explode("<",$t1[1]);
    $link=$t2[0];
}
if (preg_match("/clients\.your\-server\.de/",$link)) {
  // https://static.240.188.251.148.clients.your-server.de:2083/live/canale/live/2.m3u8
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0";
  $ua     =   $_SERVER['HTTP_USER_AGENT'];
  $ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
    if ($flash <> "flash") {
     $link=$link."|Origin=".urlencode("https://canale.live")."&Referer=".urlencode("https://canale.live");
     $link=$link."&User-Agent=".urlencode($ua);
    }
}
if (preg_match("/canale1\.live/",$link)) {
    $ua="Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://canale.live");
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    //$h=file_get_contents($link);
    if (preg_match_all("/location\:\s*(.+)/i",$h,$m))
     $link=trim($m[1][count($m[1])-1]);
    else {
     $t1=explode("source: '",$h);
     $t2=explode("'",$t1[1]);
     $link=$t2[0];
    }
    if ($flash <> "flash") {
     $link=$link."|Origin=".urlencode("https://canale.live")."&Referer=".urlencode("https://canale.live");
     $link=$link."&User-Agent=".urlencode($ua);
    }
     //$link=trim($m[1]);
}
if (preg_match("/tvhd-online1\.com/",$link)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://tvhd-online.com");
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    if (preg_match("/location\:\s*(.+)/i",$h,$m))
     $link=trim($m[1]);
}
if (preg_match("/albacarolinatv\.ro/",$link)) {
 $h=file_get_contents($link);
 $t1=explode('<iframe src="',$h);
 $t2=explode('"',$t1[1]);
 $link=str_replace("&#038;","&",$t2[0]);
}
if (strpos($link,"live1.cdn.tv8.md/TV7") !== false) {
    $l="https://tv8.md/wp-json/tv8/v1/live-url";
    $l="https://api.tv8.md/v1/live";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    $p=json_decode($h,1);
    //$link=$p['url'];
    $link=$p['liveUrl'];
}
if (strpos($link,"jurnaltv.md/JurnalTV") !== false) {
    $l="https://www.jurnaltv.md/page/live";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    $link=str_between($h,'source src="','"');
    if (!$link) $link=str_between($h,'file:"','"');
}
if (strpos($link,"www.b1tv.ro/wp-json/strawberry") !== false) {
    $l="https://www.b1tv.ro/wp-json/strawberry/v1/live?v=".time();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    $link=urldecode(json_decode($h,1)['url']);
}
///////////////////////////////////////////////
if ($from=="sultanovic") {
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
 if (preg_match("/\?\w+\=(.+)/",$link,$m)) {
  $link=$m[1];
 } else {
  $host="https://".parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"http://sultanovic.net");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/videoLink \= \'/",$h)) {
   $t1=explode("videoLink = '",$h);
   $t2=explode("'",$t1[1]);
   $link=$host.$t2[0];
   if ($flash <> "flash")
    $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host);
  } else {
    $link="";
  }
 }
}
if ($from=="tvcanale") {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $host="https://".parse_url($link)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://tvcanale.live");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/source:\s*\"([^\"]+)\"/",$h,$m)) {
   $link=$m[1];
   if ($flash <> "flash")
    $link=$link."|Referer=".urlencode($host)."&Origin=".urlencode($host)."&User-Agent=".urlencode($ua);
  } else
   $link="";
   
}
if ($from=="canale.live") {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  $l="https://canale.live/embdr/".$link."/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://canale.live/reclama/ftv/?id=100");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  if (preg_match("/\<iframe/",$html)) {
  $t1=explode("<iframe",$html);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"https://canale.live/reclama/ftv/?id=100");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  }
  if (preg_match("/parseInt\(atob\(value/",$html)) {
  $t1=explode('[',$html);
  $t2=explode(';',$t1[1]);
  $e="\$c=[".$t2[0].";";
  eval ($e);
  $t1=explode("replace(/\D/g,'')) -",$html);
  $t2=explode(")",$t1[1]);
  $n=$t2[0];

  //echo $n."\n";
  $out="";
  for ($k=0;$k<count($c);$k++) {
    $p=base64_decode($c[$k]);
    $p=preg_replace("/\D/","",$p);
    //echo $p." ";
    $out .=chr($p-$n);
  }
  } else {
   $out=$html;
  }
  //echo $out;
  //echo $e;
  // source: '
  preg_match_all("/(\/\s+)?source\:\s+\'(.*?)\'/",$out,$m);
  //print_r ($m);
  for ($z=0;$z<count($m[2]);$z++) {
   if ($m[0][$z][0] != "/") {
     $link=$m[2][$z];
     break;
   }
  }
  if (preg_match("/uload\.ru|olacast\.live|netwrk\.ru\.com|networkbest\.ru\.com/",$link) && $flash <> "flash") {
    $link="http://127.0.0.1:8080/scripts/tv/uload.php?file=".$link."|Referer=".urlencode("https://canale.live");
  } else  if (preg_match("/uload\.ru|olacast\.live|netwrk\.ru\.com|networkbest\.ru\.com/",$link) && $flash == "flash") {
    $link="uload.php?file=".$link;
  } elseif ($link[0]=="/") {
    $link="https://canale.live".$link;
  }
}
if ($from=="ustvgo") {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
  //echo $link;
  // https://ustvgo.tv/acc-network/
  //$link="https://ustvgo.tv/player.php?stream=ABC";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://ustvgo.tv");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  //echo $html;
  // iframe src='/player.php?stream=ACCN'
  $t1=explode("stream=",$html);
  $t2=explode("'",$t1[1]);
  $stream=$t2[0];
  $l="https://ustvgo.tv/player.php?stream=".$stream;
  curl_setopt($ch, CURLOPT_URL, $l);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $link="";
  $t1=explode("hls_src='",$html);
  $t2=explode("'",$t1[1]);
  $link=$t2[0];
  $html="";
  if (preg_match("/clappr\.php\?stream\=/",$html)) {
   $t1=explode("/clappr.php?stream=",$html);
   $t2=explode("'",$t1[1]);
   $post="stream=".$t2[0];
   $l="https://ustvgo.tv/data.php";
   $head= array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'X-Requested-With: XMLHttpRequest',
   'Content-Length: '.strlen($post),
   'Origin: https://ustvgo.tv',
   'Connection: keep-alive',
   'Referer: https://ustvgo.tv/clappr.php?stream=ACCN');
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
  $link = curl_exec($ch);
  curl_close($ch);
  }
}
if ($from=="primaplay") {
//echo $link;
  $link=str_replace("/play/","/show/",$link);
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.primaplay.ro");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  //die();
  $t1=explode('class="container-video">',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h1 = curl_exec($ch);
  curl_close($ch);
  //echo $h1;
  $t1=explode('data-playurl","',$h1);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
}
if ($from=="stream4free") {
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:87.0) Gecko/20100101 Firefox/87.0";
 $head=array('Cookie: 1f3372654d375ef621c5014fed5588ff=74cc38feeba3ca1a1f8f1a7b1ebc2a95');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.stream4free.live");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/http.+\.m3u8/",$h,$m))
     $link=trim($m[0]);
  if ($link && $flash <> "flash") {
    $link .="|Referer=".urlencode("https://www.stream4free.live")."&Origin=".urlencode("https://www.stream4free.live");
    $link .="&User-Agent=".urlencode($ua);
  }
}
if ($from=="tvrlive") {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/http.+\.m3u8/",$h,$m))
     $l=trim($m[0]);
     //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
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
} else {
  $link=$l;
}
} else {
  $link=$l;
}
}
if ($from=="tvhd-online") {
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:84.0) Gecko/20100101 Firefox/84.0";
//echo $link;
//echo time();
// 1609488001
// 1609531716
//$link = preg_replace("/\d{10}/",time(),$link);
//echo $link;
  //$link="https://tvhd-online.com/playertv/1609488001/progold.html";
  // https://tvhd-online.com/playertv/1609484401/progold.html
  $l="https://tvhd-online.com";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://tvhd-online.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
      $t1=explode("PHPSESSID=",$h);
      $t2=explode(";",$t1[1]);
      $ses=$t2[0];
$head=array('Origin: https://tvhd-online.com',
'Cookie: PHPSESSID='.$ses);
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://tvhd-online.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $out="";
  $t1=explode("source: '",$html);
  $t2=explode("'",$t1[1]);
  $l2=$t2[0];
  if (!$l2) {
   $t1=explode('source: "',$html);
   $t2=explode('"',$t1[1]);
   $l2=$t2[0];
  }
  if (strpos($l2,"http") !== false)
   $out=$l2;
  if (preg_match("/rcs\-rds\.ro1/",$out)) {
  $t1=explode("id=",$link);
  $id=$t1[1];
  $l="https://realiptv.eu/digidata.php?id=".$id;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://realiptv.eu");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode("<div",$h);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[1]);
  $out=$t3[0];
  }
  $link=$out;
  //$link="https://v-e-00-cdn.rcs-rds.ro/dash/68/68.mpd";
  if ($flash <> "flash") {
   if (preg_match("/cmero\-ott\-live\-sec\.ssl\.cdn\.cra\.cz/",$link))
    $link=$link."|Origin=".urlencode("https://media.cms.protvplus.ro")."&Referer=".urlencode("https://media.cms.protvplus.ro");
   if (preg_match("/tvhd\-online\.com\/iptvlive/",$link))
    $link=$link."|Cookie=".urlencode("PHPSESSID=".$ses)."&Referer=".urlencode("https://tvhd-online.com")."&Origin=".urlencode("https://tvhd-online.com")."&User-Agent=".urlencode($ua);
  }
}
if ($from=="teleon") {
//echo $link;
//$link="http://player.teleon.tv/ro/channel/a7-tv-ro";
$ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $link="";
  //echo $html;
  $t1=explode('<source',$html);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l2=$t3[0];
  if (strpos($l2,"http") !== false)
   $link=$l2;
}
if ($from=="ustream") {
  // https://www.ustream.to/live/antena-1-romanesti/cf617b06961f0b35cfc2582012bf749
  require_once("../filme/JavaScriptUnpacker.php");

  $jsu = new JavaScriptUnpacker();
  $t1=explode("/",$link);
  //echo $link;
  $id=$t1[3];
  $id=str_replace("-live","",$id);
  //$id="b446a8b8fdd785ef1ea91aab57e751b2";
  //$id="40dc1e70446506473d32a7cd81d67d20";
  //$id="kanal-d-romanesti";
  $l="https://www.ustream.to/stream?id=".$id;
  //$l="https://blog1199.blogspot.com/page.html?id=rtv-romania&url=aHR0cHMlM0ElMkYlMkZ3d3cudXN0cmVhbS50byUyRnN0cmVhbV9vcmlnaW5hbC5waHAlM0Z0b2tlbiUzRGNkYzRkYWQ2ZTc1NGQzNTM0OTM4YThmZjgyMjNjYjkzJTI2aWQlM0RydHYtcm9tYW5pYSUyNg==";
  //echo $l;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:85.0) Gecko/20100101 Firefox/85.0";
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: '.$link);
  //echo $l;
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
  //echo $h;


  //echo $out;
  $t1=explode('<iframe id="iframe',$h);
  $out = $jsu->Unpack($t1[1]);
  $t1=explode('url=',$out);
  $t2=explode('"',$t1[1]);
  $l=urldecode(base64_decode($t2[0]));
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
  //echo $h;
  $t1=explode("var x_first_ua",$h);
  $out = $jsu->Unpack($t1[1]);
  $t1=explode('var file_name="',$out);
  $t2=explode('"',$t1[1]);
  $fn=$t2[0];
  $t1=explode('var jdtk="',$out);
  $t2=explode('"',$t1[1]);
  $token=$t2[0];
  // https://hls.ustream.to/Antena-1-Romania.m3u8?token=e5b-52f-f5f-f97-973-5fc-342-f0c-25b-f20-88d-8bd-d6a-113-7d3-62a-6a9-6f8-6c1-09c-039-4
  $link = "https://hls.ustream.to/".$fn."?token=".$token;
  if ($token && $flash <> "flash")
   $link=$link."|Referer=".urlencode("https://dotmeraz.blogspot.com")."&Origin=".urlencode("https://dotmeraz.blogspot.com");
}
if ($from=="b1tv") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  $t1=explode('https://app.dejacast.com/player',$html);
  $t2=explode('"',$t1[1]);
  $link= 'https://app.dejacast.com/player'.$t2[0];
  curl_setopt($ch, CURLOPT_URL, $link);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $t1=explode('data-playurl","',$html);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
}
if ($from=="tvhd") {
  $cookie=$base_cookie."tvhd.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $html = curl_exec($ch);
  curl_close($ch);
  $t1=explode('source: "',$html);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if (strpos($link,"http") === false) $link="http:".$link;
$ad1="";
if (file_exists($cookie)) {
  $h=file_get_contents($cookie);
  if (preg_match("/PHPSESSID\s+([a-zA-Z0-9]+)/",$h,$m)) {
   $ad1="&Cookie=".urlencode("PHPSESSID=".$m[1]);
}
}
$ad="User-Agent=".urlencode("Mozilla/5.0(Linux;Android 10.1.2) MXPlayer");
//$ad .="&Accept=".urlencode("*/*")."&Accept-Language=".urlencode("ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2");
//$ad .="&Accept-Encoding=".urlencode("gzip, deflate, br");
$ad .="&X-CustomHeader=videojs";
$ad ."&Origin=".urlencode("http://tvhd-online.com");
//$ad .="&Connection=".urlencode("keep-alive");
$ad .="Referer=".urlencode("http://tvhd-online.com").$ad1;
//   if ($link && $flash != "flash")
//     $link=$link."|".$ad;
/*
  $link="http://tvhd-online.com/vod/live/225.mp4";



$ad ."Origin=".urlencode("http://tvhd-online.com");
//$ad .="&Connection=".urlencode("keep-alive");
$ad .="&Referer=".urlencode("http://tvhd-online.com");
//$ad.="&Connection=".urlencode("keep-alive");
   if ($link && $flash != "flash")
     $link=$link."|".$ad;
*/
   if ($link && $flash != "flash") {
    $link="http://127.0.0.1:8080/scripts/tv/redirect.php?file=".$link;
    //$link=$link."|".$ad;
   } else
    $link="redirect.php?file=".$link;
}
if ($from=="digilive") {
//echo $link;
  if (!preg_match("/clients\.your\-server\.de/",$link)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("videoLink = '",$h);
  $t2=explode("'",$t1[1]);
  $link=$t2[0];
  }
}
if ($from=="protvplus") {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $head = array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Referer: https://protvplus.ro',
   'Origin: https://protvplus.ro'
  );
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $h=str_replace("\\","",$h);
  //$t1=explode("/embed",$h);
  //$t2=explode('"',$t1[1]);
  //$l="https://media.cms.protvplus.ro/embed".$t2[0]."?autoplay=any";
  //$l="https://media.cms.protvplus.ro/embed/7HijmOZ8Ouc?autoplay=any";
  /*
  $t1=explode('iframe data-src="',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  */
  preg_match("/embedUrl\"\:\s*\"([^\"]+)\"/",$h,$m);
  $l=$m[1];
  //echo $l;
  //https://media.cms.protvplus.ro/embed/GVDpaTJKFcK?autoplay=1
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $h=str_replace("\\","",$h);
  //echo $h;
  $t1=explode('src":"',$h);
  //$t1=explode('DASH":[{"src":"',$h);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);

  //echo $h;

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

//curl_setopt($ch, CURLOPT_URL, $link);
//$h = curl_exec($ch);
//echo $h;
curl_close($ch);
//$link=$l;
//$link="https://cmero-ott-vod-web-prep-sec.ssl.cdn.cra.cz/K3MenhEnnM5nqiAaCxE4xg==,1698066020/vod_cmero/_definst_/0202/2815/rum-sd0-sd1-sd2-sd3-sd4-hd1-hd2-TwRAFeuj.smil/chunklist_b1155072.m3u8";
//$link="https://cmero-ott-vod-prep-prot.ssl.cdn.cra.cz/vod_cmero/_definst_/0202/2815/rum-sd0-sd1-sd2-sd3-sd4-hd1-hd2-axinom-taKVQLlA.smil/playlist.m3u8";
if ($link && $flash <> "flash")
 $link .="|Referer=".urlencode("https://media.cms.protvplus.ro/")."&Origin=".urlencode("https://media.cms.protvplus.ro");
}
if ($from=="profunzime") {
    //echo $link;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    if (strpos($h,"embed/") !== false) {
    $t1=explode("embed/",$h);
    $t2=explode('"',$t1[1]);
    // https://inprofunzime.protv.md/embed/2679589.html
    $link="https://inprofunzime.protv.md/embed/".$t2[0];
    // https://inprofunzime.protv.md/embed/2679589.html
    //echo "\n".$link."\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_REFERER,"https://inprofunzime.protv.md");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    //echo $h1;
    $t1=explode('source  src="',$h1);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    } else
    $link="";
}
if ($from=="protvmd") {
$l="https://protv.md/api/article-page";
$post="article_id=".$link;
$head=array('Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: http://inpro.protv.md/emisiuni',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post).'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  $p=json_decode($html,1);
  //print_r ($p);
  //die();
    if ($p["item"]["cover_type"] == "video") {
    //$t1=explode("embed/",$h);
    //$t2=explode('"',$t1[1]);
    //$link="http://protv.md/embed/".$t2[0];
    //echo $link;
    $id=$p["item"]["cover_link"];
    $link="http://protv.md/embed/".$id.".html";
    //echo $link;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    $t1=explode('source  src="',$h1);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    } else
    $link="";
}
if ($from=="moldova") {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $cookie=$base_cookie."hdpopcorns.dat";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    $t1=explode("loadSource('",$h);
    $t2=explode("'",$t1[1]);
    $link="http://www.trm.md".$t2[0];
    //$h=file_get_contents($cookie);
    //preg_match("/cf_clearance\s+(\S+)/",$h,$m);
    //print_r ($m);
    //die();
    //if ($link && $flash != "flash")
    //$link=$link."|Cookie=".urlencode("cf_clearance=").urlencode($m[1])."&User-Agent=".urlencode($ua);
    //$head=array("Cookie: cf_clearance=".$m[1]);
    //echo $link;
    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    //die();
    */
}
if ($from=="flc") {
  $token=file_get_contents($base_fav."flc.txt");
  $l="http://api.folclor.platform24.tv/v2/channels/".$link."/stream?access_token=".$token."&format=json";
  //$l="http://api.folclor.platform24.tv/v2/channels/".$link."/stream?access_token=".$token."&format=json&type=http";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, "http://api.folclor.platform24.tv");
    //curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    $r=json_decode($h,1);
    //print_r ($r);
    $link=$r["hls"];
    //$link=$r["http"];
}
if ($from=="flc1") {
  $token=file_get_contents($base_pass."tvplay.txt");
  $l="http://hd4all.ml/d/flc.php?file=".$link."&s=".$token;
  //echo $l;
    $h=file_get_contents($l);
    $link=trim($h);
}
if ($from=="digifree") {
  $l="http://balancer2.digi24.ro/?scope=".$link."&type=hls&quality=hq&outputFormat=jsonp&callback=jsonp_callback_1";
  //echo $l;
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:85.0) Gecko/20100101 Firefox/85.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://digiapis.rcs-rds.ro',
'Origin: https://digiapis.rcs-rds.ro',
'Connection: keep-alive');
  $l="http://balancer2.digi24.ro/streamer/make_key.php";
$proxy="86.120.79.84:4145";

  $key=file_get_contents($l);
  //echo $h;
  $proxy="86.123.166.109:8080";

/*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_REFERER, "http://207.180.233.100:2539");
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    //curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $key = curl_exec($ch);
    curl_close($ch);
    */
    //echo $h;
    //die();
    //echo $key;
    //die();                 // abr
  $l="http://balancer2.digi24.ro/streamer.php?&scope=".$link."&key=".$key."&outputFormat=json&type=abr&quality=hq";  //&is=4&ns=digi24&pe=site&s=site&sn=digi24.ro&p=browser&pd=windows
  //$l="https://balancer2.digi24.ro/streamer.php?&scope=digisport1desk&key=".$key."&outputFormat=json&type=abr&quality=hq&is=1&ns=digisport1&pe=site&s=site&sn=digisport.ro&p=browser&pd=windows";
//$l="http://balancer2.digi24.ro/streamer.php?&scope=digisport1&key=a16112d32a5dbc53267fadd3a70f2c4f&outputFormat=json&type=abr&quality=hq";
  $proxy="79.115.245.227:8080";
  $proxy="86.123.166.109:8080";
  $proxy="86.120.79.84:4145";
  $proxy="86.120.79.84:4145";
  //$proxy="5.15.52.65:8080";
  //$proxy="5.12.136.46:8080";
  //$proxy="86.125.112.230:57373";
  //$l="https://balancer2.digi24.ro/streamer.php?&scope=digisport1desk&key=".$key."&outputFormat=json&type=abr&quality=hq&is=1&ns=digisport1&pe=site&s=site&sn=digisport.ro&p=browser&pd=windows";
  //$proxy="86.124.162.105:9090";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:85.0) Gecko/20100101 Firefox/85.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_REFERER, "http://207.180.233.100:2539");
    if ($link <> "digi24") {
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
    }
    //curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    $r=json_decode($h,1);
    //print_r ($r);
    //$h=str_replace("\/","/",$h);
    //$link=str_between($h,'file":"','"');
    $link=$r['file'];
    $host=parse_url($link)['host'];
    $link=str_replace($host,"edge-ar.rcs-rds.ro",$link);
    //echo $link;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_REFERER, "https://digiapis.rcs-rds.ro");
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
    //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $x = curl_exec($ch);
    curl_close($ch);
    echo $x;

    //$link=preg_replace("/edge\d+\.rcs\-rds\.ro/","82.76.40.81",$link);
    //$link=preg_replace("/edge\d+\.rdsnet\.ro/","82.76.40.81",$link);
    //$link=str_replace($host,"edge30.rcs-rds.ro",$link);
    //if ($link) $link="http:".$link;
}
if ($from=="neterra") {
$file=$base_cookie."neterra.dat";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("|",$h);
  $user=trim($t1[0]);
  $pass=trim($t1[1]);
} else {

    //$link="http://207.180.233.100:2539/player.php?id=".$link;
    $l="http://207.180.233.100:2539/m3u8.php?id=".$link;
    //echo $link;
    //http://207.180.233.100:2539/player.php?id=50
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, "http://207.180.233.100:2539");
    curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    $t1=explode("username=",$h);
    $t2=explode("&",$t1[1]);
    $user=$t2[0];
    $t1=explode("password=",$h);
    $t2=explode("&",$t1[1]);
    $pass=$t2[0];
    if ($user && $pass) file_put_contents($file,$user."|".$pass);
    //echo $h;
    //die();
    //$t1=explode("loadSource('",$h);
    //$t2=explode("'",$t1[1]);
    //$link=$t2[0];

}
    $link="http://207.180.233.100:2539/streaming/clients_live.php?extension=m3u8&username=".$user."&password=".$pass."&stream=".$link."&type=hls";
    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, "http://207.180.233.100:2539");
    curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $h = curl_exec($ch);
    curl_close($ch);
    echo $h;
    */
}
if ($from=="tvrplus") {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    $t1=explode("sources:",$h);
    $t2=explode("src: '",$t1[1]);
    $t3=explode("'",$t2[1]);
    $link=$t3[0];
}
if ($from=="tvrplus_y") {
 $link1=youtube_nou("https://www.youtube.com/watch?v=".$link);
 if ($link1)
  $link=$link1;
 else
  $link=youtube("https://www.youtube.com/watch?v=".$link);
}
if ($from=="b98") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $t1=explode('file: "',$html);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
}
if ($from=="protvstiri") {
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
$ua="Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0";
$head=array("X-Requested-With: XMLHttpRequest",
"Content-Type: application/x-www-form-urlencoded; charset=UTF-8");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("video_id: '",$h);
  $t2=explode("'",$t1[1]);
  $id=$t2[0];
  $l="https://stirileprotv.ro/lbin/ajax/drm_config.php";
  $post="video_id".$id;
  $post="video_id=".$id."&section_id=&subsite_id=&to_call=VIDEO_PAGE&player_container=div_article_player";
  //echo $post;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://stirileprotv.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  if (preg_match("/http.+\.mp4/",$html,$m)) {
   $link=$m[0];
  } else
   $link="";
  $link=str_replace("https","http",$link);
}
if ($from=="europalibera") {
//echo $link;
//$link="https://romania.europalibera.org/a/interviu-geoana---despre-r%C4%83zboi-comunicarea-%C8%99i-liderii-rom%C3%A2niei/31817643.html";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<video',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($l));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
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
} else {
  $link=$l;
}
} else {
  $link=$l;
}
}
if ($from=="libertatea") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (strpos($h,"https://www.youtube.com/embed/") !== false) {
    $t1=explode('https://www.youtube.com/embed/',$h);
    $t2=explode('"',$t1[1]);
    $l1='https://www.youtube.com/embed/'.$t2[0];
    $link=youtube($l1);
  } else {
  $t1=explode("https://player.libertatea.ro",$h);
  $t2=explode('&image=',$t1[1]);
  $l= urldecode("https://player.libertatea.ro".$t2[0]);
  //echo $l;
  $link="";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($l));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("hls_base_url_js + '/",$h);
  $t2=explode("'",$t1[1]);
  if ($t2[0]) $link="https://ringier-video-cache.distinct.ro:443/static3.libertatea.ro/_definst_/uploads/tx_lsuvideo/".$t2[0];
  if (!$link) {
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.m3u8))/', $h, $m)) {
  $link=$m[1];
  }
  }
 }
}
if ($from=="bzi") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, urldecode($link));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.mp4))/', $h, $m);
  $link=$m[1];
}
if ($from=="arconaitv") {
//echo $link;
require_once("../filme/JavaScriptUnpacker.php");
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
  //$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
//$ua="Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0";
$ua=$user_agent;
//$link="https://www.arconaitv.us/stream.php?id=168";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://www.arconaitv.us/");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h = curl_exec($ch);
      curl_close($ch);
      //$h=file_get_contents($link);
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.m3u8))/', $out, $m);
  $link=$m[1];
  //$link="https://videoserver1.org/live/Wg6C1lCuC5yHa7SzCIihbQ/1575117837/974006f79db4e097ce136f1b66b9770d.m3u8";
  //$link="https://videoserver2.org/live/NIqqJisElXJ2p-wIp8aEBA/1542023953/65f3a12d1dc82d6cd205f62101ee521c.m3u8";
  //echo $link;
  //$link=str_replace("videoserver1.org","videoserver2.org",$link);
  $head=array('X-CustomHeader: videojs','Origin: https://www.arconaitv.us');
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: gzip, deflate, br',
'Referer: https://www.arconaitv.us/stream.php?id=168',
'X-CustomHeader: videojs',
'Connection: keep-alive');
$origin="https://www.arconaitv.us";
$ad="User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:70.0) Gecko/20100101 Firefox/70.0");
//$ad .="&Accept=".urlencode("*/*")."&Accept-Language=".urlencode("ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2");
//$ad .="&Accept-Encoding=".urlencode("gzip, deflate, br");
$ad .="&X-CustomHeader=videojs";
$ad ."&Origin=".urlencode("https://www.arconaitv.us");
//$ad .="&Connection=".urlencode("keep-alive");
$ad .="&Referer=".urlencode("https://www.arconaitv.us");

   if ($link && $flash != "flash")
     $link=$link."|".$ad;
      /*
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch,CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt ($ch, CURLINFO_HEADER_OUT, true);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      //curl_setopt($ch, CURLOPT_REFERER, "https://www.arconaitv.us/");
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
      //die();
      */
  //$link=str_replace("videoserver2.org","videoserver1.org",$link);
  //$flash="flash";
  //$link=str_replace("https","http",$link);
}
/*
if ($from=="tvmobil") {
//echo $link;
//die();
//http://tvpemobil.net/wap/tv-online.php?categorie=generaliste&canal=1&sid=7e8962323978b6939b9d45549cd84543
//http://tvpemobil.net/wap/tv-online.php?categorie=generaliste&canal=1&sid=7e8962323978b6939b9d45549cd84543
if (strpos($link,"televiziune.live") !== false) {
$ua     =   $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."tvmobil.dat";
//echo $link;
//$link=str_replace("2.ts","HBO.ts",$link);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_HEADER,1);
      //curl_setopt($ch, CURLOPT_NOBODY,1);
      curl_setopt($ch,CURLOPT_REFERER,"http://televiziune.live/");
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      //curl_setopt($ch, CURLOPT_REFERER, "http://hqq.tv/");
      $h = curl_exec($ch);
      curl_close($ch);
if ($h) {
$t1=explode("Location:",$h);
$t2=explode("\n",$t1[1]);
$l1=trim($t2[0]);
if ($l1) $link=$l1;
}
}
}
*/
if ($from=="digi24") {
//$cookie=$base_cookie."digi.dat";
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://www.digi-online.ro");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $t1=explode('id="video-player-cfg">',$html);
  $t2=explode('</script',$t1[1]);
  $h=trim($t2[0]);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $link = $r['new-info']['meta']['versions']['720p.mp4'];
  //die();
//http://s1.digi24.ro/onedb/transcode/5794a369682ccfd2588b4567.480p.mp4
//$out=str_replace("\\","",str_between($html,'480p.mp4":"','"'));
//$link=str_replace("https","http",$out);
//$link=str_replace("v2.iw.ro","v1.iw.ro",$link);
//$link=str_replace("v4.iw.ro","v1.iw.ro",$link);
//$link=preg_replace("/v\d+\.iw/","v1.iw",$link);
}
if ($from=="antenaplay") {
$id1 = substr(strrchr($link, "/"), 1);
$cookie=$base_cookie."antena.dat";
//https://antenaplay.ro/cont/device?ts=1492160441438&sui=a23b2eb7056b69a4e96C
//Cookie: uniquCM=5d18c14501f0f655a707e44d0cbfaa75; aplay_device_id=22d0678e848093a18e4c825fae842522d35e2a2f%2B197445705475ed61; aplay_device_type=b7b288846c64fab4812475723c4f60abdcf05b0f%2BComputer; aplay_device_name=7ce7cdd90f5e8b6fd5294190a9ec3312859af006%2BWindows+XP+-+Mozilla+Firefox; laravel_session=69d787adc3596719638fcfbed5305dbf64d65b35%2B0QWww870NtktAPhYFXtSqpqGDVALIb1OsTVxkvQP; session_payload=30ccc37ab311bb69299da894a00c9b530674353a%2B%2BsXD5EMSUw78BXqFZvC0%2FXOqCn%2F%2F1VqPUAv0LKwMCQ8zn6%2F%2BzTbKbk2P772vZCx%2BS8GVzknK8DSWF0fl1NwigYMW68eIV8J%2FDevCnts1BHVhxLomxmN2Pncm7qJvLDrcfoMFNDhGSQ44WezGCyWaaeWxOANoHoczFY6IyqkhlWyRTVMG20ss014B9MbkEUrDqhEp01IygwaiimJNEHjdFoQX92aU%2BNr9DtOqxFK3%2FKczMNKb%2FDE46TyWOAvNzZJy3fRnI%2FyjfN1vj2B938RuQTD751l4QlDcaxmYj3hksgmhBk4qOANHmXZ9pHBjlKh7u%2FWYwbAu4pRexrkZNDt%2B9n7WoivpGPbSyELRlOL4M1Jx6OfI5gBMWVYiPpm5%2B%2FOY2ZxHkR7Ed2gdDWEyCW4gvHXYFCuNdZm1pdTdDG89Wvk8IdCrEVtlSSkZdE92DUJFFkm2zUMRJNSaiZ%2B2R7F8ZUDd35Pe7dl21MkrZH8Xd1IBaaOoZdEEOvb%2FwyXGst6xKHeeJNWuL%2BHqcqybdMgXIhc%2BVYRsMbtzl%2Fo65B5OES6jNznclFuHdWn%2BX%2F71vE7JI4K09anelLkWmH9pueBJ3OPZYOpmDh1Zb%2BZM0IuFSpesDxqW33YAMR0JZfoNOOV4SV6lKsyZO01EU9nxofnRgrLGtlPejglKGUZQPP08eJurmblnqrBVtvHbPCtJZwjv3sUeg1wss0A3MI5U6KiJELquHcBfuy9yL8ralmZ8Wm%2B775xY55K2PmccHzJWekIUEURMyzmXDY%2BsvsXxlXxDVdfJzYgBMIFUq80uWwHpoGDrpDqZiRWFwWUgd0apQwy9%2FgsBOkE%2Bus2j0qzecTZOaYD32klaBjicUIzlqjiC78Kbz2h9JRA%2FqdH0NkXc57gk4YOjDHWJAePVdC4Xa2%2FBzMEvBwb0qYqG1UVSTx5dT2o%3D; _ga=GA1.2.899447015.1493927492; PHPSESSID=e3o10dgag6c6auen5t46aca8a0; ap_userUnique=a23b2eb7056b69a4e96C; _gid=GA1.2.1475486294.1495390762; uniquCM=2443ad54fe5f02494bc4a0745c7d8f4c

//https://antenaplay.ro/v/3OSFc3zFkuh
$l="https://antenaplay.ro/".$link;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0');
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html=curl_exec($ch);
  curl_close($ch);

$uhash=str_between($html,'uhash" value="','"');
$utype=str_between($html,'utype" value="','"');
$device=str_between($html,'device" value="','"');
$guid=str_between($html,'guid" value="','"');
$guidid=str_between($html,'guidid" value="','"');

//$link="https://ivm.antenaplay.ro/js/embed_aplay_fullshow.js?id=".$id."&width=700&height=500&autoplay=1&stretching=fill&div_id=playerHolder&_=";
//$link=file_get_contents("".$link."");
$link="https://ivm.antenaplay.ro/js/embed_aplay_fullshow.js?id=".$id1."&width=700&height=500&&autoplay=1&stretching=fill&div_id=playerHolder&uhash=".$uhash."&device=".$device."&guidid=".$guidid."&video_type=".$utype."&video_free=no&_=";
//https://ivm.antenaplay.ro/js/embed_aplay_fullshow.js?id=3OSFc3zFkuh&width=790&height=444.5694991558807&autoplay=1&stretching=fill&div_id=playerHolder&uhash=a23b2eb7056b69a4e96C&device=197445705475ed61&guidid=68826&video_type=VOD&video_free=no&_=1495390789079

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0');
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html=curl_exec($ch);
  curl_close($ch);


$link=str_between($html,'ivmSourceFile.src = "','"');
}
if ($from=="digi") {
   $id=$link;
   if (strpos($id,"timisoara") !== false) $id=str_replace("timisoara","timis",$id);
   $l="http://balancer.digi24.ro/?scope=".$id."&type=hls&quality=hq&outputFormat=jsonp&callback=jsonp_callback_1";
   $h=file_get_contents($l);
   $h=str_replace("\/","/",$h);
   $link=str_between($h,'file":"','"');
   if (strpos("http:",$link) === false && $link) $link="http:".$link;
}
if ($from == "digisport") {
  $head=array('Cookie: m2digisportro=0');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
$x=str_between($html,'<script type="text/template">','</script');
//echo $x;
$r=json_decode($x,1);

$out=$r["new-info"]["meta"]["source"];

$link=str_replace("https","http",$out);
$link=preg_replace("/v\d+\.iw/","v1.iw",$link);
}
if ($from=="cabinet") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(mp4|m3u8)))/', $html, $m))
   $link=$m[1];
  else
   $link="";
}
if ($from=="privesceu") {
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');

  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
  $link="";
  $t1=explode('contentUrl":"',$html);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
/*
  $id=str_between($html,"widget/live/",'"');
  $l="http://www.privesc.eu/api/live/".$id;
echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');

  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
echo $h;
*/
//$link=trim(str_between($h,'hls":"','"'));
   if ($link && $flash != "flash")
     $link=$link."|Referer=".urlencode("https://www.privesc.eu");
}
if ($from=="epoch") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');

  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
  $link=str_between($html,"file: '","'");
}
if ($from=="protv") {
$l="https://player.protv-vidnt.com/api/get_embed_token/";
$post='{"media_id":"'.$link.'","poster":"https://avod.static.protv.ro/cust/protv/www/protvmms-o2meD1-nnq6.5729751.poster.HD.jpg","_csrf":"2e0716f4e98fb4e712a8f34d0192bde1","account":"protv"}';
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://protvplus.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$x=json_decode($html,1);
//print_r ($x);
$token=$x["data"]["token"];
$l="https://protvplus.ro/play/".$link."/?embtoken=".$token;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://protvplus.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$x=json_decode($html,1);
//print_r ($x);
$man=$x["media"][0]["link"];
$l="https://player.protv-vidnt.com/api/decrypt_link/";
$post='{"link":"'.$man.'.","_csrf":"2e0716f4e98fb4e712a8f34d0192bde1","account":"protv"}';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://protvplus.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$x=json_decode($html,1);
//print_r ($x);
$link=$x["data"]["playback_url"];
}
if ($from == "gazw") {
if (file_exists($base_pass."dev.txt"))
  $dev=file_get_contents($base_pass."dev.txt");
else {
$filename=$base_pass."tvplay.txt";
$pass=file_get_contents($filename);
$lp="http://hd4all.ml/d/gazv.php?c=".$pass;
$dev = file_get_contents("".$lp."");
}
$link="http://iptvmac.cf:8080/live".$dev."/".$link.".m3u8";
$out2="http://iptvmac.cf:8080/live".$dev."/".$link.".ts";
if (!$dev) $link="";
}
if (preg_match("/vk\.com|vkontakte\.ru/",$link)) {
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
  $ch = curl_init($link);
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
  if (preg_match_all("/hls\":\"([http|https][\.\d\w\-\.\/\\\:\=\?\&\#\%\_\,]*)\"/",$html,$m))   {
    $link=$m[1][count($m[1])-1];
    //print_r ($m);
  } else
    $link="";
}
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $link, $match)) {
  $id = $match[2];
  $l1 = "https://www.youtube.com/watch?v=".$id;
  //$html   = file_get_contents($link);
  //echo $l1;
  $link=youtube_nou1($l1);
  if (!$link)
   $link=youtube($l1);
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

$out=$link;
//$flash="flash";
//$out="http://127.0.0.1:8080/scripts/filme/lava.m3u8|Referer=https%3A%2F%2Ftr.vidlink.org&Origin=https%3A%2F%2Ftr.vidlink.org";

if ($flash=="mpc") {
  $mpc=trim(file_get_contents($base_pass."vlc.txt"));

  $ua=$_SERVER['HTTP_USER_AGENT'];
  $t1=explode("|",$out);
  $movie=$t1[0];

  parse_str(urldecode($t1[1]),$q);

  if (isset($q['Referer']))
   $host="https://".parse_url($q['Referer'])['host'];
  elseif (isset($q['Origin']))
   $host="https://".parse_url($q['Referer'])['host'];

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
   $out1=' --user-agent="'.$ua.'"';
  }
  $out="";
  foreach ($q as $key =>$value) {
   $out .='"'.$key.": ".$value.'",';
   //echo $out;
  }
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
  //echo '<script>window.close();</script>';
  //echo '<script>setTimeout(function(){ window.close(); }, 500);</script>';
  //pclose(popen($c,"r"));

} elseif ($flash == "mp") {
$mod="direct";
if (preg_match("/\.m3u8/",$out)) {
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
$l=$out;
//echo $out."\n";
$base1=str_replace(strrchr($l, "/"),"/",$l);
$base2=getSiteHost($l);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
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
  //print_r ($m);
  if (isset($m[1][0])) {
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $out=$base.$pl[$key_max];
  }
}
}
if (preg_match("/dotto\.edvr\.ro/",$link))
  $out=$out."|User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0");
//echo $out;
}
if (strpos($out,"cmn.digitalcable.ro") !== false && $out)
 $out=$out."|User-Agent=".urlencode("Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0")."&Origin=".urlencode("https://www.akta.ro")."&Referer=".urlencode("https://www.akta.ro");
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
//$c="intent:".$out."#Intent;package=com.mxtech.videoplayer.".$mx.";action=android.intent.action.view();S.title=".urlencode($title).";end";
if (strpos($out,"ONETV") !== false)
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";
if (preg_match("/v1\.iw\.ro/",$out)) //digisport ????
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";
///////////////////
if ($hw==1) //sportsonline
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";

echo $c;
die();

} else {
$out=str_replace("&amp;","&",$out);
if (strpos($out,"realitatea") !== false)
  $type="m3u8";
else {
if (strpos($out,"m3u8") !== false)
   $type="m3u8";
else
   $type="mp4";
}
$title=str_replace('"',"'",$title);
//header('Access-Control-Allow-Origin: *');
//,
            //onXhrOpen: function(xhr, url) {
            //    xhr.setRequestHeader("X-CustomHeader", "videojs");
            //}
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
<link rel="stylesheet" href="../player.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script src="../jwplayer.js"></script>

</HEAD>
<BODY>
<div id="container"></div>
<script type="text/javascript">
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": [{
"title": "'.preg_replace("/\n|\r|\"/","",$title).'",
"sources": [{"file": "'.$out.'", "type": "'.$type.'"
}]
}],
"height": $(document).height(),
"width": $(document).width(),
"skin": {
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"title": "'.preg_replace("/\n|\r|\"/","",$title).'",
"abouttext": "'.preg_replace("/\n|\r|\"/","",$title).'",
"autostart": true,
"fallback": false,
"wmode": "direct",
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
</BODY>
</HTML>
';
}
?>
