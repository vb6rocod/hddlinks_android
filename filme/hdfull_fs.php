<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
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
//query=8612&tv=0&title=The+Intervention+(2016)&serv=30&hd=NU
//////////////////////////////////////////////////////////////
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
if ($tip=="movie") {
$tit2="";
} else {
$tit2=" - ".$ep_title;
$tip="series";
}
$year="";
$imdbid="";

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function get_value($q, $string) {
   $t1=explode($q,$string);
   return str_between($t1[1],"<string>","</string>");
}
   function generateResponse($request)
    {
        $context  = stream_context_create(
            array(
                'http' => array(
                    'method'  => "POST",
                    'header'  => "Content-Type: text/xml",
                    'content' => $request
                )
            )
        );
        $response     = file_get_contents("http://api.opensubtitles.org/xml-rpc", false, $context);
        return $response;
    }
//echo $link;
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
?>
<html>



   <head>

      <meta charset="utf-8">
      <title><?php echo $tit." ".$tit2; ?></title>
	  <link rel="stylesheet" type="text/css" href="../custom.css" />
     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
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
function openlink1(link) {
  link1=document.getElementById('server').innerHTML;
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
}
function openlink(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  document.getElementById("wait").innerHTML = '<font size="4" color="#ebf442"><b>ASTEPTATI...............</b></font>';

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  link1=document.getElementById('server').innerHTML;
  var the_data = "link=" + link1 + "&title=" + link;
  //alert(the_data);
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
       document.getElementById("wait").innerHTML = '';
       document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function changeserver(s) {
  document.getElementById('server').innerHTML = s;
  //alert (document.getElementById('server').innerHTML);
  //history.back();
}
</script>
<script type="text/javascript">
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
  </head>
   <body><div id="mainnav">
  <a href='' id='mytest1'></a>
<?php
echo '<h2>'.$tit.' '.$tit2.'</H2>';
echo '<BR>';
if ($tip=="series")
$l1=$link."/season-".$sez."/episode-".$ep;
else
$l1=$link;
  $ch = curl_init($l1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://hdfull.me/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close ($ch);
$t1=explode("var ad =",$h3);
$t2=explode("'",$t1[1]);
$ad1=base64_decode($t2[1]);
$key=111;
$n=126;
$out="";
for ($i=0;$i<strlen($ad1);$i++) {
 $c=ord($ad1[$i]);
 if ($c<$n) {
  $out=$out.chr((ord($ad1[$i])+ $key)%$n);
 } else {
  $out=$out.$ad1[$i];
 }
}
$r=array();
$prov=json_decode($out,1);
for ($k=0;$k<count($prov);$k++) {
  $pp=$prov[$k]["provider"];
  //echo $pp;
  $code=$prov[$k]["code"];
  if ($pp==1) $r[]="https://powvideo.cc/iframe-".$code."-920x360.html"; //$r[]="https://powvideo.net/".$code;
  if ($pp==2) $r[]="https://streamplay.me/player-".$code."-920x360.html"; //$r[]="https://streamplay.to/".$code;
  if ($pp==3) $r[]="https://openload.co/f/".$code;
  if ($pp==6) $r[]="https://vev.io/".$code;
  if ($pp==4) $r[]="http://streamcloud.eu/".$code;
  if ($pp==11) $r[]="https://clipwatching.com/embed-".$code.".html"; //$r[]="https://clipwatching.com/".$code.".html";
  if ($pp==12) $r[]="http://gamovideo.com/".$code;
  if ($pp==41) $r[]="https://vidzi.tv/".$code;
  if ($pp==31) $r[]="https://vidoza.net/".$code;
  if ($pp==20) $r[]="https://flix555.com/embed-".$code.".html";
}
  $siteParts = parse_url($r[0]);
  $server_select =$siteParts['host'];
echo '<table border="1" width="100%">';
echo '<TR><TD align="center"><font size="4"><b>Alegeti un server: Server curent:<label id="server">'.$r[0].'</label></b></font></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);

$x=0;
for ($i=0;$i<$k;$i++) {
  //print_r ($value);
  if ($x==0) echo '<TR>';
  $openload=$r[$i];
  $siteParts = parse_url($openload);
  $server =$siteParts['host'];
  if (preg_match("/streamplay1\.|thevideo\.|vev\.|vidup\./",$server)) {
  echo '<TD align="center"><a href="filme_link.php?file='.urlencode($openload).'&title='.$server.'" target="_blank"><b>'.$server.'</b></a></td>';
  } else
  echo '<TD align="center"><a id="myLink" href="#" onclick="changeserver('."'".$openload."'".');return false;"><b>'.$server.'</b></a></td>';
  $x++;
  if ($x>5) {
    echo '</TR>';
    $x=0;
  }
}
echo '</TR></TABLE>';
//echo '<font size="5"><b>Server curent: <label id="server22">'.$r[0].'</label></b></font>';
if ($tip=="movie") {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $tit2="";
  $sez="";
  $ep="";
  $imdbid="";
  $from="putlockerfit";
  $link_page=urlencode($link);
} else {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $sez=$sez;
  $ep=$ep;
  $imdbid="";
  $from="putlockerfit";
  $link_page=urlencode($link);
}
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2));
echo '<br>';
echo '<h2>Alegeti o subtitrare</h2>';
echo '<table border="1" width="100%">';
//echo '<TR><TD style="background-color: lightskyblue;color:black" align="center" colspan="4"><font size="4"><b>Alegeti o subtitrare</b></font></td></TR>';
echo '<TR>';
echo '<TD align="center"><font size="4"><b><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'&page=1">titrari.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</b</font></a></td>';
echo '</TR><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($ep_title));
//$openlink="tip=";
 if ($flash != "mp")
   echo '<TD align="center" colspan="4"><font size="4"><b><a id="viz" onclick="'."openlink1('".$openlink."')".'"'." style='cursor:pointer;'>".'<font size="4">VIZIONEAZA !</b></font></a></td>';
 else
   echo '<TD align="center" colspan="4"><font size="4"><b><a id="viz" onclick="'."openlink('".$openlink."')".'"'." style='cursor:pointer;'>".'<font size="4">VIZIONEAZA !</b></font></a></td>';
echo '</tr>';
echo '</table>';
echo '<BR><table border="0px" width="100%"><TR>'."\n\r";
echo '<TD align="center"><label id="wait"></label></TR></TABLE>';
echo '<br></div>
<table border="0px" width="100%">
<TR>
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5=vizioneaza</b></font></TD></TR></TABLE>
</body>
</html>';
