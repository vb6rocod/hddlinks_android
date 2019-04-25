<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];
$link=$_GET["link"];
$width="200px";
$height="278px";
if ($tip=="search") {
$page_title = "Cautare: ".$tit;
file_put_contents($base_cookie."filme.dat",urldecode($tit));
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
Cautare :  <input type="text" id="title" name="title" value="">
<input type="hidden" id="page" name="page" value="'.$page.'">
<input type="hidden" id="tip" name="tip" value="search">
<input type="hidden" id="link" name="link" value="search">
<input type="submit" value="Cauta !"></form>';
$r=array();

$ua = $_SERVER['HTTP_USER_AGENT'];

if ($tip=="release") {
   if($page>1) {
    $l=$link."page/".$page."/";
   } else {
    $l=$link;
   }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
} else {
  $search=str_replace(" ","+",$tit);
  $l="https://f-hd.biz/?s=".$search;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
}
  if ($tip=="release") {
 $videos = explode('li class="film"', $html);
//echo $html;
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $mod="da";
  $link = trim(str_between($video,'itemprop="url">','<'));
  $title=str_between($video,'title="','"');
  $title=str_replace("&#8211;","-",$title);
  $title=str_replace("&#8217;","'",$title);
  $title=trim(preg_replace("/Online Subtitrat in Romana|Filme Online Subtitrat HD 720p|Online HD 720p Subtitrat in Romana|Online Subtitrat Gratis|Online Subtitrat in HD Gratis|Film HD Online Subtitrat/i","",$title));
  $title=trim(preg_replace("/(onlin|film)(.*)/i","",$title));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  $image="r_m.php?file=".$image;
    if ($mod=="da") array_push($r ,array($title,$link, $image));
  }
  } else {
 $videos = explode('li class="film"', $html);
//echo $html;
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $mod="da";
  $link = trim(str_between($video,'itemprop="url">','<'));
  $title=str_between($video,'title="','"');
  $title=str_replace("&#8211;","-",$title);
  $title=str_replace("&#8217;","'",$title);
  $title=trim(preg_replace("/Online Subtitrat in Romana|Filme Online Subtitrat HD 720p|Online HD 720p Subtitrat in Romana|Online Subtitrat Gratis|Online Subtitrat in HD Gratis|Film HD Online Subtitrat/i","",$title));
  $title=trim(preg_replace("/(onlin|film)(.*)/i","",$title));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  $image="r_m.php?file=".$image;
    if ($mod=="da") array_push($r ,array($title,$link, $image));
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
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
    }
   }
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>

</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<div id="mainnav">
<?php

if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';



if ($tip=="release" || $tip=="search") {
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
  $title=htmlspecialchars_decode($title,ENT_QUOTES);
  $title=html_entity_decode($title,ENT_QUOTES);
  $link_film=$r[$k][1];
  $image=$r[$k][2];
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  $link_f="filme_link.php?file=".urlencode($link_film).','.urlencode(fix_t($title));
  if ($n==0) echo '<TR>';
  if ($tast == "NU")
    echo '<td class="mp" align="center" width="25%"><a class="imdb" href="'.$link_f.'" target="blank"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  else {
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link_f.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
  $w++;
  }
  $n++;
  if ($n == 4) {
  echo '</TR>';
  $n=0;
  }
}
echo '</TABLE>';
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';

?>
</div></body>
</html>
