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
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);
if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next="opensubtitles_new.php?page=".($page+1)."&".$p;
$prev="opensubtitles_new.php?page=".($page-1)."&".$p;
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
  var php_file="opensubtitles_new_sub.php";
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
if (file_exists($base_pass."opensubtitlesc.txt")) {
 $key=file_get_contents($base_pass."opensubtitlesc.txt");
} else {
 $key="";
}
$ww=$key;
if ($imdbid) $imdbid=round($imdbid);
if ($tip=="movie") {
 if (!$imdbid) {
  $search=array(
   'query' => $title,
   'type' => 'movie',
   'languages' => 'ro,en',
   'order_by' => 'language',
   'page' => $page
  );
 } else {
  $search=array(
   'type' => 'movie',
   'imdb_id' => $imdbid,
   'languages' => 'ro,en',
   'order_by' => 'language',
   'page' => $page
  );
 }
} else { //episode
 if (!$imdbid) {
  $search=array(
   'query' => $title,
   'type' => 'episode',
   'episode_number' => $ep,
   'season_number' => $sez,
   'languages' => 'ro,en',
   'order_by' => 'language',
   'page' => $page
  );
 } else {
  $search=array(
   'type' => 'episode',
   'parent_imdb_id' => $imdbid,
   'episode_number' => $ep,
   'season_number' => $sez,
   'languages' => 'ro,en',
   'order_by' => 'language',
   'page' => $page
  );
 }
}
$ua = $_SERVER['HTTP_USER_AGENT'];
$l="https://api.opensubtitles.com/api/v1/subtitles?";
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://opensubtitles.stoplight.io/',
'Content-Type: application/json',
'Api-Key: '.$key,
'Origin: https://opensubtitles.stoplight.io',
'Connection: keep-alive');
$q=http_build_query($search);
$l=$l.$q;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $res=json_decode($h,1);
  //print_r ($res);
  $subs=array();
  for ($k=0;$k<count($res['data']);$k++) {
   $subs[]=array($res['data'][$k]['attributes']['language'],
     $res['data'][$k]['attributes']['files'][0]['file_id'],
     $res['data'][$k]['attributes']['files'][0]['file_name'],
     $res['data'][$k]['attributes']['release'],
     $res['data'][$k]['attributes']['comments']
   );
  }
  //print_r ($subs);
//arsort($arrsub);
//print_r ($arrsub);
$nn=count($subs);
$k=intval($nn/10) + 1;
$n=0;
echo '<h2>'.$title.'</H2>';
echo '<table border="1" width="100%">
<TR>';
echo '<TD colspan="2" align="left">';
if ($page>1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
echo '</TR>';
echo '</table>';
echo '<table border="1" width="100%">';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '<TD></TD></TR>';
foreach ($subs as $key => $val) {
  if ($subs[$key][4]) {
   if ($subs[$key][2])
     $display=$subs[$key][0]." - ".$subs[$key][2]." (".$subs[$key][4].")";
   else
     $display=$subs[$key][0]." - ".$subs[$key][3]." (".$subs[$key][4].")";
  } else {
   if ($subs[$key][2])
    $display=$subs[$key][0]." - ".$subs[$key][2];
   else
    $display=$subs[$key][0]." - ".$subs[$key][3];
  }
  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".$subs[$key][1]."'".');return false;">'.$display.'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table>';
echo '<table border="1" width="100%">';
echo '<tr>';
echo '<TD colspan="2" align="left">';
if ($page>1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
echo '</TR>';
echo '</table>';
if (!$ww) {
echo '<br><p>';
echo 'Visit https://opensubtitles.stoplight.io/docs/opensubtitles-api, get an API-KEY.
Put this key in file "opensubtitlesc.txt" and copy this file to "parole" folder.</p>';
}
echo '</body></html>';

?>
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
