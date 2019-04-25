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
$tit2=$sez."x".$ep." - ".$ep_title;
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
$l=$link;
//$l="https://dwatchmovies.com/movies/slender-man-2018-online-free-streaming-dwatchmovies";
//$l="https://topeuropix.net/movies/slender-man-2018-online-free-hd-with-subtitles-europix";
$base=str_replace(substr(strrchr($l, "/"), 1),"",$l);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

  $r=array();
  if ($tip=="series") {
  $videos = explode('id="chserv', $h);
  unset($videos[0]);
  $videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2=explode('"',$t1[1]);
  $l1=$base.$t2[0];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (strpos($h,"404 Not Found") === false) {
  //echo $h."=============================================================";
  $t1=explode('id="dodji">',$h);
  $t3=explode("<Script Language='Javascript'>",$t1[1]);
  //echo $t3[1]."=============================================================";
  $t4=explode('</script>',$t3[1]);
  $h1=$t4[0];
  //$h1=$h;
  preg_match_all("@\\\(x)?([0-9a-fA-F]{2,3})@",$h1,$m);
  for ($k=0;$k<count($m[0]);$k++) {
    $h1=str_replace($m[0][$k],chr($m[1][$k]?hexdec($m[2][$k]):octdec($m[2][$k])),$h1);
  }
  //echo $h1."=============================================================";
  if (preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=]*")/', $h1, $m)) {
  //print_r ($m[0]);
  $link_ep=str_replace('"',"",$m[0][$ep-1]);
  }
  //echo $link_ep."\n";
  if (strpos($link_ep,"dwatchmovies") !== false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link_ep);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    if (strpos($h1,"404 Not Found") === false) {
    $link_ep="";
    $link_ep=str_between($h1,"iframe src='","'");
    if ($link_ep && strpos($link_ep,"http") !== false) $r[]=$link_ep;
    }
  } else {
    if (strpos($link_ep,"http") !== false) $r[]=$link_ep;
  }
}
}
} else {
  //echo $h;
  $t1=explode("change(id)",$h);
  //echo $t1[0];
  $t2=explode("document.getElementById",$t1[1]);
  $h1=$t2[0];
  //echo $h1;
  preg_match_all("@\\\(x)?([0-9a-fA-F]{2,3})@",$h1,$m);
  for ($k=0;$k<count($m[0]);$k++) {
    $h1=str_replace($m[0][$k],chr($m[1][$k]?hexdec($m[2][$k]):octdec($m[2][$k])),$h1);
  }
  //echo $h1."=============================================================";
  if (preg_match_all('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,\=]*(\"|\'))/', $h1, $m)) {
  //print_r ($m);
  for ($k=0;$k<count($m[0]);$k++) {
  $link_ep=str_replace('"',"",$m[0][$k]);
  $link_ep=str_replace("'","",$link_ep);
  //}
  if (strpos($link_ep,"dwatchmovies") !== false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link_ep);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    if (strpos($h1,"404 Not Found") === false) {
    $link_ep="";
    $link_ep=str_between($h1,"iframe src='","'");
    if ($link_ep && strpos($link_ep,"http") !== false) $r[]=$link_ep;
    }
  } else {
    if (strpos($link_ep,"http") !== false) $r[]=$link_ep;
  }
  }
}
}
//https://vev.io/embed/78r81xm7ym34  ==> https://thevideo.me/embed-afdtxrbc8wrg.html
echo '<table border="1" width="100%">';
echo '<TR><TD align="center" colspan="'.count($r).'"><font size="4"><b>Alegeti un server: Server curent:<label id="server">'.$r[0].'</label></b></font></td></TR>';

$k=count($r);
echo '<TR>';
for ($i=0;$i<$k;$i++) {
  //print_r ($value);
  $openload=$r[$i];
  $siteParts = parse_url($openload);
  $server =$siteParts['host'];
  echo '<TD align="center"><font size="4"><b><a id="myLink" href="#" onclick="changeserver('."'".$openload."'".');return false;">'.$server.'</a></b></font></td>';
}
//echo '<font size="5"><b>Server curent: <label id="server22">'.$r[0].'</label></b></font>';
if ($tip=="movie") {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $sez="";
  $ep="";
  $imdbid="";
  $from="pix";
  $link_page=urlencode($link);
} else {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $sez=$sez;
  $ep=$ep;
  $imdbid="";
  $from="pix";
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
$openlink=urlencode(fix_t($tit." ".$ep_title))."&image=".$image;
//echo unfix_t(urldecode($openlink));
//tip=series&
//link=https://topeuropix.net/tvseries2/madam-secretary-online/madam-secretary-season-1-online-free-hd-streaming-europix&
//title=Madam Secretary&sez=1&ep=1&ep_title=Episode 1 - "Pilot"&image=https://hdeuropix.io/images/madam-secretary.jp

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
