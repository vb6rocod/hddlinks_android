<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["file"];
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
echo '<h2>'.$tit.'</h2>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg

$requestLink=$link;
//$cookie="/tmp/vumoo.txt";
//$cookie="D:/";
$cookie=$base_cookie."hdpopcorns.dat";
$ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://tvhub.org/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
 //$videos = explode('<div class="imagen"', $html);
   echo '<table border="1px" width="100%">'."\n\r";
   $n=0;
 $videos = explode('span class="se-t se-o">', $html);
$sezoane=array();
unset($videos[0]);
$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  //$t1=explode('class="title">Season',$video);
  $t2=explode("<",$video);
  $sezoane[]=trim($t2[0]);
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';

foreach($videos as $video) {
 // $t1=explode('class="title">Season',$video);
  $t2=explode("<",$video);
  $season=trim($t2[0]);
  $sez = $season;
  $first=true;
  $vids = explode('<li', $video);
unset($vids[0]);
$videos = array_values($videos);
//$vids = array_reverse($vids);
foreach($vids as $vid) {
  if ($first) {
    $first=false;
    $n=0;
    echo '<table border="1" width="100%">'."\n\r";
    echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';


  }
  //Star Trek: Enterprise Season 1 Episode 1 - Broken Bow: Part 1
  $t1=explode('href="',$vid);
  $t2=explode('"',$t1[1]);
  $link1=$t2[0];
    $t3=explode(">",$t1[1]);
    $t4=explode("<",$t3[1]);
    $se = trim($t4[0]);
  if (preg_match("/Sezonul\s+(\d+)\s+Episodul\s+(\d+)/",$se,$m)) {
  //print_r ($m);
  $episod=$m[2];
  $season=$m[1];
  } else {
  $episod="";
  $season="";
  }
  $t1=explode('title="',$vid);
  $t2=explode('"',$t1[1]);
  $ep_tit=$t2[0];
  if (!$ep_tit) $ep_tit="N/A";

  //$ep_tit = trim($t4[0])." ".$ep_tit;
  $ep_tit=$season."x".$episod." - ".$ep_tit;

  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link="filme_link.php?file=".$link1.",".urlencode(fix_t($tit." ".$ep_tit));
  if ($episod) {
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link.'" target="_blank"><img width="200px" height="100px" src="'.$image.'"><BR>'.$ep_tit.'</a>';
		echo '</TD>'."\n\r";
        $n++;
        if ($n > 2) {
         echo '</TR>'."\n\r";
         $n=0;
        }
  }
}  

echo '</table>';
}
echo '</table>';
?>
<br></div></body>
</html>
