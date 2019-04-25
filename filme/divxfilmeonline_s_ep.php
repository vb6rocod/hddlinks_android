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

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://divxfilmeonline.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);


$videos = explode('li class="border-', $html);

unset($videos[0]);
if (!preg_match("/sezonul\-\d+\-episodul-\d+/",$html)) {
$videos = array_values($videos);
$s="sez";
} else {
$videos = array_reverse($videos);
$s="ep";
}
$sezoane=array();
$last_sez="";
//unset($videos[0]);
//$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  //$t1=explode('class="title">Season',$video);
    $t1=explode('href="',$video);
    //$t2=explode('sezonul-',$t1[1]);
    //$t3=explode('/',$t2[1]);
    preg_match("/sezonul\-(\d+)/",$t1[1],$m);
    //print_r ($m);
    if ($m[1] <> $last_sez) {
      $sezoane[]=trim($m[1]);
      $last_sez=$m[1];
    }
}
//print_r ($sezoane);
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';
$last_sez="";
foreach($videos as $video) {
    $t1=explode('href="',$video);
    preg_match("/sezonul\-(\d+)/",$t1[1],$m);
    if ($m[1] <> $last_sez) {
       $n=0;
       $sez=$m[1];
       echo '<table border="1" width="100%">'."\n\r";
       echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';
       $last_sez=$sez;
    }
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $l=$t2[0];
    if (!preg_match("/sezonul\-\d+\-episodul-\d+/",$l)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt ($ch, CURLOPT_REFERER, "https://divxfilmeonline.org");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close($ch);
    $vids = explode('li class="border-', $h1);
    unset($vids[0]);
    //$videos = array_values($videos);
    $vids = array_reverse($vids);
    foreach($vids as $vid) {
    $t1=explode('href="',$vid);
    $t2=explode('"',$t1[1]);
    $link1 = $t2[0];
    //$link1="";
    $ep_tit = trim(str_between($vid,'title="','"'));
    $link="filme_link.php?file=".$link1.",".urlencode(fix_t($ep_tit));
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link.'" target="_blank"><img width="200px" height="100px" src="r_m.php?file='.$image.'"><BR>'.$ep_tit.'</a>';
		echo '</TD>'."\n\r";
        $n++;
        if ($n > 2) {
         echo '</TR>'."\n\r";
         $n=0;
        }
    }
    } else {
    $link1 = $l;
    //$link1="";
    $ep_tit = trim(str_between($video,'title="','"'));



  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link="filme_link.php?file=".$link1.",".urlencode(fix_t($ep_tit));
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link.'" target="_blank"><img width="200px" height="100px" src="r_m.php?file='.$image.'"><BR>'.$ep_tit.'</a>';
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
