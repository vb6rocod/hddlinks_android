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
//$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
//$ep_title=prep_tit($ep_title);
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
$imdbid=str_replace("tt","",$link);

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
function savesub(sub) {
  on();
  var request =  new XMLHttpRequest();
  link1=document.getElementById('file').value;
  var the_data = "link=" + sub;
  var php_file="savesub1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
    }
  }
}
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
  document.getElementById('server').innerHTML = decodeURIComponent(s.replace(/\+/g, ' '));;
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
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."vikv.dat";
$l1="https://api.hdv.fun/embed/".$link;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch,CURLOPT_REFERER,"https://vikv.net");
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_NOBODY,1);
curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
if (preg_match("/location:\s*(.+)/i",$h,$m)) {
 $host=parse_url(trim($m[1]))['host'];
} else {
 $host="api.hdv.fun";
}
$l1="https://".$host."/embed/".$link;
//$l1="https://eb4.ffull.pw/embed/tt9779516";
//echo $l1;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch,CURLOPT_REFERER,"https://vikv.net/");
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_HEADER,1);
//curl_setopt($ch, CURLOPT_NOBODY,0);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
//die();
$l="https://api.hdv.fun/l1";
$l="https://".$host."/l1";
//echo $l;
//$link="tt1502397";
$ip=$_SERVER['REMOTE_ADDR'];
$post="imdb=".$link."&ip=".$ip."&hd=true";
//$post="imdb=".$link."&ip=0&hd=false";
//echo $post;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'Accept: */*',
     'Accept-Language: en-US,en;q=0.5',
     'Accept-Encoding: deflate',
     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
     'Content-Length: '.strlen($post).'',
     'Origin: https://'.$host.'',
     'X-Requested-With: XMLHttpRequest'
));
curl_setopt($ch, CURLOPT_REFERER, "https://".$host."/embed/".$link);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
$p=json_decode($h,1);
//print_r ($p);
$r=array();
//die();
$s=array();
$s_l=array();
//https://api.hdv.fun/sub?fid=22&lg=english
//https://sub1.hdv.fun/vtt1/442044.vtt
for ($k=0;$k<count($p);$k++) {
 $l=$p[$k]['src'][0]['src']."&referer=https://".$host;
 $name=$p[$k]['name'];
 if (strpos($l,"http") !== false) $r[]=urlencode($l)."||".$name;
 if (isset($p[$k]['sub'])) {
 foreach ($p[$k]['sub'] as $key=> $value) {
   if ($p[$k]['sub'][$key]['lg'] == 'romanian' || $p[$k]['sub'][$key]['lg'] == 'english') {
     $sub_id=$p[$k]['sub'][$key]['sub_id'];
     $s_l[]=$p[$k]['sub'][$key]['lg'];
     $s[]="https://api.hdv.fun/sub?fid=".$sub_id."&lg=".$p[$k]['sub'][$key]['lg'];
   }
 }
 }
}

$t1=explode("||",$r[0]);
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Varianta aleasa: <label style="color: #e6e600;" id="server">'.$t1[1].'</label>
<input type="hidden" id="file" value="'.urlencode($t1[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $t1=explode("||",$r[$i]);
  $c_link=$t1[0];
  //$openload=parse_url(urldecode($r[$i]))['host'];
  $indirect="/asasaass/";
  $openload=$t1[1];
  if (preg_match($indirect,$openload)) {
  echo '<TD class="mp"><a href="filme_link.php?file='.urlencode($c_link).'&title='.urlencode(unfix_t($tit.$tit2)).'" target="_blank">'.$openload.'</a></td>';
  } else
  echo '<TD class="mp"><a id="myLink" href="#" onclick="changeserver('."'".urlencode($openload)."','".urlencode($c_link)."'".');return false;">'.$openload.'</a></td>';
  $x++;
  if ($x==2) {
    echo '</TR>';
    $x=0;
  }
}
if ($x < 2 && $x > 0 & $k>2) {
 for ($k=0;$k<2-$x;$k++) {
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
  $from="";
  $link_page="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  $from="";
  $link_page="";
}
  $rest = substr($tit3, -5);
  if (preg_match("/\-(\d+)/",$rest,$m)) {
   $tit3=trim(str_replace($m[0],"",$tit3));
  }
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
echo '<br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="4">Alegeti o subtitrare</td></TR>';
echo '</table>';
echo '<table border="1" width="100%">';
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
