<!DOCTYPE html>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}


//$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$tit3."&link=".$link_page;
$from=$_GET["from"];
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$imdbid=$_GET["imdb"];
$imdbid = str_replace("tt","",$imdbid);
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
  var php_file="opensubtitles_sub.php";
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
<body>
<H2></H2>
<?php

//echo $sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$title."&link=".$link;
if (file_exists($base_pass."opensubtitlesc.txt")) {
 $h=file_get_contents($base_pass."opensubtitlesc.txt");
 $t1=explode("|",$h);
 $user=$t1[0];
 $pass=$t1[1];
} else {
 $user="";
 $pass="";
}
$ua = $_SERVER['HTTP_USER_AGENT'];
$f=$base_cookie."opensubc.dat";
$token="";
  $a=array(
    'username' => $user,
    'password' => $pass
  );
  $post=json_encode($a);
  $l="https://www.opensubtitles.com/api/v1/login";
  $head=array('Accept: application/json',
  'Content-Type: application/json');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $res=json_decode($h,1);
  $status=$res['status'];
  If ($status == "200") {
    $token=$res['token'];
    file_put_contents($f,$token);
    $head=array('Accept: application/json',
    'Content-Type: application/json',
    'Authorization: '.$token);
  }
$arrsub = array();
if ($token) {
if ($tip=="movie") {
if ($imdbid) {
  $search=array(
   'imdbid' => $imdbid,
   'languages' => 'ro,en'
  );
  $q=http_build_query($search);
  $l="https://www.opensubtitles.com/api/v1/find/movie?".$q;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $res=json_decode($h,1);

  for ($k=0;$k<count($res['data']);$k++) {
   $arrsub[]=array($res['data'][$k]['attributes']['language'],
     $res['data'][$k]['attributes']['files'][0]['file_name'],
     $res['data'][$k]['attributes']['files'][0]['id']
   );
  }
}
} else {  // start tv
if ($imdbid) {
  $search=array(
   'query' => $title
  );
  $q=http_build_query($search);
  $l="https://www.opensubtitles.com/api/v1/search/tv?".$q;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $res=json_decode($h,1);
  //print_r ($res['data']);
  $id="";
  for ($k=0;$k<count($res['data']); $k++) {
   if (round($res['data'][$k]['attributes']['imdbid']) == round($imdbid))
    $id=$res['data'][$k]['id'];
  }
  $search=array(
   'parent_id' => $id,
   'parent_imdbid' => $imdbid,
   'season_number' => $sez,
   'episode_number' => $ep,
   'languages' => 'ro,en'
  );
  $q=http_build_query($search);
  $l="https://www.opensubtitles.com/api/v1/find/tv?".$q;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $res=json_decode($h,1);
  print_r ($res['data']);
  for ($k=0;$k<count($res['data']);$k++) {
   $arrsub[]=array($res['data'][$k]['attributes']['language'],
     $res['data'][$k]['attributes']['files'][0]['file_name'],
     $res['data'][$k]['attributes']['files'][0]['id']
   );
  }
}
}
if ($imdbid) {
arsort($arrsub);
print_r ($arrsub);
$nn=count($arrsub);
$k=intval($nn/10) + 1;
$n=0;
echo '<table border="1" width="100%"><tr><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="'.($k-0).'"><font size="6"><b>'.$page_tit.'</b></font></TD></TR><TR>';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '<TD></TD></TR>';
foreach ($arrsub as $key => $val) {

  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".$arrsub[$key][2]."'".');return false;">'.$arrsub[$key][0]." - ".$arrsub[$key][1].'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table>';
}
} else {
  echo 'Server error!';
}
echo '</body></html>';

?>
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
