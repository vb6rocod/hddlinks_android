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
$base_server="https://www4.the123movieshub.net";
echo '<h2>'.$tit.'</h2><BR>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center"><font size="4">'.$tit.'</font></TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $base_server.$link."/watching.html");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $h2 = curl_exec($ch);
  curl_close($ch);

$videos1 = explode('<div class="les-title"', $h2);
//><a title=
unset($videos1[0]);
$videos1 = array_values($videos1);

foreach($videos1 as $video1) {
  $t1=explode('strong>',$video1);
  $t2=explode('<',$t1[1]);
  $num_serv=$t2[0];
  //echo "=========================".$num_serv."============================"."\n";
  echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="4"></b> SERVER: '.$num_serv.'</b></font></td></TR>';
  $n=0;
  $videos = explode('<a title="',$video1);
  unset($videos[0]);
  //$videos = array_values($videos);
  $videos = array_reverse($videos);
  foreach($videos as $video) {
  $t1=explode('"',$video);
  $ep_tit=$t1[0];
  $t1=explode('data="',$video);
  $t2=explode('"',$t1[1]);
  $l1=$t2[0];
  $t2=explode('&title',$l1);
  $l1=$t2[0];
  $t1=explode("?c1_file",$l1);
  $l1=$t1[0];
  if (substr($l1, 0, 2) == "//") $l1="http:".$l1;
  //echo $ep_tit1."\n";

  //echo $l1."\n";
  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  if ($l1) {
  $link="123movies_s_fs.php?tip=series&link=".urlencode($l1)."&page_tit=".urlencode(fix_t($tit))."&ep_tit=".urlencode(fix_t($ep_tit))."&image=".$image;
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD><font size="4">'.'<a href="'.$link.'" target="_blank">'.$ep_tit.'</a></font>';
		echo '</TD>'."\n\r";
        $n++;
        if ($n > 2) {
         echo '</TR>'."\n\r";
         $n=0;
        }
  }
}  
}
echo '</table>';
?>
<br></div></body>
</html>
