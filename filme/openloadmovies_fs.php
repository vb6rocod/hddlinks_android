<!doctype html>
<?php
include ("../common.php");
include ("../util.php");
error_reporting(0);
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
  msg="openloadmovies_link.php?q=" + link1 + "&link=" + link;
  window.open(msg);
}
function openlink(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  document.getElementById("wait").innerHTML = '<font size="4" color="#ebf442"><b>ASTEPTATI...............</b></font>';

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  link1=document.getElementById('server').innerHTML;
  var the_data = "q=" + link1 + "&link=" + link;
  //alert(the_data);
  var php_file="openloadmovies_link.php";
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
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$r=parse_url($link);
//print_r ($r);
//echo $link;
//die();
$host=$r["host"];
$requestLink=$link;
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
if (strpos($html,"503 Service") !== false) {
$head=array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate, br',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
 if (strpos($h,"503 Service") !== false) {
  if (strpos($h,'id="cf-dn') === false)
   $q= getClearanceLink_old($h,$requestLink);
  else
   $q= getClearanceLink($h,$requestLink);

  curl_setopt($ch, CURLOPT_URL, $q);
  $h = curl_exec($ch);
  curl_close($ch);
 } else {
    curl_close($ch);
 }

  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
}
  //$t1=explode('imdb.com/title/tt',$html);
  //$t2=explode('"',$t1[1]);
  //$imdb=$t2[0];
  $imdb="";
  //preg_match("/input name=\"(\w+)\" type=\"hidden\" value=\"(\w+)\"/",$html,$m);
  //print_r ($m);
  //die();
  //echo $html;
$t1=explode('LoadPlayer("',$html);
$t2=explode('"',$t1[1]);
$id=$t2[0];
$t3=explode(',"',$t1[1]);
$t4=explode('"',$t3[1]);
$data=$t4[0];
$post="id=".$id."&data=".$data;
//echo $post;
$l="https://openloadmovies.net/wp-content/plugins/apiplayer/load.php";
$l="https://".$host."/wp-content/plugins/apiplayer/load.php";
$head=array('X-Requested-With: XMLHttpRequest');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://openloadmovies.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $html;
$t1=explode('sources: [',$html);
$t2=explode('],',$t1[1]);
$s=json_decode("[".$t2[0]."]",1);
//print_r ($s);
$r=array();
for ($k=0;$k<count($s);$k++) {
  $r[]=$s[$k]["label"];
}
//print_r ($r);
/*
$l="http://hdpopcorns.co/select-movie-quality.php";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://hdpopcorns.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $html = curl_exec($ch);
  curl_close ($ch);
 //echo $html;
$videos = explode('Quality:<strong>', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('<',$video);
 $q=$t1[0];
 $t2=explode('href="',$video);
 $t3=explode('"',$t2[1]);
 $link2=$t3[0];
}
*/


 // $server_select =$siteParts['host'];
echo '<table border="1" width="100%">';
echo '<TR><TD align="center"><font size="4"><b>Alegeti calitate: Calitate aleasa::<label id="server">'.$r[0].'</label></b></font></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
echo '<TR>';
for ($i=0;$i<$k;$i++) {
  //print_r ($value);
  $openload=$r[$i];
  //$siteParts = parse_url($openload);
  $server =$openload;
  echo '<TD align="center"><a id="myLink" href="#" onclick="changeserver('."'".$openload."'".');return false;"><b>'.$server.'</b></a></td>';
}
echo '</TR></TABLE>';
//echo '<font size="5"><b>Server curent: <label id="server22">'.$r[0].'</label></b></font>';
if ($tip=="movie") {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $tit2="";
  $sez="";
  $ep="";
  $imdbid=$imdb;
  $from="putlockerfit";
  $link_page=urlencode($link);
} else {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $sez=$sez;
  $ep=$ep;
  $imdbid=$imdb;
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
  $openlink=$link."&title=".urlencode(fix_t($tit3));
else
  $openlink=$link."&title=".urlencode(fix_t($ep_title));
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
