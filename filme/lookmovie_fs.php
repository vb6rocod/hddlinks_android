<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
$cookie=$base_cookie."hdpopcorns.dat";
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists($base_cookie."look_token.txt")) unlink ($base_cookie."look_token.txt");
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
$slug="";
} else {
if ($ep_title)
   $tit2=" - ".$sez."x".$ep." ".$ep_title;
else
   $tit2=" - ".$sez."x".$ep;
$tip="series";
$slug=$_GET['slug'];
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
$r=array();
$s=array();
$srt="";
if ($tip=="movie") {
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$cookie=$base_cookie."lookmovie.dat";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
if (file_exists($cookie)) {
 $x=file_get_contents($cookie);
 if (preg_match("/lookmovie\.io	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
  $cc=trim($m[1]);
 else
  $cc="";
} else {
  $cc="";
}
$head=array('User-Agent: '.$ua.'',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Cookie: cf_clearance='.$cc);
//print_r ($head);
$l=$link;
  //$ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $h=str_replace('" + window.location.host + "',"lookmovie.io",$h);
  if (preg_match("/file\"\:\s*\"((.*?)(Romanian|ro)\.vtt)/",$h,$p))
    $srt=$p[1];
  elseif (preg_match("/file\"\:\s*\"((.*?)(English|en)\.vtt)/",$h,$p))
    $srt=$p[1];
  else
    $srt="";
  //print_r ($s);
  //echo $srt;
  //$t1=explode("id_movie='",$h);
  //$t2=explode("'",$t1[1]);
  //$id=$t2[0];
  if (preg_match("/id_movie\:?\s*\'?(\d+)/",$h,$m))
   $id=$m[1];
  $l="https://lookmovie.io/api/v1/security/movie-access?id_movie=".$id."&token=1&sk=&step=1";
  $opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=> $head
  )
  );

  $context = stream_context_create($opts);
  $h = @file_get_contents($l, false, $context);
  $x=json_decode($h,1);
  //print_r ($r);
  $time=$x['data']['expires'];
  $token=$x['data']['accessToken'];
  $l="https://lookmovie.io/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8";
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=> $head
  )
);

$context = stream_context_create($opts);
$h = @file_get_contents($l, false, $context);
  if (!$slug) {
  $x=json_decode($h,1);
  //print_r ($r);
  foreach ($x as $key => $value) {
   if ($key <> "auto") {
    $r[]=$value;
    $s[]=$key;
   }
  }
  } else {
  //echo $h;
   $base1=str_replace(strrchr($l, "/"),"/",$l);
   $base2=getSiteHost($l);
   if (preg_match("/\.m3u8/",$h)) {
    $a1=explode("\n",$h);
    for ($k=0;$k<count($a1)-1;$k++) {
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
    $link=$base.$pl[0];
   }
  } else {
   $link=$l;
  }
   $r[]=$link;
   $s[]="auto";
  }
////////////////////////////////////////////////////////////////////////////////////////
} else {
  $id=$link;
  $sub="";
$cookie=$base_cookie."lookmovie.dat";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
if (file_exists($cookie)) {
 $x=file_get_contents($cookie);
 if (preg_match("/lookmovie\.io	\w+	\/	\w+	\d+	cf_clearance	([\w|\-]+)/",$x,$m))
  $cc=trim($m[1]);
 else
  $cc="";
} else {
  $cc="";
}
$head=array('User-Agent: '.$ua.'',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Cookie: cf_clearance='.$cc);
$s=array();
  $l="https://lookmovie.io/api/v1/shows/episode-subtitles/?id_episode=".$id;
  //$l="https://lookmovie.io/api/v1/shows/episode-subtitles/?id_episode=119775";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  $s=json_decode($h,1);
  //print_r ($s);
  // https://lookmovie.io/storage2/shows/8134470-the-undoing-2020/9676-S1-E6-1610711843/subtitles/en.vtt
  // shows/8134470-the-undoing-2020/9676-S1-E1-1610132346/subtitles/
  $srt1=array();
  $srt="";
  for ($k=0;$k<count($s);$k++) {
    $srt1[$s[$k]["languageName"]]="https://lookmovie.io/".$s[$k]["shard"]."/".$s[$k]["storagePath"].$s[$k]["isoCode"].".vtt";
  }
  if (isset($srt1["Romanian"]))
    $srt=$srt1["Romanian"];
  elseif (isset($srt1["English"]))
    $srt=$srt1["English"];
  else
    $srt="";
$r=array();
$s=array();
//$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  $l="https://lookmovie.io/api/v1/security/show-access?slug=".$slug."&token=&step=2";
  $opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=> $head
  )
  );

  $context = stream_context_create($opts);
  $h = @file_get_contents($l, false, $context);
  $x=json_decode($h,1);
  //print_r ($r);
  $time=$x['data']['expires'];
  $token=$x['data']['accessToken'];
  $l="https://lookmovie.io/manifests/movies/json/".$id."/".$time."/".$token."/master.m3u8";
  $l="https://lookmovie.io/manifests/shows/json/".$token."/".$time."/".$id."/master.m3u8";
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=> $head
  )
);

$context = stream_context_create($opts);
$h = @file_get_contents($l, false, $context);
//echo $h;
  //if ($slug) {
  $x=json_decode($h,1);
  //print_r ($x);
  foreach ($x as $key => $value) {
   if ($key <> "auto") {
    $r[]=$value;
    $s[]=$key;
   }
  }
  //}
  //echo $sub;
  //echo 'lookmovie_token.html?id='.$id.'&slug='.$slug;
  // https://lookmovie.ag/api/v1/shows/episode-subtitles/?id_episode=96036
}
// https://lookmovie.io/storage2/1612680271/movies/9028784-christmas-at-grand-valley-2018-1612657873/subtitles/en.vtt

//$r[]=$l;
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.$s[0].'</label>
<input type="hidden" id="file" value="'.urlencode("https://lookmovie.ag?link=".$r[0]."&sub=".$srt).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link="https://lookmovie.ag?link=".$r[$i]."&sub=".$srt;
  $openload=$s[$i];
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
echo '<br>';
if ($tip=="movie") {
if (preg_match("/English|Romanian|en|ro/",$srt,$z))
 echo '<b>Subtitles: '.$z[0]."</b><BR>";
} else {
if (preg_match("/(en|ro)\.vtt/",$srt,$z))
 echo '<b>Subtitles: '.$z[1]."</b><BR>";
}
echo '<table border="0px" width="100%">
<TR>
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5=vizioneaza
<BR>Scurtaturi: 7=opensubtitles, 8=titrari, 9=subs, 0=subtitrari (cauta imdb id)
</b></font></TD></TR></TABLE>
<!--<iframe src="lookmovie_token.html?id='.$id.'&slug='.$slug.'"></iframe>-->
';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
