<!DOCTYPE html>
<?php
include ("../common.php");
$pg_title=urldecode($_GET["title"]);
$link_pg=$_GET["link"];
$cat=$_GET["cat"];
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $pg_title; ?></title>
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
echo '<h2>'.$pg_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo "<TR>"."\n\r";
$l="http://mywebtv.info/getch";
$post="main=get_ch&idcat=".$cat."&tip=0";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $html;
$t1=explode('a title="'.$pg_title,$html);
$t2=explode('<li',$t1[1]);
$html=$t2[0];
//echo $html;
$videos = explode('onClick="play(', $html);
//echo count($videos);
//print_r ($videos);
if (count($videos) > 1) {
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];

    $t1=explode("'",$video);
    $link=$t1[1];
    $t1=explode(",",$video);
    $sursa=$t1[1];
    
    $t1=explode('class="strm">',$video);
    $t2=explode('<',$t1[1]);
    $title=trim($t2[0]);
    if ($sursa > 0)
    $l="http://mywebtv.info".$link_pg."/sursa-".($sursa+1);
    else
    $l="http://mywebtv.info".$link_pg;
    //echo $l;
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
    curl_setopt($ch,CURLOPT_REFERER,$l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close ($ch);
    //echo $h;
    $t1=explode('id="player">',$h);
    $t2=explode("/div",$t1[1]);
    $h1=$t2[0];
    //echo $h1;
    if (preg_match('/((sop)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $tip="sop";
      $link=$m[0];
      $link3="sop_link.php?file=".$link."&title=".urlencode($title);
      if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link3.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/((acestream)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $tip="acestream";
      $link=$m[0];

      $link2="intent:".$link."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
      if ($flash == "mp" || $flash=="chrome")
      echo '<TD class="mp">'.'<a href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4|flv|ts)))/', $h1, $m)) {
      $file=$m[1];
      $from="";
      $mod="direct";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=".$mod;
    if ($flash != "mp")
    echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    else
    echo '<TD class="mp">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a></TD>';
   }
}
} else {
    $l="http://mywebtv.info".$link_pg;
    //echo $l;
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
    curl_setopt($ch,CURLOPT_REFERER,$l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close ($ch);
    $t1=explode('id="player">',$h);
    $t2=explode("/div",$t1[1]);
    $h1=$t2[0];
    if (preg_match('/((sop)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $title="sop";
      $link=$m[0];
      $link3="sop_link.php?file=".$link."&title=".urlencode($title);
      if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link3.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/((acestream)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $title="acestream";
      $link=$m[0];
      $link2="intent:".$link."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
      if ($flash == "mp" || $flash=="chrome")
      echo '<TD class="mp">'.'<a href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4|flv|ts)))/', $h1, $m)) {
      $file=$m[1];
      $from="";
      $title=$m[2];
      $mod="direct";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=".$mod;
    if ($flash != "mp")
    echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    else
    echo '<TD class="mp">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a></TD>';
   }
}
echo "</TR>";
echo "</table>";
//play(127,0, 'telekom-sport-online')
?>
<br></body>
</html>
