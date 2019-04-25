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
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=movie&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "99" || charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
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
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
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
//org.acestream.media.atv
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
//$html = file_get_contents("http://divxonline.biz/");

//echo $h;
//die();
//https://123netflix.pro/movies/
$n=0;
echo '<H2>'.$page_title.'</H2>';

echo '<table border="1px" width="100%">'."\n\r";
if ($tip=="release") {
echo "<TR>";
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
echo '<a class="nav" href="popcorn_f.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="popcorn_f.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}else {
echo '<TD class="form" colspan="3">';
//title=star&page=1&file=search
echo '<form action="popcorn_f.php" target="_blank">Cautare film:';
echo '<input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="1">
<input type="hidden" id="file" name="file" value="search">
<input type="submit" id="send" value="Cauta"></form></TD>';
echo '<TD colspan="1" align="right"><a class="nav" href="popcorn_f.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
}

  
if ($tip=="search")
   $requestLink="https://tv-v2.api-fetch.website/movies/".$page."?sort=last%20added&order=-1&keywords=".str_replace(" ","+",$title);
else
   $requestLink = "https://tv-v2.api-fetch.website/movies/".$page."";  //?sort=last%20added&order=-1
/*
https://api.flixanity.watch/api/v1/0A6ru35yevokjaqbb8
q=suits&limit=100&timestamp=1488790946255&verifiedCheck=eCNBuxFGpRmFlWjUJjmjguCJI&set=IzQRciTAgnxYXLCGTapGBQPVy&rt=rPAOhkSTcEzSyJwHWwzwthPWVVmDEpvGNtakLKYPTGncTODCIl&sl=52b1b99472b9ce7f990647349ed08f75
//https://tv-v2.api-fetch.website/movies/1?sort=last%20added&order=1&keywords=Star%20Wars
*/
//echo $requestLink;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://tv-v2.api-fetch.website");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($html,1);
  //print_r ($r);
//echo $l;

for ($w=0;$w <count($r); $w++) {
  $imdb=$r[$w]["imdb_id"];
  $title11 = $r[$w]["title"];
  $title11=str_replace("&#8217;","'",$title11);
  $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
  $year=$r[$w]["year"];
  $image =$r[$w]["images"]["poster"];
  $image=str_replace("w500","w300",$image);
  if (!$image) $image="blank.jpg";
  $season="";
  $episod="";
  if ($n==0) echo '<TR>';
  if ($tast == "NU")
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="popcorn_fs.php?tip=movie&imdb='.urlencode($imdb).'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.' - ('.$year.')</a></TD>';
  else {
  //$year="";
  $val_imdb="title=".$title11."&year=".$year."&imdb=".$imdb;
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="popcorn_fs.php?tip=movie&imdb='.urlencode($imdb).'&title='.urlencode(fix_t($title11)).'&image='.$image.'" target="_blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title11.' - ('.$year.')</a></TD>';
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}

//if ($tip=="release") {
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="popcorn_f.php?page='.($page-1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="popcorn_f.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="popcorn_f.php?page='.($page+1).'&file='.$file.'&title='.urlencode($title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
//}
echo "</table>";
?>

<br></div></body>
</html>
