<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
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
$t1=explode("- Season",$tit);
$tit=trim($t1[0]);
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
$ua = $_SERVER['HTTP_USER_AGENT'];
//$host=parse_url($link)['host'];
if ($tip=="movie") {
  /*
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://www.moviesjoy.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  $t1=explode("movie = {",$h);
  $t2=explode('id: "',$t1[1]);
  $t3=explode('"',$t2[1]);
  $id=$t3[0];

  $t2=explode('movie_id: "',$t1[1]);
  $t3=explode('"',$t2[1]);
  $movie_id=$t3[0];
  //https://www1.moviesjoy.net/ajax/movie/episodes/58758
  $l="https://www1.moviesjoy.net/ajax/v4_movie_episodes/".$id."/".$movie_id;
  */
  $l="https://www1.moviesjoy.net/ajax/movie/episodes/".$link;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);

  //$x=json_decode($h,1);
  //$h=$x['html'];
  $r=array();
  $s=array();
  $videos=explode('data-linkid="',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('</i>',$video);
    $t2=explode('<',$t1[1]);
    $svr_name=trim($t2[0]);
    $t3=explode('"',$video);
    $l="https://www1.moviesjoy.net/movie/".$t3[0];
    $r[]=$l;
    $s[]=$svr_name;
  }
} else {
  $r=array();
  $s=array();
  $svr_name=array();
  $l="https://www1.moviesjoy.net/ajax/season/episodes/".$link;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $link);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $videos=explode('href="#',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('"',$video);
    $t2=explode('aria-selected',$video);
    $t3=explode('>',$t2[1]);
    $t4=explode('<',$t3[1]);
    $svr_name[$t1[0]]=$t4[0];
  }
  //print_r ($svr_name);
  $videos=explode('div class="tab-pane',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('id="',$video);
    $t2=explode('"',$t1[1]);
    $id_serv=$t2[0];
    $vids=explode('data-linkid="',$video);
    //$vids = explode('data-linkid="', $h);
    unset($vids[0]);
    $vids = array_values($vids);
    foreach($vids as $vid) {
      $t1=explode('"',$vid);
      $id_link=$t1[0];
      $t1=explode('strong>',$vid);
      $t2=explode(':',$t1[1]);
      preg_match("/\d+/",$t2[0],$p);
      $episod=$p[0];
      if ($episod == $ep) {
        $l="https://www1.moviesjoy.net/movie/".$id_link;
        $r[]=$l;
        $s[]=$svr_name[$id_serv];
      }
    }
  }
}
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.$s[0].'</label>
<input type="hidden" id="file" value="'.urlencode($r[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link=$r[$i];
  $openload=parse_url($r[$i])['host'];
  if (preg_match($indirect,$openload)) {
  echo '<TD class="mp"><a href="filme_link.php?file='.urlencode($c_link).'&title='.urlencode(unfix_t($tit.$tit2)).'" target="_blank">'.$s[$i].'</a></td>';
  } else
  echo '<TD class="mp"><a id="myLink" href="#" onclick="changeserver('."'".$s[$i]."','".urlencode($c_link)."'".');return false;">'.$s[$i].'</a></td>';
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
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5=vizioneaza</b></font></TD></TR></TABLE>
';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
';

echo '
</body>
</html>';
