<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
   $page_title=urldecode($queryArr[2]);
   $search=str_replace("|","&",$search);
}
//$cookie=$base_cookie."digisport.dat";
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
</head>
<body>
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
echo '<a href="libertatea.php?page='.($page-1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="libertatea.php?page='.($page+1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="libertatea.php?page='.($page+1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';


if ($page==1)
  $l = "https://www.libertatea.ro/channel/stiri";
else
  $l = "https://www.libertatea.ro/channel/stiri/page/".$page1;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$videos = explode('<div class="col-xs-6', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1 = explode('href="', $video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];

    $t1 = explode('src="', $video);
    $t2 = explode('"', $t1[1]);
    $image = $t2[0];
    //$image=str_replace("https","http",$image);

    $t1 = explode('alt="', $video);
    $t2 = explode('"', $t1[1]);
    $title = $t2[0];
//    $l1=explode("picture:",$image);
//    $id=$l1[1];
    $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=libertatea&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=libertatea&mod=direct";
  if ($link && $title) {
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td align="center" width="25%"><a href="'.$link1.'" target="_blank"><img src="'.$image.'" width="200px" height="106px"><BR><font size="4">'.$title.'</font></a></TD>';
    else
  echo '<td align="center" width="25%"><font size="4">'.'<a onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="200px" height="106px"><BR><font size="4">'.$title.'</a></font></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="libertatea.php?page='.($page-1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="libertatea.php?page='.($page+1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="libertatea.php?page='.($page+1).','.$search.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
echo "</table>";
?>
<br>
</div>
</body>
</html>
