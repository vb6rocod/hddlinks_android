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
   echo '<table border="1px" width="100%">'."\n\r";
   $n=0;
 $videos = explode('<li class="post-', $html);
$sezoane=array();
$last_sez="";
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
foreach($videos as $video) {
  //$t1=explode('class="title">Season',$video);
    $t1=explode('href="',$video);
    $t2=explode('season/',$t1[1]);
    $t3=explode('/',$t2[1]);
    if ($t3[0] <> $last_sez) {
      $sezoane[]=trim($t3[0]);
      $last_sez=$t3[0];
    }
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';

//$videos = explode('<li class="post-', $html);

//unset($videos[0]);
//$videos = array_values($videos);
//$videos = array_reverse($videos);
$last_sez="";
foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2=explode('season/',$t1[1]);
    $t3=explode('/',$t2[1]);
    if ($t3[0] <> $last_sez) {
       $n=0;
       $sez=$t3[0];
       echo '<table border="1" width="100%">'."\n\r";
       echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';
       $last_sez=$sez;
    }
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $link1=$t2[0];

    $t1 = explode('src="', $video);
    $t2 = explode('"', $t1[1]);
    $image = $t2[0];
    $se = trim(str_between($video,'rel="bookmark">','</a>'));
  if (preg_match("/Sezonul\s+(\d+)\s+Episodul\s+(\d+)/",$se,$m)) {
  //print_r ($m);
  $episod=$m[2];
  $season=$m[1];
  } else {
  $episod="";
  $season="";
  }
  $ep_tit=$season."x".$episod;


  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link="filme_link.php?file=".$link1.",".urlencode(fix_t($tit." ".$ep_tit));
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link.'" target="_blank"><img width="200px" height="100px" src="r_m.php?file='.$image.'"><BR>'.$ep_tit.'</a>';
		echo '</TD>'."\n\r";
        $n++;
        if ($n > 2) {
         echo '</TR>'."\n\r";
         $n=0;
        }
}  

echo '</table>';
?>
<br></div></body>
</html>
