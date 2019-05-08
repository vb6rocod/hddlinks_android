<?php
//set_time_limit(0);
error_reporting(0);
include ("../common.php");
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
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
function youtube($file) {
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
  //echo $html;
  $html = str_between($html,'ytplayer.config = ',';ytplayer.load');
  $parts = json_decode($html,1);
  //preg_match('#config = {(?P<out>.*)};#im', $html, $out);
  //$parts  = json_decode('{'.$out['out'].'}', true);
  if ($parts['args']['livestream']== 1) {
    $r=$parts['args']['hlsvp'];

      $ua="Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $r);
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

	//$r=urldecode($path.http_build_query($output));
	if (isset($output['sig'])) {
		$signature=($output['sig']);

	} else {
  $sA="";
  $s=$output["s"];
  //echo $s;
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
}
    $r=$output['url']."&signature=".$signature;

return $r;
}
} else
  return "";
}
//http://gradajoven.es/spicenew.php
//http://edge3.spicetvnetwork.de:1935/live/_definst_/mp4:spicetv/ro/6tv.stream/chunklist_w2087458837.m3u8?c=176&u=52409&e=1398753453&t=298944a96a9161b2300ae3ae072b85f4&d=android&i=1.30
//http://edge1.spicetvnetwork.de:1935/live/_definst_/mp4:spicetv/ro/6tv.streamchunklist_w2087458837.m3u8?c=176&u=52560&e=1398777448&t=3869972b307e53bfd2e048f093fd5f1c&d=site&i=Android%2C+Safari
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
}
//$link="https://cdn.drm.protv.ro/avod/2019/01/08/man:a121b208f4d2ceb191be29119e3a23b4:ef955898665ba80da15610d4c7b04804-7d017cb346b477351d97fe79378b111c.ism/man:a121b208f4d2ceb191be29119e3a23b4:ef955898665ba80da15610d4c7b04804-7d017cb346b477351d97fe79378b111c.m3u8";
//$link="http://89.136.209.30:1935/liveedge/TVRMOLDOVA.stream/playlist.m3u8";
//$link=urldecode("https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3Dr_d4ryn9UsA&title=Gaming%20Music%20Radio%20%E2%9A%A1%2024/7%20NCS%20Live%20Stream%20%E2%9A%A1%20Trap,%20Chill,%20Electro,%20Dubstep,%20Future%20Bass,%20EDM");
//$mod="direct";
if ($from=="profunzime") {
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
    $link="http://inprofunzime.protv.md/embed/".$t2[0];
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
if ($from=="protvmd") {
$l="http://protv.md/api/article-page";
$post="article_id=-".$link;
$head=array('Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: gzip, deflate',
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
    $t1=explode("video.src = '",$h);
    $t2=explode("'",$t1[1]);
    $link="http://www.trm.md".$t2[0];
}
if ($from=="flc") {
  $token=file_get_contents($base_fav."flc.txt");
  $l="http://api.folclor.platform24.tv/v2/channels/".$link."/stream?access_token=".$token."&format=json";
  $l="http://api.folclor.platform24.tv/v2/channels/".$link."/stream?access_token=".$token."&format=json&type=http";
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
    //$link=$r["hls"];
    $link=$r["http"];
}
if ($from=="digifree") {
  $l="http://balancer.digi24.ro/?scope=".$link."&type=hls&quality=hq&outputFormat=jsonp&callback=jsonp_callback_1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER, "http://207.180.233.100:2539");
    //curl_setopt($ch, CURLOPT_HEADER,1);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    //echo $h;
    $h=str_replace("\/","/",$h);
    $link=str_between($h,'file":"','"');
    $link=preg_replace("/edge\d+\.rcs\-rds\.ro/","82.76.40.81",$link);
    $link=preg_replace("/edge\d+\.rdsnet\.ro/","82.76.40.81",$link);
    if ($link) $link="http:".$link;

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
 $link=youtube("https://www.youtube.com/watch?v=".$link);
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
  //http://vid2.stirileprotv.ro/2019/01/12/62014229-2.mp4
  $t1=explode('sources: [',$html);
  $t2=explode('src: "',$t1[1]);
  $t3=explode('"',$t2[1]);
  $link=$t3[0];
  $link=str_replace("https","http",$link);
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
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  //echo $out;
  preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.m3u8))/', $out, $m);
  $link=$m[1];
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
/*
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $link);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
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
      echo $h;
      die();
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
  //die();
//http://s1.digi24.ro/onedb/transcode/5794a369682ccfd2588b4567.480p.mp4
$out=str_replace("\\","",str_between($html,'480p.mp4":"','"'));
$link=str_replace("https","http",$out);
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
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
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
  $link=str_between($html,'source src="','"');
}
if ($from=="privesceu") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');

  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
  $id=str_between($html,"widget/live/",'"');
  $l="http://www.privesc.eu/api/live/".$id;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');

  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
//echo $h;
$link=trim(str_between($h,'hls":"','"'));
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
if (strpos($link,"watch?v=") !== false) {
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $link, $match)) {
  $id = $match[2];
  $l1 = "https://www.youtube.com/watch?v=".$id;
  //$html   = file_get_contents($link);
}
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $html = str_between($html,'ytplayer.config = ',';ytplayer.load');
  $parts = json_decode($html,1);
  //print_r ($parts['args']);
  //echo urldecode($parts['args']["adaptive_fmts"]);
$link=$parts['args']['hlsvp'];

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
$out=$link;
//$flash="flash";

if ($from=="arconaitv1") {
header('Accept: */*');
header('X-CustomHeader: videojs');
header('Origin: https://www.arconaitv.us');
header('Referer: https://www.arconaitv.us/stream.php?id=157');
header("Location: $out");
} else if ($flash=="mpc") {
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$out.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
} elseif ($flash == "mp") {
$mod="direct";
if (preg_match("/\.m3u8/",$out)) {
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
$l=$out;
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
  $max_res=max($m[1]);
  $arr_max=array_keys($m[1], $max_res);
  $key_max=$arr_max[0];
  $out=$base.$pl[$key_max];
}
}
}
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
//$c="intent:".$out."#Intent;package=com.mxtech.videoplayer.".$mx.";action=android.intent.action.view();S.title=".urlencode($title).";end";
if (strpos($out,"ONETV") !== false)
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";

