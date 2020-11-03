<!DOCTYPE html>
<?php
include ("../common.php");

$page_title=urldecode($_GET['title']);
$width="200px";
$height=intval(200*(214/380))."px";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
   on();
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

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
<a href='' id='mytest1'></a>

<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
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
echo '<h2>'.$page_title.'</h2>';
echo '<table border="1px" width="100%">'."\n\r";
$l="http://revo.ddns.me:8080/get.php?username=Florin-Restream-Filme&password=rtFBXLaKD9&type=m3u_plus&output=mpegts";
$lines = file($l);
$r=array();
foreach ($lines as $nn => $line) {
if (preg_match("/\#EXTINF.+tvg\-name\=\"(.*?)\".+group\-title\=\"(.*?)\"/",$line,$m)) {
  //print_r ($m);
  $r[$m[2]][$m[1]]=$lines[$nn+1];
}
}

foreach ($r as $key=>$value ) {
 if ($key==$page_title) {
 foreach ($value as $key1=>$value1) {
  $link=trim($value1);
  $title=$key1;
 $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=&mod=direct";
 $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=&mod=direct";

  if ($n == 0) echo "<TR>"."\n\r";
  if ($flash != "mp")
  echo '<td class="mp" width="25%"><a href="'.$link1.'" target="_blank">'.$title.'</a></TD>';
  else
  echo '<TD class="mp"  width="25%">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".''.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
}
}
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
<BODY>
</HTML>
