<!DOCTYPE html>
<?php
error_reporting(0);
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
//https://www4.the123movieshub.net/tv-series.html
//https://www.seriestop.net/browse-shows.php
include ("../common.php");
if (file_exists($base_cookie."seriale.dat"))
  $val_search=file_get_contents($base_cookie."seriale.dat");
else
  $val_search="";
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];

if ($tip=="search") {
$page_title = "Cautare: ".$tit;
file_put_contents($base_cookie."seriale.dat",urldecode($tit));
} else
$page_title=$tit;
$base=basename($_SERVER['SCRIPT_FILENAME']);
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);

if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next=$base."?page=".($page+1)."&".$p;
$prev=$base."?page=".($page-1)."&".$p;
$form='<form action="'.$base.'" target="_blank">
Cautare serial:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="'.$page.'">
<input type="hidden" id="tip" name="tip" value="search">
<input type="submit" id="send" value="Cauta !"></form>';
$r=array();
if ($tip=="release") {
  if ($page > 1)
     $l="https://seriesfreetv.com/tvshows/latestepisode/".($page*12);
  else
     $l="https://seriesfreetv.com/tvshows/latestepisode";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

} else {
  $search=str_replace(" ","+",$tit);
  $l="https://seriesfreetv.com/home/search";
  $post="searchText=".$search;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://seriesfreetv.com");
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

}
  //echo $html;
  if ($tip=="release") {
  $videos = explode('class="video_penal">', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $t3=explode('- Season',$t2[0]);
    $title=decode_entities(trim($t3[0]));
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    if ($link) array_push($r ,array($title,$link, $image));
  }
  } else {
  //echo $html;
  $videos = explode('class="video_penal">', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $t3=explode('- Season',$t2[0]);
    $title=trim($t3[0]);
    $title=htmlspecialchars_decode($title,ENT_QUOTES);
    $title=html_entity_decode($title,ENT_QUOTES);
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    if ($link) array_push($r ,array($title,$link, $image));
  }
  }
  //print_r ($r);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
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
  var php_file="seriesfreetv_s_add.php";
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
if (file_exists($base_pass."lista.txt")) {
$tabel=trim(file_get_contents($base_pass."lista.txt"));
} else {
$tabel="TABEL";
}
$w=0;
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';



if ($page==1 && $tip=="release") {
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD class="cat" width="25%"><a id="fav" a href="seriesfreetv_s_fav.php">Favorite</a></TD>
<TD class="form" colspan="2">'.$form.'</TD>';
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';
} else {
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';
}

echo '<table border="1px" width="100%">'."\n\r";
$c=count($r);
//print_r ($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $link_film=$r[$k][1];
  $image=$r[$k][2];
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".$link_film."&image=".urlencode($image);
  if ($n==0) echo '<TR>';
  $link="seriesfreetv_s_ep.php?tip=tv&title=".urlencode(fix_t($title))."&link=".urlencode($link_film)."&image=".$image."&sez=&ep=%ep_tit=";
   if ($tast == "NU")
    echo '<td class="mp" align="center" width="25%"><a class="imdb" href="'.$link.'" target="blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }
   $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '</TABLE>';

if ($page==1 && $tip=="release") {
echo '<table border="1px" width="100%"><TR>'."\n\r";
//echo '<TD width="25%"><a id="fav" a href="swatchseries_s_fav.php">Favorite</a></TD><TD style="height:10px;text-align:left" colspan="2">'.$form.'</TD>';
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';
} else {
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';
}
?>
</div></body>
</html>
