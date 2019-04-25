<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
if (file_exists($base_cookie."seriale.dat"))
  $val_search=file_get_contents($base_cookie."seriale.dat");
else
  $val_search="";
$title = $_GET["title"];
$page = $_GET["page"];
$file=$_GET["file"];
$tip=$file;
if ($tip=="search") {
  $page_title="Cautare: ".urldecode($title);
  file_put_contents($base_cookie."seriale.dat",urldecode($title));
} else
  $page_title=urldecode($title);
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

function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  //link=document.getElementById('server').innerHTML;
  var the_data = link;
  //alert(the_data);
  var php_file="tvhub_s_add.php";
  request.open("POST", php_file, true);			// set the request

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
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
    }
   }
var id_link="";
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=series&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>

</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<div id="mainnav">
<H2></H2>
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
$w=0;
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';

echo '<table border="1px" width="100%">'."\n\r";
if ($tip=="release") {
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
echo '<a class="nav" href="tvhub_s.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="tvhub_s.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}else {
echo '<TR><TD class="cat" ><a id="fav" href="tvhub_s_fav.php" target="blank">Favorite</a></TD>
<TD class="form" colspan="2">';
//title=star&page=1&file=search
echo '<form action="tvhub_s.php" target="_blank">Cautare serial:';
echo '<input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="1">
<input type="hidden" id="file" name="file" value="search">
<input type="submit" id="send" value="Cauta"></form></TD>';
echo '<TD colspan="1" align="right"><a class="nav" href="tvhub_s.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
}
//http://tvhub.org/?s=good

$ua = $_SERVER['HTTP_USER_AGENT'];
//$ua="Linux";
$cookie=$base_cookie."hdpopcorns.dat";
$requestLink="https://tvhub.org/";
//$requestLink="http://hdpopcorns.co/category/latest-movies/";
if ($page==1 && $tip !="search") {
if (file_exists($cookie)) unlink ($cookie);
$head=array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate, br',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  //curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
 if (strpos($h,"503 Service") !== false) {
  if (strpos($h,'id="cf-dn') === false)
   $q= getClearanceLink_old($h,$requestLink);
  else
   $q= getClearanceLink($h,$requestLink);

  curl_setopt($ch, CURLOPT_URL, $q);
  $h = curl_exec($ch);
  curl_close($ch);
 } else {
    curl_close($ch);
 }
}
if ($tip=="search") {
  $requestLink = "https://tvhub.org/?s=".str_replace(" ","+",$title);
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://tvhub.org/");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
} else {
  $requestLink="https://tvhub.org/wp-admin/admin-ajax.php";
  $post="action=load_more&page=".($page-1)."&template=cactus-channel%2Fcontent-listing&vars%5Bpost_type%5D=ct_channel&vars%5Bposts_per_page%5D=20&vars%5Bpost_status%5D=publish&vars%5Bignore_sticky_posts%5D=1&vars%5Bpaged%5D=1&id_playlist=";

  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://tvhub.org/");
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
}
//echo $html;
//echo $l;
if ($tip=="release") {
 $videos = explode('div class="entry-content"', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1 = explode('href="',$video);
  $t2 = explode('"', $t1[1]);
  $link1 = $t2[0];

  $t1 = explode('title="', $video);
  $t2 = explode('"', $t1[1]);
  $title11 = $t2[0];
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $id1=$link1;
  //$title=trim(preg_replace("/- filme online subtitrate/i","",$title));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  $image = "r_m.php?file=".$image;
  $image1=$image;
  //$year=trim(str_between($video,'movie-date">','<'));
  //$title=$title11; //." (".$year.")";
  //$id_t=$id1;
  $season="";
  $episod="";
  if ($n==0) echo '<TR>';
  $fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".urlencode($image);
  if ($tast == "NU")
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="tvhub_s_ep.php?tip=serie&file='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><img src="'.$image.'" width="200px" height="107px"><BR>'.$title11.'</a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="tvhub_s_ep.php?tip=serie&file='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><img src="'.$image.'" width="200px" height="107px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
} else {
 $videos = explode('<div id="mt-', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1 = explode('href="',$video);
  $t2 = explode('"', $t1[1]);
  $link1 = $t2[0];

  $t1 = explode('alt="', $video);
  $t2 = explode('"', $t1[1]);
  $title11 = $t2[0];
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $id1=$link1;
  //$title=trim(preg_replace("/- filme online subtitrate/i","",$title));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  $image = "r_m.php?file=".$image;
  $image1=$image;
  //$year=trim(str_between($video,'movie-date">','<'));
  //$title=$title11; //." (".$year.")";
  //$id_t=$id1;
  $season="";
  $episod="";
  if (strpos($link1,"serial/") !== false) {
  if ($n==0) echo '<TR>';

  $fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".urlencode($image);
  if ($tast == "NU")
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="tvhub_s_ep.php?tip=serie&file='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><img src="'.$image.'" width="200px" height="107px"><BR>'.$title11.'</a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="tvhub_s_ep.php?tip=serie&file='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><img src="'.$image.'" width="200px" height="107px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}

}
if ($tip=="release") {
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="tvhub_s.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="tvhub_s.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="tvhub_s.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo "</table>";
?>
</div>
<br></body>
</html>
