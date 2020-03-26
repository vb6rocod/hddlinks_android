<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="tvhd-online";
$width="200px";
$height="278px";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = link;
  var php_file='link1.php';
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

function isValid(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    }
    return true;
}
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     }
   }
function isKeyPressed(event) {
  if (event.ctrlKey) {
    id = "imdb_" + event.target.id;
    val_imdb=document.getElementById(id).value;
    msg="imdb.php?" + val_imdb;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
  }
}
function prog(link) {
    msg="imdb.php?" + link;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
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
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
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
$w=0;
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$cookie=$base_cookie."tvhd.dat";
$l="http://tvhd-online.com/filme/26/filme.html";
$post="uid=traianp&pwds=113298&submit=&logare=";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$videos = explode('a class="title', $html);
unset($videos[0]);
$n=0;
$videos = array_values($videos);
  foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2=explode('"',$t1[1]);
  $link="http://tvhd-online.com".$t2[0];
  $t3=explode('item-title">',$video);
  $t4=explode('<',$t3[1]);
  $title=$t4[0];
  $t1=explode('src="',$video);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
  $year="";
  $imdb="";
  $rest = substr($title, -6);
  if (preg_match("/\(?(\d+)\)?/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }
    $val_prog="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
    $link1="link1.php?file=".$link."&title=".urlencode(fix_t($title));
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title));

  if ($n==0) echo '<TR>';
  if ($flash != "mp") {
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link1.'" target="_blank" onmousedown="isKeyPressed(event)">
  <img id="myLink'.($w*1).'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a>
  <a onclick="prog('."'".$val_prog."')".'"'." style='cursor:pointer;'>"." *".'</a>
  </TD>';
  }  else
  echo '<td class="mp" align="center" width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".'<img id="myLink'.($w*1).'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
  $w++;
  $n++;
  if ($n == 4) {
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
