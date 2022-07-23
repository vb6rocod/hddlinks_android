<!doctype html>
<?php
include ("../common.php");
error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists($base_pass."debug.txt"))
 $debug=true;
else
 $debug=false;
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
$tit=unfix_t(urldecode($_GET["title"]));
$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
$ep_title=prep_tit($ep_title);
$year=$_GET["year"];
if ($tip=="movie") {
$tit2="";
} else {
if ($ep_title)
   $tit2=" - ".$sez."x".$ep." ".$ep_title;
else
   $tit2=" - ".$sez."x".$ep;
$tip="series";
}
$imdbid="";

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title><?php echo $tit.$tit2; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
function openlink1(link) {
  link1=document.getElementById('file').value;
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
}
function openlink(link) {
  on();
  var request =  new XMLHttpRequest();
  link1=document.getElementById('file').value;
  var the_data = "link=" + link1 + "&title=" + link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      <?php
      if ($debug) echo "document.getElementById('debug').innerHTML = request.responseText.match(/http.+#/g);"."\r\n";
      ?>
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function changeserver(s,t) {
  document.getElementById('server').innerHTML = s;
  document.getElementById('file').value=t;
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
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
</head>
<body>
<a href='' id='mytest1'></a>
<?php
echo '<h2>'.$tit.$tit2.'</H2>';
echo '<BR>';
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
$cookie=$base_cookie."onionplay.txt";
//echo $link;
//$link="https://onionplay.is/movies/zack-snyders-justice-league-2021/";
 $link=str_replace("/watch-","/",$link);
 $link=str_replace("-onionplay","",$link);
// https://onionplay.is/movies/star-wars-the-rise-of-skywalker-2019/
// https://onionplay.is/movies/watch-star-wars-the-rise-of-skywalker-2019-onionplay/
//$link="https://onionplay.club/watch-baggio-the-divine-ponytail-2021-online-ads-free";
//$link="https://onionplay.se/movies/alien-resurrection-1997/";
$host=parse_url($link)['host'];
$ref="https://".$host;
if (file_exists($cookie)) {
$x=file_get_contents($cookie);
//echo $x;
//file_put_contents($base_cookie."onionplay.txt",$x);
$y=preg_quote($host,"/");
//unlink ($cookie);
if (preg_match("/".$y."	\w+	\/	\w+	\d+	cf_clearance	([\w\-\.\_]+)/",$x,$m))
 $cc=trim($m[1]);
else
 $cc="";
}
$firefox = $base_pass."firefox.txt";
if (file_exists($firefox))
 $ua=file_get_contents($firefox);
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0";

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$ref."\r\n"
  )
);
//echo $link;
//$context = stream_context_create($opts);
//$html=@file_get_contents($link,false,$context);
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Cookie: _ga=GA1.1.238192412.1599210100; _js_datr=UiDGXydiuVrQoP7WfPCjEPFp; __atuvc=0%7C41%2C0%7C42%2C0%7C43%2C0%7C44%2C1%7C45; _ym_uid=1582124103637524699; _ym_d=1635325499; _ga_NHDEZ3LMR5=GS1.1.1608507498.1.0.1608507498.60; _hjid=5dad3d29-5449-41ee-b759-461662a02d3c; cX_P=kixru44z4zqi4e91; sc_is_visitor_unique=rx11849134.1609874042.F52CF4D32A424F9BB6E9BC378CA70AF9.2.2.2.2.2.2.2.2.2-12096247.1609499921.3.3.3.3.2.1.1.1.1; __gads=ID=9404faa27f9171f8-22d8b4b15dba004c:T=1612557705:S=ALNI_MbHte3wNPzuvHG76vr9bLfqn21_3A; _ga_4Y92J21ZFR=GS1.1.1630144471.3.1.1630144497.0; _ga_9ZBLTKLKK0=GS1.1.1615106758.1.1.1615108141.0; _ga_5S4R5BDE8S=GS1.1.1633897355.2.0.1633897365.0; _ga_0WGNHNHYZS=GS1.1.1615711605.1.0.1615711609.0; _ga_PVLYD1EH1L=GS1.1.1616408660.1.0.1616408668.0; HstCfa4529398=1618562419370; HstCla4529398=1619980849748; HstCmu4529398=1618562419370; HstPn4529398=1; HstPt4529398=2; HstCnv4529398=2; HstCns4529398=2; ai_user=5CFRa|2021-04-25T08:45:14.198Z; _clck=1w9rrjr; cto_bundle=lnKgWF9EazRTJTJGQ0ZIbldHMVdRZWxiSTZLcE5CZnNlQ1I1V2hpWTl4UTlYcnc2JTJGWTdvSVYyRFF3bGRTOUlwZnFpcWs5QUdhUmlVNEQ4VDNOSzNiaGtQNTg2QWhma1NNNWVnc0MycENIeHFqV3F4UVhoM0VzTjNyVk55eUV4Tk5CTTQ5bDhyZXRoYUV3MFZLV0p1RHpqZUhTYXZTSlFLdGZtSTNnZUFvcWpEZUZTRXRjJTNE; HstCfa4533164=1621364947339; HstCla4533164=1621364947339; HstCmu4533164=1621364947339; HstPn4533164=1; HstPt4533164=1; HstCnv4533164=1; HstCns4533164=1; _ga_0CLRKRJFKB=GS1.1.1621364947.1.0.1621364949.0; __utma=111872281.238192412.1599210100.1627208922.1627208922.1; __utmz=111872281.1627208922.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); csm-hit=tb:s-XAFQYFK0V4EPBB1DJH2N|1630147890762&t:1630147899688&adb:adblk_no; HstCfa4518200=1630264093671; HstCla4518200=1630580782689; HstCmu4518200=1630264093671; HstPn4518200=1; HstPt4518200=11; HstCnv4518200=6; HstCns4518200=6; _ga_TNN38RF6S1=GS1.1.1636455736.1.1.1636455754.0; _ga_JJ8C3FEJHM=GS1.1.1637323126.1.1.1637323343.0; dom3ic8zudi28v8lr6fgphwffqoz0j6c=17b722ad-50b5-4b00-88fe-10bc19ad3b5c%3A1%3A1',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');
  $ch = curl_init($link);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
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
  $out="";
  $t1=explode("= [",$html);
  $t2=explode("]",$t1[1]);
  $e="\$c=array(".$t2[0].");";
  eval ($e);
  //print_r ($c);
  $t1=explode("parseInt(value) -",$html);
  $t2=explode(")",$t1[1]);
  $d=$t2[0];
  for ($k=0;$k<count($c);$k++) {
   $out .=chr($c[$k]-$d);
  }
  //echo $out;
  //echo $html;
  //$t1=explode("data-post='",$html);
  //$t2=explode("'",$t1[1]);
  //$id=$t2[0];
