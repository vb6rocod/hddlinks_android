<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["link"];
$tip=$_GET["tip"];
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body><div id="mainnav">
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
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h2 = curl_exec($ch);
  curl_close($ch);
  $t1=explode('data-gid="',$h2);
  $t2=explode('"',$t1[1]);
  $gid=$t2[0];
  $t1=explode("mid=",$h2);
  $t2=explode('&',$t1[1]);
  $mid=$t2[0];
  $l="https://www.filme-online.to/ajax/mep.php?id=".$mid;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  $h4=json_decode($h3,1);
  //print_r ($h4);
  $ts=$h4["ts"];
  $t2=0;
//explode('<div id="',$ep);
$videos1 = explode('<div id="', $h4["html"]);
//><a title=
unset($videos1[0]);
$videos1 = array_values($videos1);

foreach($videos1 as $video1) {
  $t1=explode('data-id="',$video1);
  $t2=explode('"',$t1[1]);
  $num_serv=$t2[0];
  //echo "=========================".$num_serv."============================"."\n";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">SERVER: '.$num_serv.'</td></TR>';
  $n=0;
  $videos = explode('<a title="',$video1);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
  $t1=explode('"',$video);
  $ep_tit=$t1[0];
  $t1=explode('data-server="',$video);
  $t2=explode('"',$t1[1]);
  $server=$t2[0];
  $t1=explode('data-id="',$video);
  $t3=explode('"',$t1[1]);
  $eid=$t3[0];
  $t1=explode('data-epNr="',$video);
  $t2=explode('"',$t1[1]);
  $epNr=$t2[0];
  $t1=explode('data-so="',$video);
  $t2=explode('"',$t1[1]);
  $sc=$t2[0];
  $t1=explode('data-index="',$video);
  $t2=explode('"',$t1[1]);
  $epIndex=$t2[0];
  //echo $epNr.$ep_tit."\n";
  //$t1=explode('class="btn-eps ep-item so5">',$video);
  $t1=explode('class="btn-eps',$video);
  $t2=explode(">",$t1[1]);
  $t3=explode('<',$t2[1]);
  $ep_tit1=$t3[0];
  //echo $ep_tit1."\n";

  $l1="https://www.filme-online.to/ajax/movie_embed.php?";
  $l1 =$l1."eid=".$eid."B&lid=undefined&ts=".$ts."&up=0&mid=".$mid;
  $l1 =$l1."&gid=".$gid."&epNr=".$epNr."&type=tv&server=".$server."&epIndex=".$epIndex."&so=".$sc."&srvr=";
  //echo $l1."\n";
  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link="filmeto_s_fs.php?link=".urlencode($l1)."&page_tit=".urlencode(fix_t($tit))."&ep_tit=".urlencode(fix_t($ep_tit1))."&ep=".$epNr."&image=".$image;
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$ep_tit1.'</a>';
		echo '</TD>'."\n\r";
        $n++;
        if ($n > 2) {
         echo '</TR>'."\n\r";
         $n=0;
        }
}  
}
echo '</table>';
?>
<br></div></body>
</html>
