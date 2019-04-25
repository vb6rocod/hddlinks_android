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
$width="250px";
$height="140px";
if ($tip=="search")
$page_title = "Cautare: ".$tit;
else
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
  $l="https://www.rovideo.net/latest-updates/?mode=async&function=get_block&block_id=list_videos_latest_videos_list&sort_by=post_date&from=".$page."&_=1550932971794";
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
  $search=str_replace(" ","_",$tit);
  //https://filmeonline2019.us/cauta/star-trek
  $l="https://www.rovideo.net/search/filme_romanesti/?mode=async&function=get_block&block_id=list_videos_videos_list_search_result&q=".$search."&category_ids=&sort_by=&from_videos=".$page.".&from_albums=".$page."&_=1550933092095";
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
//echo $html;
  if ($tip=="release") {
  $videos = explode('div class="item', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $mod="da";
    $t1=explode('video-id="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $title=$t2[0];
    $t1=explode('data-original="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    $t1=explode('class="duration">',$video);
    $t2=explode('<',$t1[1]);
    $durata=$t2[0];
    if ($mod=="da") array_push($r ,array($title,$link, $image,$durata));
  }
  } else {
  $videos = explode('div class="item', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $mod="da";
    $t1=explode('video-id="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $title=$t2[0];
    $t1=explode('data-original="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    $t1=explode('class="duration">',$video);
    $t2=explode('<',$t1[1]);
    $durata=$t2[0];
    if ($mod=="da") array_push($r ,array($title,$link, $image,$durata));
  }
  }
  //print_r ($r);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
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
  var the_data = link;
  //alert(the_data);
  var php_file="rovideo_add.php";
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
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
</script>
<link rel="stylesheet" type="text/css" href="../custom.css" />

</head>
<body><div id="mainnav">
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

if ($page > 1) {
echo '<TD style="height:10px;text-align:right" colspan="4">';
echo '<a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
} else {
echo '<TR><TD class="cat">'.'<a href="rovideo_fav.php" target="_blank"><b>Favorite</b></a></TD>
<TD class="form" colspan="2"><form action="rovideo.php" target="_blank">
Cautare:  <input type="text" id="title" name="title">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="search">
<input type="submit" value="Cauta..."></form>
</TD>
<TD style="height:10px;text-align:right">
<a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>
</td>
</TR>';

}
echo '</TABLE>';
}
echo '<table border="1px" width="100%">'."\n\r";
$c=count($r);
//print_r ($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $title=htmlspecialchars_decode($title,ENT_QUOTES);
  $title=html_entity_decode($title,ENT_QUOTES);
  $link_film="https://www.rovideo.net/embed/".$r[$k][1];
  $image=$r[$k][2];
  $durata=$r[$k][3];
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".$link_film."&image=".urlencode($image);
  $link_f="filme_link.php?file=".urlencode($link_film).','.urlencode(fix_t($title));
  if ($n==0) echo '<TR>';
   if ($tast == "NU") {
  echo '<td class="mp" align="center" width="25%"><a href="'.$link_f.'" target="_blank"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.' ('.$durata.')</a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  } else {
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.' ('.$durata.')</a>';
  echo '<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
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
echo '<a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';

?>
</div></body>
</html>
