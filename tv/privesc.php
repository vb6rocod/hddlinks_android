<!DOCTYPE html>
<?php
include ("../common.php");
$page = $_GET["page"];
$search= $_GET["link"];
$page_title=urldecode($_GET["title"]);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
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

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  on();
  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
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
    //alert (request.responseText);
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function prog(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='prog.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
    }
  }
}
</script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
#overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 2;
    cursor: pointer;
}

#text{
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 50px;
    color: white;
    transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
}
</style>
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
   <div id="mainnav">
<H2></H2>
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
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="privesc.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="privesc.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="privesc.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

$n=0;
$page1 = $page-1;
//$search1=mb_convert_encoding($search, 'UTF-8');
$search1=str_replace("*",",",$search);
//echo $search1;
if ($page > 1)
$l=$search1."/".$page1;
else
$l=$search1;
$l=urlencode($l);
$l=str_replace("%3A",":",$l);
$l=str_replace("%2F","/",$l);
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$videos = explode("<div class='col-xs-12", $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $t1 = explode("href='", $video);
  $t2 = explode("'",$t1[1]);
  $link="https://www.privesc.eu".$t2[0];

  $t1=explode("alt='",$video);
  $t2=explode("'",$t1[1]);
  $title=trim($t2[0]);
  //$title=fix_s($title);
//http://storage.privesc.eu/thumnails/49569.jpg
  $t1=explode("src='",$video);
  $t2=explode("'",$t1[1]);
  $image=$t2[0];

    $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=privesceu&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=privesceu&mod=direct";
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="25%"><a href="'.$link1.'" target="_blank"><img src="'.$image.'" width="200px" height="150px"><BR>'.$title.'</a></TD>';
    else
  echo '<td class="mp" align="center" width="25%">'.'<a onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="200px" height="150px"><BR>'.$title.'</a></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="privesc.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="privesc.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="privesc.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo "</table>";
?>
</div>
<br>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
