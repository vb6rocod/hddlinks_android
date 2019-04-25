<!DOCTYPE html>
<?php
include ("../common.php");
$search= urldecode($_GET["link"]);
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

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
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
    //alert (request.responseText);
      off();
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
<script type="text/javascript">
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
        self = evt.target;
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     val_imdb=document.getElementById(id).value;
     prog(val_imdb);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
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
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
//http://www.hdfilm.ro/index.php?p=filme&gen=Actiune&page=1
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";

$n=0;
$w=0;
$ua     =   $_SERVER['HTTP_USER_AGENT'];
//echo $search;
$cookie=$base_cookie."tvmobil.dat";
$l="http://tvpemobil.net/wap/".$search;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"http://tvpemobil.net/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
 $videos = explode('<b>&bull;', $html);
//echo $html;
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $title="";
  $t3=explode("<",$video);
  $title=trim($t3[0]);
  $title=html_entity_decode($title,ENT_QUOTES,'UTF-8');
  $t1=explode('href="',$video);
     $t2=explode('"',$t1[1]);
     $link=$t2[0];
     $link="http://tvpemobil.net/wap/".str_replace("&amp;","&",$link);
  $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=tvmobil&mod=indirect";
  $link="tvpemobil_link.php?".$l;
  $l1="link=".urlencode(fix_t($title));
  $val_prog="link=".urlencode(fix_t($title));
  if ($n==0) echo '<TR>';
  if ($flash != "mp1")
  echo '<td class="mp" align="center"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
  echo '<TD class="mp" style="text-align:center">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".''.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
  $n++;
  $w++;
   if ($tast == "NU") {
   	echo '<td style="text-align:right;width:"5px"><a onclick="prog('."'".$l1."', '"."')".'"'." style='cursor:pointer;'>"."PROG".'</a></TD>';
    $n++;
  if ($n > 5) {
  echo '</tr>';
  $n=0;
  }
  } else {
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
  }
}
if ($tast == "NU")
if ($n<6) echo "</TR>"."\n\r";
else
if ($n<3) echo "</TR>"."\n\r";
echo "</table>";
?>
</div>
<br>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