preg_match_all("/data\-post\=\'(\d+)\'\s+data\-nume\=\'(\d+)\'/",$out.$html,$m);
//print_r ($m);
//preg_match_all("/postid\-(\d+)/",$html,$m);
//print_r ($m);
$l="https://".$host."/wp-admin/admin-ajax.php";
$ch = curl_init($l);
$source=array();
for ($k=0;$k<count($m[1]);$k++) {
$id=$m[1][$k];
$nume=$m[2][$k];
//$nume="2";
$post="action=doo_player_ajax&post=".$id."&nume=".$nume."&type=movie";
//echo $post;
//die();
$opts = array(
  'http'=>array(
    'method'=>"POST",
    'content' => $post,
    'header'=>"User-Agent: ".$ua."\r\n".
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$ref."\r\n"
  )
);
//$context = stream_context_create($opts);
//$h=@file_get_contents($l,false,$context);
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post).'',
'Origin: https://'.$host,
'Connection: keep-alive',
'Referer: https://'.$host.'/',
'Cookie: _ga=GA1.1.238192412.1599210100; _js_datr=UiDGXydiuVrQoP7WfPCjEPFp; __atuvc=0%7C41%2C0%7C42%2C0%7C43%2C0%7C44%2C1%7C45; _ym_uid=1582124103637524699; _ym_d=1635325499; _ga_NHDEZ3LMR5=GS1.1.1608507498.1.0.1608507498.60; _hjid=5dad3d29-5449-41ee-b759-461662a02d3c; cX_P=kixru44z4zqi4e91; sc_is_visitor_unique=rx11849134.1609874042.F52CF4D32A424F9BB6E9BC378CA70AF9.2.2.2.2.2.2.2.2.2-12096247.1609499921.3.3.3.3.2.1.1.1.1; __gads=ID=9404faa27f9171f8-22d8b4b15dba004c:T=1612557705:S=ALNI_MbHte3wNPzuvHG76vr9bLfqn21_3A; _ga_4Y92J21ZFR=GS1.1.1630144471.3.1.1630144497.0; _ga_9ZBLTKLKK0=GS1.1.1615106758.1.1.1615108141.0; _ga_5S4R5BDE8S=GS1.1.1633897355.2.0.1633897365.0; _ga_0WGNHNHYZS=GS1.1.1615711605.1.0.1615711609.0; _ga_PVLYD1EH1L=GS1.1.1616408660.1.0.1616408668.0; HstCfa4529398=1618562419370; HstCla4529398=1619980849748; HstCmu4529398=1618562419370; HstPn4529398=1; HstPt4529398=2; HstCnv4529398=2; HstCns4529398=2; ai_user=5CFRa|2021-04-25T08:45:14.198Z; _clck=1w9rrjr; cto_bundle=lnKgWF9EazRTJTJGQ0ZIbldHMVdRZWxiSTZLcE5CZnNlQ1I1V2hpWTl4UTlYcnc2JTJGWTdvSVYyRFF3bGRTOUlwZnFpcWs5QUdhUmlVNEQ4VDNOSzNiaGtQNTg2QWhma1NNNWVnc0MycENIeHFqV3F4UVhoM0VzTjNyVk55eUV4Tk5CTTQ5bDhyZXRoYUV3MFZLV0p1RHpqZUhTYXZTSlFLdGZtSTNnZUFvcWpEZUZTRXRjJTNE; HstCfa4533164=1621364947339; HstCla4533164=1621364947339; HstCmu4533164=1621364947339; HstPn4533164=1; HstPt4533164=1; HstCnv4533164=1; HstCns4533164=1; _ga_0CLRKRJFKB=GS1.1.1621364947.1.0.1621364949.0; __utma=111872281.238192412.1599210100.1627208922.1627208922.1; __utmz=111872281.1627208922.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); csm-hit=tb:s-XAFQYFK0V4EPBB1DJH2N|1630147890762&t:1630147899688&adb:adblk_no; HstCfa4518200=1630264093671; HstCla4518200=1630580782689; HstCmu4518200=1630264093671; HstPn4518200=1; HstPt4518200=11; HstCnv4518200=6; HstCns4518200=6; _ga_TNN38RF6S1=GS1.1.1636455736.1.1.1636455754.0; _ga_JJ8C3FEJHM=GS1.1.1637323126.1.1.1637323343.0; dom3ic8zudi28v8lr6fgphwffqoz0j6c=17b722ad-50b5-4b00-88fe-10bc19ad3b5c%3A1%3A1',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');


  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //echo $h."\n";
  $l="";
  $x=json_decode($h,1);
  if (isset($x["embed_url"])) {
    $l=$x["embed_url"];
  } else {
    $t1=explode("src='",$h);
    $t2=explode("'",$t1[1]);
    $l=$t2[0];
  }
  $source[]=$l;
}
curl_close ($ch);
$r=array();
require_once("JavaScriptUnpacker.php");
$jsu = new JavaScriptUnpacker();
//print_r ($source);
  //$l="https://go.onionplay.is/iAt";

  //$l="https://go.onionplay.is/nNY";
  //echo $l;
