<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$main_title="FOTBAL LA TV";
$target="";
$fav_target="";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $main_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
var id_link="";
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
     id = "imdb_" + self.id;
     id_link=self.id;
    return true;
}
function prog(link) {
    msg="prog.php?" + link;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
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
<?php
include ("../common.php");

echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
echo '<TR><th class="cat" colspan="4">'.$main_title.'</th></TR>';
$n=0;
$l="https://www.telefootball.net/RO/tvs";
$ua = 'Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  $html=str_replace(urldecode("%CE"),"O",$html);
$videos = explode('<span class="mobfoot2n2">', $html);
unset($videos[0]);
$videos = array_values($videos);
$w=0;
foreach($videos as $video) {
$n=0;
    $t1=explode('<span style="',$video);
    $day=trim(strip_tags($t1[0]));
    $day=trim(str_replace("/","",$day));
    echo '<TR><TD class="mp" colspan="4" bgcolor="#0a6996">'.$day.'</td></TR>';
    //echo $link;
    $vids=explode('<div class="lqvonews12',$video);
    unset ($vids[0]);
    $vids=array_values($vids);
    foreach ($vids as $vid) {

    $t2 = explode('div align="right">', $vid);
    $t3 = explode('<', $t2[1]);
    $title1 = $t3[0];
    $t1=explode('class="appt32ins">',$vid);
    $t2=explode('<',$t1[2]);
    $title2=$t2[0];
    if (preg_match("/class\=\"appt32\"\>/",$vid)) {
    $t1=explode('class="appt32">',$vid);
    $t2=explode('</span',$t1[1]);
    $ora=trim(strip_tags($t2[0]));
    } elseif (preg_match("/class\=\"appt33\"\>/",$vid)) {
    $t1=explode('class="appt33">',$vid);
    $t2=explode('<',$t1[1]);
    $ora=trim(strip_tags($t2[0]));
    } else {
     $ora="0:0";
    }
    if (preg_match("/appt34live\"\>/",$vid)) {
      $t1=explode('appt34live">',$vid);
      $t2=explode('<',$t1[1]);
      $min=$t2[0];
      $ora='<font color="red">'.$ora." (".$min.")</font>";
    }
    $t1=explode('title="',$vid);
    $t2=explode('"',$t1[1]);
    $post=trim($t2[0]);
    $title=$title1."-".$title2."<BR>".$ora." - ".$post;
	if ($n == 0) echo "<TR>"."\r\n";
     echo '<td class="mp" align="center" width="25%">'.$title.'</TD>';
    $n++;
    $w++;
    if ($n == 4) {
     echo '</TR>'."\r\n";
     $n=0;
    }

  if ($n < 5 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      //echo '<TD></TD>'."\r\n";
    }
    //echo '</TR>'."\r\n";
  }
}
}
 echo '</table>';
?>
</BODY>
</HTML>
