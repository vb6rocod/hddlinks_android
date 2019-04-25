<!DOCTYPE html>
<?php
error_reporting(0);
$pg_tit=urldecode($_GET["title"]);
?>
<html>
   <head>

      <meta charset="utf-8">
      <title><?php echo $pg_tit; ?></title>
      <link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

      <script type="text/javascript" src="../jquery.fancybox.js?v=2.1.5"></script>
      <link rel="stylesheet" type="text/css" href="../jquery.fancybox.css?v=2.1.5" media="screen" />

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
<h2><?php echo $pg_tit; ?></H2>

<table border="1px" width="100%">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
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
$n=0;
$w=0;
$m3uFile="pl/".$pg_tit;
$m3uFile = file($m3uFile);
foreach($m3uFile as $key => $line) {
  if(strtoupper(substr($line, 0, 7)) === "#EXTINF") {
    $t1=explode(",",$line);
    $title=trim($t1[1]);
    $file = trim($m3uFile[$key + 1]);
    if ($file[0]=="#")  $file = trim($m3uFile[$key + 2]);
    if ($pg_tit=="alltvn.m3u")
      $tip_stream="http";
    else {
    $u=parse_url($file);
    $tip_stream=$u["scheme"];
    }
    $mod="direct";
    $from="fara";
    $val_prog="link=".urlencode(fix_t($title));
    if (substr($file, 0, 4) == "http" || $pg_tit == "alltvn.m3u") {
    if (preg_match("/\.m3u8|\.mp4|\.flv|\.ts/",$file))
      $mod="direct";
    else
      $mod="indirect";
    if (strpos($file,"telekomtv.ro") !== false) {
      $mod="indirect";
      $from="fara";
    } elseif ($pg_tit=="alltvn.m3u") {
      $mod="direct";
      $from="gazw";
    }
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    if (strpos($link,"html")=== false) {
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash != "mp")
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    $n++;
    $w++;
    }
  } elseif ($tip_stream=="acestream") {
    if ($n == 0) echo "<TR>"."\n\r";
    $link2="intent:".$file."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
    if ($flash == "mp" || $flash=="chrome")
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link2.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$file.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    $n++;
    $w++;
  } elseif ($tip_stream=="sop") {
    if ($n == 0) echo "<TR>"."\n\r";
    //$link2="intent:".$file."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
    $link2=$file;
    $link3="sop_link.php?file=".$file."&title=".urlencode($title);
    if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link2.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
    echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link3.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    $n++;
    $w++;
  }
    if ($tip_stream == "http" || $tip_stream == "https" || $tip_stream=="acestream" || $tip_stream=="sop") {
    $l_prog="link=".urlencode(fix_t($title));
    if ($tast == "NU") {
   	echo '<td style="text-align:right;width:5px"><a onclick="prog('."'".$l_prog."', '"."')".'"'." style='cursor:pointer;'>"."PROG".'</a></TD>';
    $n++;
    if ($n > 5) {
     echo '</TR>'."\n\r";
     $n=0;
    }
    } else {
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
    }
   }
  }
}
if ($tast == "NU")
if ($n<6) echo "</TR>"."\n\r";
else
if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
