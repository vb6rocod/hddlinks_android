<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$imdb=$_GET["imdb"];
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
$requestLink = "https://tv-v2.api-fetch.website/show/".$imdb;
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
  $p=$r["episodes"];
  $e=array();
  for ($k=0;$k<count($p)-1;$k++) {
    $ep=$p[$k]["episode"];
    $sez=$p[$k]["season"];
    $title=$p[$k]["title"];
    $e[]=array("sez"=>$sez,"ep"=>$ep,"title"=>$title);
  }
  //print_r ($e);
foreach ($e as $key => $row) {
    $sezon[$key]  = $row['sez'];
    $episod[$key] = $row['ep'];
}
//$sezon  = array_column($e, 'sez');
//$episod = array_column($e, 'ep');
array_multisort($sezon, SORT_ASC, $episod, SORT_ASC, $e);
//print_r ($e);
//  die();
for ($k=0;$k<count($e)-1;$k++) {
  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link="popcorn_fs.php?tip=series&imdb=".$imdb."&title=".urlencode(fix_t($tit))."&sez=".$e[$k]["sez"]."&ep=".$e[$k]["ep"]."&ep_tit=".urlencode(fix_t($e[$k]["title"]));
      if ($n == 0) echo "<TR>"."\n\r";
      $ep_tit=$e[$k]["sez"]."x".$e[$k]["ep"]." - ".$e[$k]["title"];
		echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$ep_tit.'</a>';
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
