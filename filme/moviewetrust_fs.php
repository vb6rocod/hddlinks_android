<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
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
  //alert (link1);
  if (link1.match(/streamembed|imwatchingmovies/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=flash";
  window.open(msg);
  } else {
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
  }
}
function openlink(link) {
  link1=document.getElementById('file').value;
  if (link1.match(/streamembed|imwatchingmovies|streambucket/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=mp";
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
$tmdb=$link;
$r=array();
$s=array();
$ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
///////////////////////////////////////////////////////
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
   $info .='<a href="moviewetrust_p.php?page=1&link='.$director[$k][1].'&title='.urlencode($director[$k][0]).'" target="_blank">'.$director[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($producer)>0) {
  $info .='<b><font color="yellow">Producer:</font></b>';
  for ($k=0;$k<min(5,count($producer));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$producer[$k][1].'&title='.urlencode($producer[$k][0]).'" target="_blank">'.$producer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($writer) > 0) {
  $info .='<b><font color="yellow">Writer:</font></b>';
  for ($k=0;$k<min(10,count($writer));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$writer[$k][1].'&title='.urlencode($writer[$k][0]).'" target="_blank">'.$writer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  $info .='<b><font color="yellow">Cast:</font></b>';
  for ($k=0;$k<min(15,count($actors));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$actors[$k][1].'&title='.urlencode($actors[$k][0]).'" target="_blank">'.$actors[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  $info .= '<b><font color="cyan">Overview:</font></b>'.$overview;
  $imdb=$x['external_ids']['imdb_id'];
  //echo $imdb;
  //die();
$k=0;
  //echo $imdb;
  //die();

////////////////////////////////////////////////////////////
// vidsrc.me
if ($tip=="movie")
  $l="https://vidsrc.me/embed/".$imdb."/";
else
  $l="https://vidsrc.me/embed/".$imdb."/".$sez."-".$ep."/";
   $r[]=$l;
   $s[]="v. ".parse_url($l)['host'];

   if ($tip=="movie")
     $r[]="https://voidboost.net/embed/".$imdb."?t=20&td=20&tlabel=English&cc=off&plang=en&poster=1";
   else
     $r[]="https://voidboost.net/embed/".$imdb."?&s=".$sez."&e=".$ep."&t=20&td=20&tlabel=English&cc=off&plang=en&poster=1";
   $s[]="voidboost";
   $imdbid=str_replace("tt","",$imdb);

/////////////////////////////////////////////////////
  if ($tip=="movie")
   $l="https://multiembed.mov/?video_id=".$imdb;
  else
   $l="https://multiembed.mov/?video_id=".$imdb."&s=".$sez."&e=".$ep;
   //echo $l;
   //https://www.superembed.stream/?c=embed
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) Gecko/20100101 Firefox/108.0";

$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Alt-Used: embedo.xyz',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/location\:\s*(.+)/i",$h,$m))
    $link=trim($m[1]);
  //echo $link;
  $bk="dHJ1ZS-Qt-PS-QtNj-P3LS-Qz-PzItL-0-V2NzIwODUyO-0Ay-PjU-5";
  //$bk="dHJ1ZS-Qt-PS-QtNj-P3LS-Qz-PzItL-0-V2NzIwODUyO-0Ay-PjU-4";
  $post="button-click=".base64_encode($bk)."&button-referer=";
  //echo base64_decode("ZEhKMVpTLVF0LVBTLVF0TmotUDNMUy1Rei1Qekl0TC0wLVYyTnpJd09EVXlPLTBBeS1QalUtNQ==");
$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://streambucket.net',
'Connection: keep-alive',
'Referer: '.$link,
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: same-origin',
'Sec-Fetch-User: ?1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);

  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h."\n";
  //die();
  preg_match("/load_sources\(\"([^\"]+)\"/",$h,$m);
  $token=$m[1];
  $l="https://imwatchingmovies.com/response.php";
  $l="https://streambucket.net/response.php";
  //$l="https://streamembed.net/user_guard.php";
  $post="token=".$token;

$head=array('User-Agent: '.$ua,
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://streambucket.net',
'Connection: keep-alive',
'Referer: '.$link,
'Sec-Fetch-Dest: empty',
'Sec-Fetch-Mode: cors',
'Sec-Fetch-Site: same-origin');
//sleep (5);
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
$videos = explode('li data-id="', $h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('"',$video);
 $id=$t1[0];
 $t1=explode('data-server="',$video);
 $t2=explode('"',$t1[1]);
 $ds=$t2[0];
 $r[]="https://streambucket.net/playvideo.php?video_id=".$id."=&server_id=".$ds."&token=".$token."&init=1";
 $t1=explode('</div>',$video);
 $s[]="i.".trim($t1[1]);
}
////////////////////////////////////////////////////////////////////////
  if ($tip=="movie")
   $l="https://embedo.xyz/play/movie.php?imdb=".$imdb;
  else
   $l="https://embedo.xyz/play/series.php?imdb=".$imdb."&sea=".$sez."&epi=".$ep;
   //echo $l;
  //$r[]=$l;
  //$s[]="Vidcloud";
if ($tip=="movie") {
 //$r[]="https://www.2embed.cc/imdb/".$imdb;
 //$s[]="2embed";
}
///////////////////////////////
/*
if ($tip=="movie") {
 $r[]="https://api.9animetv.live/player/cinema-player.php?id=".$imdb;
 $s[]="9animetv";
} else {
 $r[]="https://api.9animetv.live/player/cinema-player.php?id=".$imdb."&s=".$sez."&e=".$ep;
 $s[]="9animetv";
}
*/
//aniwave.to
/////////////////////////////////////////////
if ($tip=="movie")
 $l="https://moviesapi.club/movie/".$tmdb;
else
 $l="https://moviesapi.club/tv/".$tmdb."-".$sez."-".$ep;
$r[]=$l;
$s[]="moviesapi";
///////////////////////////////////////////
//print_r ($r);
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

 if ($flash =="flash")
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
echo $info;
//echo '<a href="https://streamembed.net/play/YTF0TklLYXplRnRhdjNTcHBUQUxnUzd1amt0UkIrZTJTWUZlQk8wYXJsWXhlT2EzTkxaQkU0RU9HQ2ZwemhvPQ==">sasaasas</a>';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
