<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
$title = $_GET["title"];
$page = $_GET["page"];
$file=$_GET["file"];
$tip=$file;
if ($tip=="search") {
  $page_title="Cautare: ".urldecode($title);
  file_put_contents($base_cookie."filme.dat",urldecode($title));
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
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
    }
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
echo "<TR>";
if ($page > 1) {
echo '<tr><TD class="nav" colspan="4" align="right">';
echo '<a class="nav" href="divxfilmeonline.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="divxfilmeonline.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}else {
echo '<TD class="form" colspan="2">';
//title=star&page=1&file=search
echo '<form action="divxfilmeonline.php" target="_blank">Cautare film:';
echo '<input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="1">
<input type="hidden" id="file" name="file" value="search">
<input type="submit" id="send" value="Cauta"></form></TD>';
echo '<TD class="nav" colspan="2" align="right"><a href="divxfilmeonline.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
}

if ($tip=="search") {
  $requestLink = "https://divxfilmeonline.org/?s=".str_replace(" ","+",$title);
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://tvhub.org");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
} else {
  $requestLink="https://divxfilmeonline.org/filme/page/".$page;
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://divxfilmeonline.org");
  //curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
}
//echo $html;
//echo $l;
if ($tip=="release") {
 $videos = explode('<li class="border', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $link1 = trim(str_between($video,'href="','"'));
  $title11=str_between($video,'title="','"');
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $id1=$link1;
  //$title=trim(preg_replace("/- filme online subtitrate/i","",$title));
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = trim($t2[0]);
  //$image=str_replace("https","http",$image);
  $image = "r_m.php?file=".$image;
  $image1=$image;
  //$year=trim(str_between($video,'movie-date">','<'));
  //$title=$title11; //." (".$year.")";
  //$id_t=$id1;
  $season="";
  $episod="";
  $year="";
  //300px × 168px
  if ($n==0) echo '<TR>';
  if ($tast == "NU")
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="filme_link.php?file='.$link1.','.urlencode(fix_t($title11)).'" target="_blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title11.'</a></TD>';
  else {
  $val_imdb="title=".$title11."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="filme_link.php?file='.urlencode($link1).','.urlencode(fix_t($title11)).'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title11.'</a></TD>';
  $w++;
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
} else {
 $videos = explode('<li class="border', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $link1 = trim(str_between($video,'href="','"'));
  $title11=str_between($video,'title="','"');
  $title11=trim(preg_replace("/Watch|Putlocker/i","",$title11));
  $id1=$link1;
  //$title=trim(preg_replace("/- filme online subtitrate/i","",$title));
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = trim($t2[0]);
  $image = "r_m.php?file=".$image;
  $image1=$image;
  //$year=trim(str_between($video,'movie-date">','<'));
  //$title=$title11; //." (".$year.")";
  //$id_t=$id1;
  $season="";
  $episod="";
  $year="";
  if (!preg_match("/sezonul-/",$link1)) {
  if ($n==0) echo '<TR>';
  if ($tast == "NU")
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="filme_link.php?file='.$link1.','.urlencode(fix_t($title11)).'" target="_blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title11.'</a></TD>';
  else {
  $val_imdb="title=".$title11."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="filme_link.php?file='.urlencode($link1).','.urlencode(fix_t($title11)).'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title11.'</a></TD>';
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
echo '<a class="nav" href="divxfilmeonline.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="divxfilmeonline.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="divxfilmeonline.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo "</table>";
?>
<br></div></body>
</html>
