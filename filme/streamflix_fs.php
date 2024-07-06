<!doctype html>
<?php
include ("../common.php");
error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists("vidsrc.txt")) unlink ("vidsrc.txt");
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
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
function openlink1(link) {
  link1=document.getElementById('file').value;
  s=document.getElementById('server').innerHTML;
  //alert (link1);
  if (link1.match(/streamembed|imwatchingmovies/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=flash";
  window.open(msg);
  } else if (s.match(/vidstream|mycloud|vidplay|F2Cloud/gi)) {
  msg="mcloud1.php?id=" + encodeURI(link1) + "&title=" + link + "&tip=flash";
  window.open(msg);
  } else {
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
  }
}
function openlink(link) {
  link1=document.getElementById('file').value;
  s=document.getElementById('server').innerHTML;
  if (link1.match(/streamembed|imwatchingmovies/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=mp";
  window.open(msg);
  } else if (s.match(/vidstream|mycloud|vidplay|F2Cloud/gi)) {
  msg="mcloud1.php?id=" + encodeURI(link1) + "&title=" + link + "&tip=mp";
  window.open(msg);
  } else {
  on();
  var request =  new XMLHttpRequest();
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
$ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
///////////////////////////////////////////////////////
$tmdb=$link;
if ($tip=="movie")
$l="https://api.themoviedb.org/3/movie/".$link."?api_key=".$api_key."&append_to_response=credits,external_ids";
else
$l="https://api.themoviedb.org/3/tv/".$link."?api_key=".$api_key."&append_to_response=credits,external_ids";
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
  //print_r ($x);
  //die();
  $info="";
  $overview=$x['overview'];
  if (isset($x['first_air_date']))
   $release_date=$x['first_air_date'];
  else
   $release_date=$x['release_date'];
  preg_match("/\d{4}/",$release_date,$d);
  $release_date=$d[0];
  $vote=$x['vote_average'];
  //$vote=$x['popularity'];
  if (isset($x['runtime']))
    $duration=$x['runtime'];
  elseif (isset($x['episode_run_time'][0]))
    $duration=$x['episode_run_time'][0];
  else
    $duration="";
  $y=$x['credits']['cast'];
  $z=$x['credits']['crew'];
  //print_r ($z);
  $actors=array();
  $director=array();
  $producer=array();
  $writer=array();
  for ($k=0;$k<count($y);$k++) {
   $a=$y[$k]['known_for_department'];
   //echo $a;
   if ($a=="Acting") $actors[]=array($y[$k]['name'],$y[$k]['id']);
  }
  //print_r ($actors);
  for ($k=0;$k<count($z);$k++) {
    if (preg_match("/director/i",$z[$k]['job']))
      $director[]=array($z[$k]['name'],$z[$k]['id']);
    elseif (preg_match("/story|writer/i",$z[$k]['job']))
      $writer[]=array($z[$k]['name'],$z[$k]['id']);
    elseif (preg_match("/producer/i",$z[$k]['job']))
      $producer[]=array($z[$k]['name'],$z[$k]['id']);
   }
  $genres="";
  for ($k=0;$k<count($x['genres']);$k++) {
    $genres .=$x['genres'][$k]['name'].",";
  }
  $genres = substr($genres, 0, -1);
  $info .="<b>Release date:</b>".$release_date.".<b>Runtime:</b>".$duration." min.<b>TMDB</b>:".$vote.".".$genres.'.<BR>';
  if (count($director)>0) {
  $info .='<b><font color="yellow">Director:</font></b>';
  for ($k=0;$k<min(10,count($director));$k++) {
   $info .='<a href="streamflix_p.php?page=1&link='.$director[$k][1].'&title='.urlencode($director[$k][0]).'" target="_blank">'.$director[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($producer)>0) {
  $info .='<b><font color="yellow">Producer:</font></b>';
  for ($k=0;$k<min(5,count($producer));$k++) {
   $info .='<a href="streamflix_p.php?page=1&link='.$producer[$k][1].'&title='.urlencode($producer[$k][0]).'" target="_blank">'.$producer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($writer) > 0) {
  $info .='<b><font color="yellow">Writer:</font></b>';
  for ($k=0;$k<min(10,count($writer));$k++) {
   $info .='<a href="streamflix_p.php?page=1&link='.$writer[$k][1].'&title='.urlencode($writer[$k][0]).'" target="_blank">'.$writer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  $info .='<b><font color="yellow">Cast:</font></b>';
  for ($k=0;$k<min(15,count($actors));$k++) {
   $info .='<a href="streamflix_p.php?page=1&link='.$actors[$k][1].'&title='.urlencode($actors[$k][0]).'" target="_blank">'.$actors[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  $info .= '<b><font color="cyan">Overview:</font></b>'.$overview;
  $imdb=$x['external_ids']['imdb_id'];
  //echo $imdb;
  //die();
$k=0;
//////////////////////////////////////
/*
if ($tip=="movie")
$l="https://us-west2-compute-proxied.streamflix.one/api/player/movies?id=".$link;
else
$l="https://us-west2-compute-proxied.streamflix.one/api/player/tv?id=".$link."&s=".$sez."&e=".$ep;
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
//echo $l;
//die();
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
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['sources'][0])) {
   $ref=$x['headers']['Referer'];
   for ($k=0;$k<count($x['sources']);$k++) {
     if (!preg_match("/auto/i",$x['sources'][$k]['quality'])) {
      $s[]= $x['sources'][$k]['quality'];
      $r[]= $x['sources'][$k]['url'];
     }
   }
  }
  $lang="";
  $srt="";
  if (isset($x['subtitles'][0])) {
  $srt="";
  $sss=$x['subtitles'];
  foreach($sss as $key=>$value) {
   if (preg_match("/romanian/i",$sss[$key]['lang'])) {
     $srt=$sss[$key]['url'];
     $lang="Romanian";
   }
  }
  if (!$srt) {
  foreach($sss as $key=>$value) {
   if (preg_match("/english/i",$sss[$key]['lang'])) {
     $srt=$sss[$key]['url'];
     $lang="English";
   }
  }
  }
  } else {
   $srt="";
  }
*/
///////////////////////////////////////////
//print_r ($r);
if ($tip=="movie") {
$l="https://www.2embed.cc/embed/".$imdb;
//echo $l;
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://www.2embed.cc/',
'Origin: https://www.2embed.cc',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match_all("/onclick\=\"go\(\'([^\']+)\'/",$h,$m);
  //print_r ($m[1]);
  foreach ($m[1] as $key=>$value) {
   $host=parse_url($value)['host'];
   if (preg_match("/stream.2embed.cc/",$host)) {
    $r[]=$value;
    $s[]=$host;
   } elseif (preg_match("/owns\?swid\=/",$value)) {
     $t1=explode("?swid=",$value);
     $r[]="https://stream.2embed.cc/e/".$t1[1];
     $s[]="stream.2embed.cc";
   }
  }
  /////////////////////////////////////
  $l="https://gomo.to/movie/".$imdb;
     require_once("gomo.php");
     $g=new gomo();
     $x=array();
     $x=$g->gomo_r($l);
     //print_r ($x);
     foreach ($x as $y) {
      if ($y) {
       if (!preg_match("/hqq|gomo/",$y)) {
       $r[]=$y;
       $s[]="g.".parse_url($y)['host'];
       }
      }
     }
  //////////////////////////////
  $l="https://vidsrc.me/embed/".$imdb;
  $r[]=$l;
  $s[]=parse_url($l)['host'];
  ///////////////////////////////
  $l="https://embed.smashystream.com/playere.php?imdb=".$imdb;
  //echo $l;
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
   //preg_match_all("/data\-id\=\"(http[^\"]+)/",$h,$m);
   //print_r ($m[1]);
  if (preg_match("/\/f\w+\.php/",$h,$m)) {
   $l="https://embed.smashystream.com".$m[0]."?tmdb=".$link;
   $r[]=$l;
   $s[]="smashystream";
  }
} else {
  $l="https://gomo.to/show/".$imdb."/".sprintf("%02d",$sez)."-".sprintf("%02d",$ep);
  //echo $l;
     require_once("gomo.php");
     $g=new gomo();
     $x=array();
     $x=$g->gomo_r($l);
     //print_r ($x);
     foreach ($x as $y) {
      if ($y) {
       if (!preg_match("/hqq|gomo/",$y)) {
       $r[]=$y;
       $s[]="g.".parse_url($y)['host'];
       }
      }
     }
  $l="https://vidsrc.me/embed/".$imdb."/".$sez."-".$ep."/";
  $r[]=$l;
  $s[]=parse_url($l)['host'];
  $l="https://embed.smashystream.com/playere.php?tmdb=".$link."&season=".$sez."&episode=".$ep;
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/112.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //preg_match_all("/data\-id\=\"(http[^\"]+)/",$h,$m);
  //print_r ($m[1]);
  if (preg_match("/\/f\w+\.php[^\"]+/",$h,$m)) {
   $l="https://embed.smashystream.com".$m[0];
   $r[]=$l;
   $s[]="smashystream";
  }
}
////////////////////////////////////
if ($tip=="movie")
 $l="https://vidsrc.to/embed/movie/".$tmdb;
else
 $l="https://vidsrc.to/embed/tv/".$tmdb."/".$sez."/".$ep;
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://vidsrc.to',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  $h .=json_decode($h,1)['result'];
  //echo $h;
  if (preg_match("/data\-id\=\"([^\"]+)\"/",$h,$m)) {
  $id=$m[1];
  $l="https://vidsrc.to/ajax/embed/episode/".$id."/sources";
  //echo $l;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  //echo $h;
  $z=json_decode($h,1)['result'];
  for ($k=0;$k<count($z);$k++) {
    $r[]="https://vidsrc.to/ajax/embed/source/".$z[$k]['id'];
    $s[]=$z[$k]['title'];
  }
  }
  curl_close($ch);
//die();
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.$s[0].'</label>
<input type="hidden" id="file" value="'.urlencode($r[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link=$r[$i];
  $openload=$s[$i];
  if (preg_match("/streamembed1/",$c_link)) {
  echo '<TD class="mp"><a href="streamembed1.php?file='.urlencode($c_link).'&title='.urlencode(unfix_t($tit.$tit2)).'&tip='.$flash.'" target="_blank">'.$openload.'</a></td>';
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
$imdbid=str_replace("tt","",$imdb);
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
include ("subs.php");
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($tit.$tit2));

 if ($flash == "flash")
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
</b></font></TD></TR></TABLE><BR>
';
echo $info;
//echo '<a href="https://streamembed.net/play/YTF0TklLYXplRnRhdjNTcHBUQUxnUzd1amt0UkIrZTJTWUZlQk8wYXJsWXhlT2EzTkxaQkU0RU9HQ2ZwemhvPQ==">sasaasas</a>';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
