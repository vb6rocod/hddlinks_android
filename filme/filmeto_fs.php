<!doctype html>
<?php
include ("../common.php");
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
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_tit=$_GET["ep_tit"];
if ($tip=="movie") {
$tit2="";
} else {
$tit2=$ep_tit;
$sezon=$sez;
$episod=$ep;
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
$head=array('Accept: text/html, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://www.filme-online.to/film/gan/Perdida',
'X-Requested-With: XMLHttpRequest');
$l=$link;
$src=array();
$r=array();
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $t1=explode('data-gid="',$h2);
  $t2=explode('"',$t1[1]);
  $gid=$t2[0];
  $t1=explode("mid=",$h2);
  $t2=explode('&',$t1[1]);
  $mid=$t2[0];
  $l="https://www.filme-online.to/ajax/mep.php?id=".$mid;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  $h4=json_decode($h3,1);
  //print_r ($h);
//explode('<div id="',$ep);
$videos = explode('<div id="', $h4["html"]);

unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('data-id="',$video);
  $t2=explode('"',$t1[1]);
  $server=$t2[0];
  $t3=explode('"',$t1[2]);
  $eid=$t3[0];
  $t1=explode('data-epNr="',$video);
  $t2=explode('"',$t1[1]);
  $epNr=$t2[0];
  $t1=explode('data-so="',$video);
  $t2=explode('"',$t1[1]);
  $sc=$t2[0];
  $t1=explode('data-index="',$video);
  $t2=explode('"',$t1[1]);
  $epIndex=$t2[0];
  $l1="https://www.filme-online.to/ajax/movie_embed.php?";
  $l1 =$l1."eid=".$eid."B&lid=undefined&ts=&up=0&mid=".$mid;
  $l1 =$l1."&gid=".$gid."&epNr=".$epNr."&type=film&server=".$server."&epIndex=".$epIndex."&so=".$sc."&srvr=";
  //echo $l1."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h5 = curl_exec($ch);
  $src=json_decode($h5,1);
  //print_r ($src);
  $status=$src["status"];
  if ($status==1) {
  $sub=$src["subtitles"];
  $openload=$src["src"];
  if ($sub) {
    $t1=explode("?c1_file=",$openload);
    $openload=$t1[0];
    $t2=explode("&",$t1[1]);
    $srt_ext=$t2[0];
    //echo $srt_ext;
  }
    $r[]=$openload;
  }
}
//echo $srt_ext;
if ($srt_ext) {
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $srt_ext);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   //curl_setopt($ch,CURLOPT_REFERER,"http://roshare.info");
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING, "");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h=curl_exec($ch);
   curl_close($ch);
   if ($h) file_put_contents($base_sub."sub_extern.srt",$h);
}
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
function ajaxrequest1(title, from) {
  link=document.getElementById('server').innerHTML;
  msg="link1.php?file=" + link + "," + title;
  window.open(msg);
}
function ajaxrequest(title, from) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  document.getElementById("wait").innerHTML = '<font size="4" color="#ebf442"><b>ASTEPTATI...............</b></font>';
  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  link=document.getElementById('server').innerHTML;
  var the_data = "title="+ title +"&link="+link;
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
  $siteParts = parse_url($r[0]);
  $server_select =$siteParts['host'];
//echo '<h2 style="background-color:deepskyblue;color:black;">'.$tit.' '.$tit2.'</H2>';
echo '<table border="0px" width="100%"><TR>'."\n\r";
echo '<TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px"><font size="6px" color="#64c8ff"><b>'.$tit." ".$tit2.'</b></font></td>';
echo '</TR></TABLE>';
//echo '<font size="5"><b>Server curent: <label id="server22">'.$r[0].'</label></b></font>';
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
if ($tip=="movie") {
  $tit3=trim(preg_replace("/\(\s*(\d+)\s*\)/","",$tit));
  $sez="";
  $ep="";
  $imdbid="";
  $from="123netflix";
  $link_page=urlencode($link);
}
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=";
echo '</tr></table><br>';
echo '<table border="1" width="100%">';
if ($srt_ext)
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4"><b>Alegeti o subtitrare (filmul are subtitrare)</b></font></td></TR>';
else
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4"><b>Alegeti o subtitrare (filmul nu are subtitrare)</b></font></td></TR>';

echo '<TR>';
echo '<TD align="center"><font size="4"><b><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'">titrari.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</b</font></a></td>';
echo '</TR><TR>';
 if ($flash != "mp")
   echo '<TD align="center" colspan="4"><a id="viz" onclick="ajaxrequest1('."'".urlencode($tit3)."', '".urlencode($from)."')".'"'." style='cursor:pointer;'>".'<font size="4"><b>VIZIONEAZA !</b></font</a></td>';
 else
   echo '<TD align="center" colspan="4"><a id="viz" onclick="ajaxrequest('."'".urlencode($tit3)."', '".urlencode($from)."')".'"'." style='cursor:pointer;'>".'<font size="4"><b>VIZIONEAZA !</b></font></a></td>';
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
