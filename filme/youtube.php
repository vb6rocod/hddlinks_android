<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title>Youtube</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>



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
function ajaxrequest(tip, user,id) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:del,title:title, link:link}; //Array
  var the_data = 'tip=' + tip + '&user='+ user + '&id='+id;
  var php_file='youtube_add.php';
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
      location.reload();
    }
  }
}
</script>
</head>
<body>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$filename=$base_fav."youtube.txt";
$tip=$_GET["tip"];
$user=$_GET["user"];
$id=$_GET["id"];
if ($tip) {
if (file_exists($filename)) {
 $h=file_get_contents($filename);
 if (strpos($h,$id) ===false) $h=$h.$user."\n".$id."\n".$tip."\n";
} else {
   $h=$user."\n".$id."\n".$tip."\n";
}
$fh = fopen($filename, 'w');
fwrite($fh, $h);
fclose($fh);
}
echo '<h2 style="background-color:deepskyblue;color:black">Youtube</H2>';
echo '<table align="center" border="1" width="100%">'."\n\r";
echo '<tr><td colspan="2" align="center"><font size="4">Adauga un user/canal</font></td>
<td colspan="2" align="center"><font size="4">Adauga un playlist</font></td><td colspan="2" align="center"><font size="4">Adauga o cautare</font></td></tr>';
echo '<tr>';
echo '<td colspan="2"><form action="youtube.php">
<font size="4">Titlu: </font><input type="text" name="user" id="user"><BR>
<font size="4">User/Canal ID: </font><input type="text" name="id" id="id" size="20">
<input type="hidden" name="tip" value="user">
<input type="submit" value="Adauga"></form></td>';
echo '<td colspan="2"><form action="youtube.php">
<font size="4">Titlu: </font><input type="text" name="user" id="user"><BR>
<font size="4">Playlist ID: </font><input type="text" name="id" id="id" size="30">
<input type="hidden" name="tip" value="playlist">
<input type="submit" value="Adauga"></form></td>';
echo '<td colspan="2"><form action="youtube.php">
<font size="4">Titlu: </font><input type="text" name="user" id="user"><BR>
<font size="4">Cauta: </font><input type="text" name="id" id="id" size="20">
<input type="hidden" name="tip" value="search">
<input type="submit" value="Adauga"></form></td>';
echo '</tr>';
echo '<tr>
<td colspan="6"><form action="youtube_search.php" target="_blank">
<font size="4">Cauta un videoclip: </font><input type="text" size="40" name="search" id="search">
<input type="hidden" id="page" name="page" value="1">
<input type="submit" value="cauta"></form></td></tr>';
if (file_exists($filename)) {

echo '<TR><td style="background-color:deepskyblue;color:black;text-align:center;" colspan="6"><b><font size="4">Youtube - canale/playlist-uri</font></b></TD></TR>';
$n=0;

  $h=file($filename);
  //print_r ($h);
  $m=count($h);
  for ($k=0;$k<$m;$k=$k+3) {

   $user=trim($h[$k]);
   $id=trim($h[$k+1]);
   $tip=trim($h[$k+2]);
   $arr[]=array($user, $id,$tip);
  }
if( !empty( $arr ) ) {
asort($arr);
foreach ($arr as $key => $val) {
  $id=$arr[$key][1];
  $user=$arr[$key][0];
  $tip=$arr[$key][2];
  if ($n == 0) echo "<TR>"."\n\r";
  if ($tip=="user") {
	$link = "youtube_user.php?page=1,".$id.",".$user;
  } else {
	if ($tip=="search") {
		$link = "youtube_search.php?search=".$id."&page=1";
	} else {
	$link = "yt_playlist.php?page=1,".$id.",".$user;
	}
  }
   echo '<TD><font size="4">'.'<a href="'.$link.'" target="_blank">'.$user.'</a></font></TD>';
   echo '<TD align="right" width="5px"><a onclick="ajaxrequest('."'".$tip."', '".$user."','".$id."');".'"'." style='cursor:pointer;'>".'<font size="4">DEL</font></a></TD>'."\n\r";
   $n++;
   if ($n > 2) {
   echo '</TR>'."\n\r";
   $n=0;
   }
}
}
echo '</table>';
}
?>
<br></body>
</html>
