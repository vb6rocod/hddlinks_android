<!DOCTYPE html>
<?php
include ("../common.php");
error_reporting(0);

$search=$_GET["canal"];
$doc_id=$_GET['videoid'];


$page_title=$search;
$width="200px";
$height=intval(200*(128/227))."px";
$base=basename($_SERVER['SCRIPT_FILENAME']);


//https://developers.facebook.com/tools/explorer/
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $page_title; ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">

function ajaxrequest1(link) {
  msg="link1.php?file=" + link;
  window.open(msg);
}
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = "link=" + link;
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
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
</script>

</head>
<body>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
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
$n=0;
$w=0;
$l="https://www.facebook.com/watch/live/?v=".$doc_id."&ref=watch_permalink";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/83.0";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h1 = curl_exec($ch);
      curl_close($ch);
      //$h1=file_get_contents($l);
      $h1=str_replace('<!--','',$h1);
      $h1=str_replace('-->','',$h1);
      $h1 = html_entity_decode($h1,ENT_QUOTES);
      //echo $h1;
$x=array();
$videos=explode('data-video-channel-id="',$h1);
unset($videos[0]);
$videos = array_values($videos);
$s=array();
foreach($videos as $video) {
 $t1=explode("background-image: url('",$video);
 $t2=explode("'",$t1[1]);
 $image=$t2[0];
 preg_match("/(\d+)\/\?\_\_so\_\_\=permalink\&/",$video,$m);
 $id=$m[1];
 if (preg_match("/\>((\d+\:)?\d+\:\d+)\</",$video,$n))
  $durata=$n[1];
 else
  $durata="8:00:00";
 $t1=explode('<span class=',$video);
 $t2=explode(">",$t1[1]);
 $t3=explode("<",$t2[1]);
 $title=$t3[0];
 if (strlen($title) < 2) $title="Clip video";
 $x[]=array($title,$id,$image,$durata);
}
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%">'."\n\r";
$n=0;
//print_r ($x);
//if (preg_match_all("/\<td class\=\"\S+\"\>\<.*?href\=\"(\S+)\"\s+aria\-label\=\"(.*?)\".*?src\=\"(\S+)\"/ms",$h,$m)) {
for ($k=0;$k<count($x);$k++) {
  $title=$x[$k][0];
  $image=$x[$k][2];
  $durata=$x[$k][3];
  if ($title)
    $title=$title." (".$durata.")";
  else
    $title=$durata;
   $link1="".urlencode("https://facebook.com?video_id=".$x[$k][1])."&title=".urlencode($title);
  if ($n==0) echo '<TR>';

  if ($tast == "NU") {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';

  } else {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  $w++;
  }
  $n++;
  if ($n == 3) {
  echo '</tr>';
  $n=0;
  }
}


echo "</table>";
////////////////////////////////////////////////////
$x=array();
$videos=explode('a class="',$h1);
unset($videos[0]);
$videos = array_values($videos);
$s=array();
foreach($videos as $video) {
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 //echo $t2[0];
 if (preg_match("/videos\/.+\/(\d+)\//",$t2[0],$m)) {
 $id=$m[1];
 $t1=explode('data-src="',$video);
 $t2=explode('"',$t1[1]);
 $image=$t2[0];
 $t1=explode('href="',$video);
 $t2=explode('</div>',$t1[2]);
 //echo $t2[0];
 $t3=explode('<div',$t2[1]);
 $title=$t3[0];
 $t4=explode(">",$t3[1]);
 $t5=explode("<",$t4[1]);
 $d=$t5[0];
 if (preg_match("/(\d+\:)?\d+\:\d+/",$d,$n))
  $durata=$n[0];
 else
  $durata="8:00:00";
 if (strlen($title) < 2) $title="Clip video";
 $x[]=array($title,$id,$image,$durata);
 }
}
echo '<table border="1px" width="100%">'."\n\r";
$n=0;
//print_r ($x);
//if (preg_match_all("/\<td class\=\"\S+\"\>\<.*?href\=\"(\S+)\"\s+aria\-label\=\"(.*?)\".*?src\=\"(\S+)\"/ms",$h,$m)) {
for ($k=0;$k<count($x);$k++) {
  $title=$x[$k][0];
  $image=$x[$k][2];
  $durata=$x[$k][3];
  if ($title)
    $title=$title." (".$durata.")";
  else
    $title=$durata;
   $link1="".urlencode("https://facebook.com?video_id=".$x[$k][1])."&title=".urlencode($title);
  if ($n==0) echo '<TR>';

  if ($tast == "NU") {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';

  } else {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  $w++;
  }
  $n++;
  if ($n == 3) {
  echo '</tr>';
  $n=0;
  }
}


echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