echo $c;
die();
} elseif ($flash == "direct") {
if ($mod=="direct") {
header('Content-type: application/vnd.apple.mpegURL');
header("Location: $out");
} else {
$out1="#EXTINF:-1, ".$title."\r\n".$out;
//$out1="#EXTM3U"."\r\n"."#EXTINF:-1, ".$title."\r\n".$out;
$out1="http://127.0.0.1:8080/scripts/subs/out.m3u";
header('Content-type: application/vnd.apple.mpegURL');
header("Location: $out1");
}
} elseif ($flash == "chrome") {
$mod="direct";
if (!preg_match("/http/",$out)) $mod="indirect";
if ($mod=="direct") {
$c="intent:".$out."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
} else {
$out1="#EXTM3U"."\r\n"."#EXTINF:-1, ".$title."\r\n".$out;
file_put_contents($base_sub."out.m3u",$out1);
$out1="http://127.0.0.1:8080/scripts/subs/out.m3u";
  $c="intent:".$out1."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header('Content-type: application/vnd.apple.mpegURL');
  header("Location: $c");
}
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
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="../jwplayer.js"></script>

</HEAD>
<BODY>
<div id="container"></div>
<script type="text/javascript">
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": [{
"sources": [{"file": "'.$out.'", "type": "'.$type.'"
}]
}],
"height": $(document).height(),
"width": $(document).width(),
"aspectratio": "16:9",
"stretching": "exactfit",
"skin": {
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"autostart": true,
"fallback": false,
"wmode": "direct",
"stagevideo": true
});
player.addButton(
  //This portion is what designates the graphic used for the button
  "//icons.jwplayer.com/icons/white/download.svg",
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
