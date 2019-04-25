<!DOCTYPE html>
<?php
include ("../common.php");
set_time_limit(30);
if (file_exists($base_cookie."seriale.dat"))
  $val_search=file_get_contents($base_cookie."seriale.dat");
else
  $val_search="";
$x="http://www.filmeserialeonline.org/seriale";
$title = $_GET["title"];
$page = $_GET["page"];
$file=$_GET["file"];
$tip=$file;
if ($tip=="search") {
  $page_title="Cautare: ".urldecode($title);
  file_put_contents($base_cookie."seriale.dat",urldecode($title));
} else
  $page_title=urldecode($title);
//https://filmeseriale.online/seriale/
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
  var php_file='filmeseriale_add.php';
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
function mouseOver(n) {
    zz="myLink" + n;
    //alert (zz);
    document.getElementById(zz).focus();
}
document.onkeypress =  zx;
</script>
</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<H2></H2>
<div id="mainnav">
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
if (file_exists($base_pass."filmeseriale.txt"))
echo '<H2>'.$page_title.'</H2>';
else
echo '<H2>'.$page_title.' <a href="captcha.php">(seteaza GoogleCaptcha)</a></H2>';

echo '<table border="1px" width="100%">'."\n\r";
if ($tip=="release") {
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
//echo '<a href="filmeseriale_main.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="tvhub_s.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}else {
echo '<TR><TD class="cat"><a id="fav" href="filmeseriale_fav.php" target="blank">Favorite</a></TD>
<TD class="form" colspan="3">';
//title=star&page=1&file=search
echo '<form action="filmeseriale_main.php" target="_blank">Cautare serial:';
echo '<input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="1">
<input type="hidden" id="file" name="file" value="search">
<input type="submit" id="send" value="Cauta"></form></TD>';
echo '</TR>';
}
}
///////////////////////////////////////////////////////////
//$l =$search."/page/".$page."/";
if ($tip=="release") {
$l="http://www.filmeserialeonline.org/seriale/page/1/";
//https://filmeseriale.online/seriale/page/2/
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://filmeseriale.online");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $t1=explode("previouspostslink",$html);
  $t2=explode('page/',$t1[1]);
  $t3=explode('/',$t2[1]);
  $last_page=$t3[0]+1;
  //$last_page=2;
$arr=array();
for ($k=1;$k<$last_page;$k++) {
  $p=0;
  $l="http://www.filmeserialeonline.org/seriale/page/".$k."/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://filmeseriale.online");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  
$videos = explode('div id="mt-', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1 = explode('href="', $video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];
	$link1 = $link;


    $t3 = explode('class="tt">',$video);
    $t4 = explode('<',$t3[1]);
    $title1 = trim($t4[0]);

    $t1 = explode('src="', $video);
    $t2 = explode('"', $t1[1]);
    $image=$t2[0];
    //$arr[]=array("title" => $title, "link" => $link, "iimage" => $image);
    $arr[$title1]["link"]=$link;
    $arr[$title1]["image"]=$image;
}
}
ksort($arr);
//print_r ($arr);
foreach ($arr as $key => $value) {
  $title11=$key;
  $link1 = $arr[$key]["link"];
    $link="filmeseriale.php?file=".$link1."&title=".urlencode(fix_t($title11));
    $image=$arr[$key]["image"];
  if ($n==0) echo '<TR>';
  $fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
  if ($tast == "NU") {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($w*1).'" onmouseover="mouseOver('.($w*1).')" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"></a>';
  echo ' <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  $w++;
  } else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
} else {
$l="http://www.filmeserialeonline.org/?s=".str_replace(" ","+",$title);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://www.filmeserialeonline.org/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
 $videos = explode('<div id="mt', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $link = trim(str_between($video,'href="','"'));
  $link1=$link;
  $title11=str_between($video,'<h2>','</');
  $title11=trim(preg_replace("/Online Subtitrat in Romana|Filme Online Subtitrat HD 720p|Online HD 720p Subtitrat in Romana|Online Subtitrat Gratis|Online Subtitrat in HD Gratis|Film HD Online Subtitrat/i","",$title11));
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  if($link!="" && strpos($link,"/seriale") !== false) {
  $link="filmeseriale.php?file=".$link1."&title=".urlencode(fix_t($title11));
  $fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
  if ($n==0) echo '<TR>';
  if ($tast == "NU") {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($w*1).'" onmouseover="mouseOver('.($w*1).')" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"></a>';
  echo ' <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  $w++;
  } else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
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
echo "</table>";
?>
</div>
<br></body>
</html>
