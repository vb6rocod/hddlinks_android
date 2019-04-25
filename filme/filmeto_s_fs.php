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
//ink=https%3A%2F%2Fwww.filme-online.to%2Fajax%2Fmovie_embed.php%3Feid%3D1420764B%26lid%3Dundefined%26ts%3D%26up%3D0%26mid%3Dgb1%26gid%3DgKt%26epNr%3D1%26type%3Dtv%26server%3DGF14%26epIndex%3D0%26so%3D1%26srvr%3D
//page_tit=A+Place+To+Call+Home+-+Season+6ep_tit=Ep.+1+-++For+Better+or+Worsesp=1&image=https://m.media-amazon.com/images/M/MV5BMjE5Mzk4Mzg0Ml5BMl5BanBnXkFtZTgwMTA4MzU3MzE@._V1_UY268_CR4,0,182,268_AL_.jpg
$tit=unfix_t(urldecode($_GET["page_tit"]));
if (preg_match("/\-\s*season\s*(\d+)/i",$tit,$m)) {
  //print_r ($m);
  $sez=$m[1];
  $tit_serial=trim(preg_replace("/\-\s*season\s*(\d+)/i","",$tit));
} else {
  $sez="1";
  $tit_serial=$tit;
}
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip="series";
//$sez=$_GET["sez"];
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
'Referer: https://www.filme-online.to/',
'X-Requested-With: XMLHttpRequest');
$l=$link;
$src=array();
$r=array();

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
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
//}
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

  $imdbid="";
  $from="123netflix";
  $link_page=urlencode($link);
  $tip="series";
  $tit3=urlencode(fix_t($tit." ".$tit2));
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit_serial))."&link=".$link_page."&ep_tit=".urlencode(fix_t($ep_tit));
echo '</tr></table><br>';
echo '<table border="1" width="100%">';
if ($srt_ext)
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4"><b>Alegeti o subtitrare (episodul are subtitrare)</b></font></td></TR>';
else
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4"><b>Alegeti o subtitrare (episodul nu are subtitrare)</b></font></td></TR>';

echo '<TR>';
echo '<TD align="center"><font size="4"><b><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'">titrari.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</b</font></a></td>';
echo '</TR><TR>';
 if ($flash != "mp")
   echo '<TD align="center" colspan="4"><font size="4"><b><a id="viz" onclick="ajaxrequest1('."'".urlencode($tit3)."', '".urlencode($from)."')".'"'." style='cursor:pointer;'>".'<font size="4">VIZIONEAZA !</b></font></a></td>';
 else
   echo '<TD align="center" colspan="4"><font size="4"><b><a id="viz" onclick="ajaxrequest('."'".urlencode($tit3)."', '".urlencode($from)."')".'"'." style='cursor:pointer;'>".'<font size="4">VIZIONEAZA !</b></font></a></td>';
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