//$l="https://www.onionbox.org/movie/2021/G/tt5034838.js";
// https://go.onionbox.org/movie/2021/G/Godzilla.Vs..Kong.2021.mp4
// https://fembed.stream/lUf
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://'.$host.'/',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');

  $ch = curl_init();
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);

  for ($n=0;$n<count($source);$n++) {
  curl_setopt($ch, CURLOPT_URL, $source[$n]);
  $h = curl_exec($ch);

  //echo $h."\n";
  //die();
  $out="";
  $h1=unjuice($h);


  $out = $jsu->Unpack($h1);
  //echo $out;
//echo $h;

  $t1=explode("= [",$h);
  $t2=explode("]",$t1[1]);
  $e="\$c=array(".$t2[0].");";
  eval ($e);
  //print_r ($c);
  $t1=explode("parseInt(value) -",$h);
  $t2=explode(")",$t1[1]);
  $d=$t2[0];
  //echo $d;
  //die();

  for ($k=0;$k<count($c);$k++) {
   $out .=chr($c[$k]-$d);
  }
  $t1=explode('redirect").attr("href","',$out.$h);
  $t2=explode('"',$t1[2]);
  $l=$t2[0];
  //echo $l;
  /*
  if (!$out) {
  $t1=explode('redirect").attr("href","',$h);
  $t2=explode('"',$t1[2]);
  $l=$t2[0];
  }
  */
  if (!$l) {
  $t1=explode("window.location.replace('",$h);
  $t2=explode("'",$t1[1]);
  $l=$t2[0];
  }
  if (!$l) {
  $t1=explode('<section>',$out);
  $t2=explode('href="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  }
  if (!$l) {
  $t1=explode('file":"',$out);
  $t2=explode('"',$t1[1]);
  $l=$t2[0];
  }
  if (!preg_match("/mega\.nz|filepress\.|2embed\./",$l)) $r[]=$l;
  if (preg_match("/filepress\./",$l)) resolveFP($l);
  if (preg_match("/2embed\./",$l)) resolve2E($l);
}
curl_close ($ch);
  //echo $l;
//print_r ($r);
  //if (preg_match("/filepress\./",$l)) {
  function resolveFP($l) {
    // https://filepress.site/video/62d30767f48e28735d752ad7
    // https://api.filepress.site/api/file/video/62d30767f48e28735d752ad7/
    global $r;
    $ua="Mozilla/5.0 (Windows NT 10.0; rv:102.0) Gecko/20100101 Firefox/102.0";
    $l=str_replace("/video/","/api/file/video/",$l);
    $l=str_replace("filepress","api.filepress",$l);
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_REFERER,"https://filepress.site");
    curl_setopt($ch, CURLOPT_ENCODING,"");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close ($ch);
    $x=json_decode($h,1)['data']['thirdPartyDetails'];
    foreach ($x as $key=>$value) {
      if (preg_match("/streamSB/i",$key)) {
        if (isset($value['filecode']))
          $r[]="https://gdpress.xyz/e/".$value['filecode'];
      } elseif (preg_match("/streamTape/i",$key)) {
        if (isset($value['filecode']))
          $r[]="https://streamtape.com/e/".$value['filecode'];
      } elseif (preg_match("/doodStream/i",$key)) {
        if (isset($value['filecode']))
          $r[]="https://dood.pm/e/".$value['filecode'];
        elseif (isset($value['protected_embed']))
          $r[]="https://dood.pm".$value['protected_embed'];
      }
    }
          
    //print_r ($x);
  }
function resolve2E($filelink) {
  global $r;
  $t1=explode("?",$filelink);
  $host=parse_url($t1[0])['host'];
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
  preg_match_all("/data-id=\"(\d+)\"/",$h,$m);
  for ($z=0;$z<count($m[0]);$z++) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  //$key="6LdBfTkbAAAAAL25IFRzcJzGj9Q-DKcrQCbVX__t";
  //$key="6Lf2aYsgAAAAAFvU3-ybajmezOYy87U4fcEpWS4C"; // 24.06.2022
  $co="aHR0cHM6Ly93d3cuMmVtYmVkLnJ1OjQ0Mw..";
  $co="aHR0cHM6Ly93d3cuMmVtYmVkLnJ1OjQ0Mw..";
  $loc="https://".$host;
  $sa="get_link";
  $id=$m[1][$z];
  $token=rec($key,$co,$sa,$loc);
  $l="https://".$host."/ajax/embed/play?id=".$id."&_token=".$token;
  $head=array('Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive',
  'Referer: https://'.$host.'/embed/imdb/tv?id=tt9737326&s=1&e=3');

  curl_setopt($ch, CURLOPT_URL, $l);

  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  $r[]=$x['link'];
  }
}

  //echo $html;
  //print_r ($r);
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.parse_url($r[0])['host'].'</label>
<input type="hidden" id="file" value="'.urlencode($r[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link=$r[$i];
  $openload=parse_url($r[$i])['host'];
  if (preg_match($indirect,$openload)) {
  echo '<TD class="mp"><a href="filme_link.php?file='.urlencode($c_link).'&title='.urlencode(unfix_t($tit.$tit2)).'" target="_blank">'.$openload.'</a></td>';
  } else
  echo '<TD class="mp"><a id="myLink" href="#" onclick="changeserver('."'".$openload."','".urlencode($c_link)."'".');return false;">'.$openload.'</a></td>';
  $x++;
  if ($x==6) {
    echo '</TR>';
    $x=0;
  }
}
if ($x < 6 && $x > 0 & $k>6) {
 for ($k=0;$k<6-$x;$k++) {
   echo '<TD></TD>'."\r\n";
 }
 echo '</TR>'."\r\n";
}
echo '</TABLE>';
if ($tip=="movie") {
  $tit3=$tit;
  $tit2="";
  $sez="";
  $ep="";
  $imdbid="";
  $from="";
  $link_page="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  $imdbid="";
  $from="";
  $link_page="";
}
  $rest = substr($tit3, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $tit3=trim(str_replace($m[0],"",$tit3));
  }
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
echo '<br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="4">Alegeti o subtitrare</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="4">Alegeti o subtitrare (cauta imdb id)</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub1" href="opensubtitles1.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari1" href="titrari_main1.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs1" href="subs_main1.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari1" href="subtitrari_main1.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($tit.$tit2));
 if ($flash != "mp")
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink1('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
 else
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
echo '</tr>';
echo '</table>';
echo '<br>
<table border="0px" width="100%">
<TR>
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5=vizioneaza
<BR>Scurtaturi: 7=opensubtitles, 8=titrari, 9=subs, 0=subtitrari (cauta imdb id)
</b></font></TD></TR></TABLE>
';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
