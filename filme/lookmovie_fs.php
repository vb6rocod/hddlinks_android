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
$r=array();
$s=array();
$srt="";
$lang="";
if ($tip=="movie") {
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$cookie=$base_cookie."lookmovie.txt";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";

$l=$link;
//echo $l;
  //$ua = $_SERVER['HTTP_USER_AGENT'];
$last_good="https://lookmovie.io";
$last_good="https://lookmovie2.to";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/href\=\s*\"\s*(https.*?\/play\/.*?)\"/",$h,$m)) {
    $l1=$m[1];
    $ref=parse_url($l1)['host'];
  } else {
    $l1="";
    $ref="";
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/threat\-protection/",$info['url'])) {
   require_once("rec.php");
   $key="6Lc3OL0aAAAAAJhbmY4C3GvXoRvHizdk5YKZK7fg";
   $co="aHR0cHM6Ly9sbXBsYXllcjE4Lnh5ejo0NDM.";
   $sa="submit";
   $loc="https://lookmovie2.to";
   $token=rec($key,$co,$sa,$loc);
   $t1=explode('hidden" name="_csrf" value="',$h);
   $t2=explode('"',$t1[1]);
   $csrf=$t2[0];
   $post="_csrf=".$csrf."&tk=".$token;
   //echo $post;
   $l=$info['url'];
   $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded',
   'Content-Length: '.strlen($post),
   'Origin: https://'.$ref,
   'Connection: keep-alive',
   'Referer: '.$l,
   'Upgrade-Insecure-Requests: 1');
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
 //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  //curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  //print_r ($info);
  //echo $h;
  if (isset($info['redirect_url'])) {
  $l=$info['redirect_url'];
  if (preg_match("/second/",$l)) {
  file_put_contents($base_cookie."lookmovie_ref1.txt",$l."|".$ref."|".$csrf);
   echo '<a href="look.html">Solve captcha</a>';
  } else {
   echo 'Try again';
   echo '<script>setTimeout(function(){ history.go(-1); }, 2000);</script>';
  }
  exit;
  }
  } else {
  $id="";
  $hash="";
  $ex="";
  if (preg_match("/id_movie\:?\s*\'?(\d+)/",$h,$m))
   $id=$m[1];
  if (preg_match("/hash\:\s*[\"|\']([^\"\']+)[\'\"]/",$h,$n))
    $hash=$n[1];

  if (preg_match("/expires\:?\s*\'?(\d+)/",$h,$m))
   $ex=$m[1];
  $l="https://".$ref."/api/v1/security/movie-access?id_movie=".$id."&hash=".$hash."&expires=".$ex; //"&token=1&sk=&step=1";

  //echo $l;
  //die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
  //}

  //$x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['subtitles'])) {
  $srt1=array();
  $s=array();
  $srt="";
  $sss=$x['subtitles'];
  //for ($k=0;$k<count($sss);$k++) {
  foreach($sss as $key=>$value) {
   if ($sss[$key]['file'][0]=="/") {
   //if ($sss[$k]['file'][0] == "/") {
     //$ss= "https://".$ref.$sss[$k]['file'];
     $ss= "https://".$ref.$sss[$key]['file'];
   //else
     //$ss=$sss[$k]['file'];
   //$srt1[$sss[$k]['language']] = $ss;
   if (!isset($srt1[$sss[$key]['language']]))
   $srt1[$sss[$key]['language']] = $ss;
   }
  }
  if (isset($srt1["Romanian"])) {
    $srt=$srt1["Romanian"];
    $lang="Romanian";
  } elseif (isset($srt1["English"])) {
    $srt=$srt1["English"];
    $lang="English";
  } else
    $srt="";
  }

  $x=$x['streams'];
  //print_r ($x);

  foreach ($x as $key => $value) {
   if ($key <> "auto") {
    $r[]=$value;
    $s[]=$key;
   }
  }
 }
////////////////////////////////////////////////////////////////////////////////////////
} else {    // series
  $id=$link;
  $sub="";
$cookie=$base_cookie."lookmovie.txt";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
$ref=file_get_contents($base_cookie."lookmovie_ref.txt");

$r=array();
$s=array();
//echo $srt;
//$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');
  // https://lookmovie2.to/api/v1/security/episode-access?id_episode=15205
  // https://lookmovie2.to/api/v1/security/episode-access?id=15205

  //$l="https://lookmovie.io/api/v1/security/show-access?slug=".$slug."&token=&step=2";
  $l="https://".$ref."/api/v1/security/episode-access?id_episode=".$link;
  //$l="https://".$ref."/api/v1/security/episode-access?id=".$link;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);

  //echo $h;
  $x=json_decode($h,1);
  if (isset($x['subtitles'])) {
  $srt1=array();
  $s=array();
  $srt="";
  $sss=$x['subtitles'];
  //for ($k=0;$k<count($sss);$k++) {
  foreach($sss as $key=>$value) {
   if ($sss[$key]['file'][0]=="/") {
   //if ($sss[$k]['file'][0] == "/") {
     //$ss= "https://".$ref.$sss[$k]['file'];
     $ss= "https://".$ref.$sss[$key]['file'];
   //else
     //$ss=$sss[$k]['file'];
   //$srt1[$sss[$k]['language']] = $ss;
   if (!isset($srt1[$sss[$key]['language']]))
   $srt1[$sss[$key]['language']] = $ss;
   }
  }
  if (isset($srt1["Romanian"])) {
    $srt=$srt1["Romanian"];
    $lang="Romanian";
  } elseif (isset($srt1["English"])) {
    $srt=$srt1["English"];
    $lang="English";
  } else
    $srt="";
  }
  $x=json_decode($h,1)['streams'];
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
?>
<!doctype html>
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
include ("subs.php");
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
if ($lang) {
 echo '<b>Subtitles: '.$lang."</b><BR>";
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
