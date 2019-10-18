<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
$ep_title=prep_tit($ep_title);
$year=$_GET["year"];
/* ====================== */
$fs_target = "moviesjoy_fs.php";
$width="200px";
$height="100px";
$has_img="no";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<meta charset="utf-8">
<title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2><BR>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg
  preg_match("/-season-(\d+)/",$link,$s);
  $sez=$s[1];
  $ch = curl_init($link);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $link);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode("movie = {",$h);
  $t2=explode('id: "',$t1[1]);
  $t3=explode('"',$t2[1]);
  $id=$t3[0];

  $t2=explode('movie_id: "',$t1[1]);
  $t3=explode('"',$t2[1]);
  $movie_id=$t3[0];

  $l="https://www.moviesjoy.net/ajax/v4_movie_episodes/".$id."/".$movie_id;
  //https://www.moviesjoy.net/ajax/v4_movie_episodes/28491/XpX0
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close($ch);

  $r=json_decode($h,1);
  //print_r ($r);
  //die();
$s=array();
$videos=explode("<li",$r["html"]);
unset($videos[0]);
foreach($videos as $video) {
  $t1=explode('title="',$video);
  $t2=explode('"',$t1[1]);
  $s[]=$t2[0];
}
//print_r ($s);
$s=array_unique($s);
asort($s);
//print_r ($s);
foreach($s as $key=>$value) {
  preg_match("/Episode\s+(\d+)/",$value,$e);
  $epNr=$e[1];
  $ep_tit_d = $value;
  $ep_tit = $value;
  //$link="moviesjoy_fs.php?tip=series&link=".urlencode($l)."&title=".urlencode(fix_t($tit))."&ep_tit=".urlencode(fix_t($ep_tit1))."&ep=".$epNr."&sez=".$sez."&image=".$image;
  $link_f=$fs_target.'?tip=series&link='.urlencode($l).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=".$epNr."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year;
   if ($n == 0) echo "<TR>"."\n\r";
   if ($has_img == "yes")
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank"><img width="'.$width.'" height="'.$height.'" src="'.$img_ep.'"><BR>'.$ep_tit_d.'</a></TD>'."\r\n";
   else
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank">'.$ep_tit_d.'</a></TD>'."\r\n";
   $n++;
   if ($n == 3) {
    echo '</TR>'."\n\r";
    $n=0;
   }
}
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }

//}
echo '</table>';
?>
</body>
</html>
