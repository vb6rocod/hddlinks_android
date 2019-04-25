<!DOCTYPE html>
<?php
include ("../common.php");
$cookie=$base_cookie."biz.dat";
$page = $_GET["page"];
$search=$_GET["link"];
$page_title=urldecode($_GET["title"]);
if (!$page) {
$page_title = "Cautare: ".$search;
file_put_contents($base_cookie."filme.dat",urldecode($search));
}
if($page) {
	$l=$search."/page/".$page;
} else {
$search=str_replace(" ","+",$search);
$l= "https://www.filmeonline.biz?s=".$search;
//echo $html;
}
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
if ($page) {
echo '<tr><TD class="nav" colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="filmeonline_biz.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="filmeonline_biz.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="filmeonline_biz.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
$ua=$_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);

 $videos = explode('itemprop="name">', $html);
//echo $html;
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $year="";
  $tip="movie";
  $link = trim(str_between($video,'href="','"'));
  $title=str_between($video,'title="','"');
  $title=str_replace("&#8211;","-",$title);
  $title=str_replace("&#8217;","'",$title);
  $title=htmlspecialchars_decode($title,ENT_QUOTES);
  $title=html_entity_decode($title,ENT_QUOTES);
  $title=trim(preg_replace("/Online Subtitrat in Romana|Filme Online Subtitrat HD 720p|Online HD 720p Subtitrat in Romana|Online Subtitrat Gratis|Online Subtitrat in HD Gratis|Film HD Online Subtitrat/i","",$title));
  $title=trim(preg_replace("/(subtitrat|onlin|film)(.*)/i","",$title));
  //$title=diacritice($title);
  if (preg_match("/\(?((1|2)\d{3})\)?/",$title,$r)) {
     //print_r ($r);
     $year=$r[1];
  }
  $t1=explode(" - ",$title);
  $t=$t1[0];
  $t=preg_replace("/\(?((1|2)\d{3})\)?/","",$t);
  $tit3=trim($t);
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  $image=htmlentities($image,ENT_QUOTES,'UTF-8');
  //$image=str_replace("https","http",$image);
  //$descriere=diacritice($descriere);
  if ($n==0) echo '<TR>';
  if ($tast == "NU")
    echo '<td class="mp" align="center" width="25%"><a class="imdb" href="filme_link.php?file='.urlencode($link).','.urlencode(fix_t($title)).'" target="blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  else {
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="filme_link.php?file='.urlencode($link).','.urlencode(fix_t($title)).'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  $w++;
  }
   $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
if ($page) {
echo '<tr><TD class="nav" colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="filmeonline_biz.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="filmeonline_biz.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="filmeonline_biz.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo "</table>";
?>
<br></div></body>
</html>
