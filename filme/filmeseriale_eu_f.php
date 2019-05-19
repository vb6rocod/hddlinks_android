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
include ("../common.php");

$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];

if ($tip=="search") {
$page_title = "Cautare: ".$tit;
$val_search= urldecode($tit);
file_put_contents($base_cookie."filme.dat",urldecode($tit));
} else {
$page_title=$tit;
if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
}
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
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="'.$page.'">
<input type="hidden" id="tip" name="tip" value="search">
<input type="submit" id="send" value="Cauta !"></form>';
$r=array();
if ($tip=="search") {
  $requestLink = "https://www.filmeseriale.eu/page/".$page."/?s=".str_replace(" ","+",$tit);
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://www.filmeseriale.eu");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $l;
 $videos = explode('<article', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1 = explode('href="',$video);
  $t2 = explode('"', $t1[1]);
  $link1 = $t2[0];
  if (strpos($link1,"http") === false) $link1="https://".$host.$link1;
  $t3 = explode('>', $t1[2]);
  $t4 = explode('<', $t3[1]);
  $t5=explode(', film',$t4[0]);
  $title11 = trim($t5[0]);
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  //$title11=decode_entities($title11);
  $title11=str_replace("#038;","",$title11);
  $title11=str_replace("&#8217;","'",$title11);
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
    if (strpos($link1,"filme-online") !== false && $title11 && $title11 <> "DMCA") array_push($r ,array($title11,$link1, $image));
  }
} else {
$requestLink="https://www.filmeseriale.eu/filme-online/page/".$page."/";
//$requestLink="https://www.moviesjoy.net/movie/filter/movie/all/all/all/all/all/all/page-".$page.".html";
//https://www.moviesjoy.net/movie/filter/series/latest
//echo $requestLink;
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://www.filmeseriale.eu");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $l;
 $videos = explode('article id="post-', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1 = explode('href="',$video);
  $t2 = explode('"', $t1[1]);
  $link1 = $t2[0];
  if (strpos($link1,"http") === false) $link1="https://".$host.$link1;
  $t3 = explode('>', $t1[2]);
  $t4 = explode('<', $t3[1]);
  $t5=explode(', film',$t4[0]);
  $title11 = trim($t5[0]);
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  //$title11=decode_entities($title11);
  $title11=str_replace("#038;","",$title11);
  $title11=str_replace("&#8217;","'",$title11);
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
    if (strpos($link1,"filme-online") !== false && $title11 <> "DMCA") array_push($r ,array($title11,$link1, $image));
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
     msg="imdb.php?tip=movie&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
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



if ($tip=="release" || $tip=="search") {
echo '<table border="1px" width="100%"><TR>'."\n\r";
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
} else {
echo '<TD class="form" colspan="3">'.$form.'</TD>';
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo '</TABLE>';
}

echo '<table border="1px" width="100%">'."\n\r";
$c=count($r);
//print_r ($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $link_film=$r[$k][1];
  $image=$r[$k][2];
  if ($n==0) echo '<TR>';
  //$link="filmeseriale_eu_s_ep.php?tip=tv&title=".urlencode(fix_t($title))."&link=".urlencode($link_film)."&image=".$image."&sez=&ep=%ep_tit=";
  $link="filme_link.php?file=".urlencode($link_film).",".urlencode(fix_t($title));
   if ($tast == "NU")
    echo '<td class="mp" align="center" width="25%"><a class="imdb" href="'.$link.'" target="blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  else {
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  $w++;
  }
   $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<TR><TD style="height:10px;text-align:right" colspan="4">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';

echo '</TABLE>';
?>
</div></body>
</html>
