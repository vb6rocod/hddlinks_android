<!DOCTYPE html>
<?php
include ("../common.php");
$from=$_GET["from"];
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$imdbid=$_GET["imdb"];
if (isset($_GET["ep_tit"]))
 $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
else
 $ep_tit="";
$title=unfix_t(urldecode($_GET["title"]));
if ($ep_tit)
  $page_tit=$title." ".$ep_tit;
else
  $page_tit=$title;
$link=$_GET["link"];

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
// create the XMLHttpRequest object, according browser
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
function changeserver(link) {
  on();
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = "id="+ link;
  var php_file="rest_sub.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       off();
       alert (request.responseText);
       //document.getElementById("mytest1").href=request.responseText;
      //document.getElementById("mytest1").click();
      history.back();
    }
  }
}
</script>
</head>
<body><div id="mainnav">
<H2></H2>
<?php

//echo $sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$title."&link=".$link;

if ($imdbid) $imdbid=sprintf("%07d", $imdbid);
if ($tip=="movie") {
 if (!$imdbid) {
  $l_rum="https://rest.opensubtitles.org/search/query-".urlencode($title)."/sublanguageid-rum";
  $l_eng="https://rest.opensubtitles.org/search/query-".urlencode($title)."/sublanguageid-eng";
 } else {
  $l_rum="https://rest.opensubtitles.org/search/imdbid-".$imdbid."/sublanguageid-rum";
  $l_eng="https://rest.opensubtitles.org/search/imdbid-".$imdbid."/sublanguageid-eng";
 }
} else { //episode
 if (!$imdbid) {
   $l_rum="https://rest.opensubtitles.org/search/episode-".$ep."/query-".urlencode($title)."/season-".$sez."/sublanguageid-rum";
   $l_eng="https://rest.opensubtitles.org/search/episode-".$ep."/query-".urlencode($title)."/season-".$sez."/sublanguageid-eng";
 } else {
   $l_rum="https://rest.opensubtitles.org/search/episode-".$ep."/imdbid-".$imdbid."/season-".$sez."/sublanguageid-rum";
   $l_eng="https://rest.opensubtitles.org/search/episode-".$ep."/imdbid-".$imdbid."/season-".$sez."/sublanguageid-eng";
 }
}
 $key=base64_decode("dHJhaWxlcnMudG8tVUE=");
$head=array('X-Requested-With: XMLHttpRequest',
"X-User-Agent: ".$key);
//$head=array
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_URL, $l_rum);
  $h_rum = curl_exec($ch);
  curl_setopt($ch, CURLOPT_URL, $l_eng);
  $h_eng = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  $r_rum=json_decode($h_rum,1);
  $r_eng=json_decode($h_eng,1);
$s_rum=array();
$s_eng=array();
if ($tip=="movie") {
for ($z=0;$z<count($r_rum);$z++) {
 if ($r_rum[$z]['SeriesSeason']==0 ) {
  $s_rum[]=array($r_rum[$z]['SubFileName'],$r_rum[$z]['SubDownloadLink']);
 }
}

for ($z=0;$z<count($r_eng);$z++) {
 if ($r_eng[$z]['SeriesSeason']==0 ) {
  $s_eng[]=array($r_eng[$z]['SubFileName'],$r_eng[$z]['SubDownloadLink']);
 }
}
} else {
for ($z=0;$z<count($r_rum);$z++) {
  $s_rum[]=array($r_rum[$z]['SubFileName'],$r_rum[$z]['SubDownloadLink']);
}

for ($z=0;$z<count($r_eng);$z++) {
  $s_eng[]=array($r_eng[$z]['SubFileName'],$r_eng[$z]['SubDownloadLink']);
}
}
$nn=max(count($s_rum),count($s_eng));
$k=intval($nn/10) + 1;
$n=0;
echo '<h2>'.$page_tit.'</H2>';
echo '<table border="1" width="100%">';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '</TR>';
echo '</table>';
echo '<table border="1" width="100%">';
for ($m=0;$m<$nn;$m++) {
  if (isset($s_rum[$m])) {
    $display=$s_rum[$m][1];
    $display1="ro-".$s_rum[$m][0];
  } else {
    $display="";
    $display1="";
  }
  if (isset($s_eng[$m])) {
    $display2=$s_eng[$m][1];
    $display3="en-".$s_eng[$m][0];
  } else {
    $display2="";
    $display3="";
  }
  echo '<TR><TD>'.($n+1).'</TD>
  <TD><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".$display."'".');return false;">'.$display1.'</a></font></TD>
  <TD><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".$display2."'".');return false;">'.$display3.'</a></font></TD>

  </TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table>';


echo '</body></html>';

?>
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
