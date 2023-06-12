<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Diverse...</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<H2></H2>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="4"><font size="6"><b>Diverse...</b></font></TD></TR>';
$list = glob($base_sub."*.srt");
foreach ($list as $l) {
    str_replace(" ","%20",$l);
     unlink($l);
}
$n=0;
if ($n == 0) echo "<TR>"."\n\r";

$title="Planet of the Apes";
$link='archive_ep.php?title='.urlencode("Planet of the Apes").'&link=https://archive.org/embed/PlanetOfTheApesTVSeries&sezon=1&imdb=tt0071033';
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="Stan si Bran";
$link="latimp.php?tip=release&page=1&link=stan&title=Stan+si+Bran";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';



$title="clicksud";
$link="clicksud_main.php";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';


$title="rovideo";
$link="rovideo.php?page=1&tip=release&title=rovideo&link=";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';



echo "</TR>"."\n\r";
echo "<TR>"."\n\r";
$title="filme_yt";
$link="../tv/playlist.php?title=filme_yt&link=https://raw.githubusercontent.com/vb6rocod/hddlinks/master/filme_yt.m3u";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
$title="dailymotion";
$link="dailymotion_fav.php";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="Cartoons";
$link="../tv/b98.php?page=1,,Cartoons";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$link="best_cs.php";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">Classic SciFi Movies</a></font></TD>';
echo "</TR>"."\n\r";
echo "<TR>"."\n\r";
$link="best.php?link=https://www.menshealth.com/entertainment/g33352561/best-alien-movies/&title=best-alien-movies";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">best-alien-movies</a></font></TD>';
$link="best.php?link=https://www.menshealth.com/entertainment/g34701308/best-thanksgiving-movies/&title=best-thanksgiving-movies";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">best-thanksgiving-movies</a></font></TD>';
$link="best.php?link=https://www.menshealth.com/entertainment/g34497836/funny-christmas-movies/&title=funny-christmas-movies";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">funny-christmas-movies</a></font></TD>';
$link="best_sf.php?link=&title=Best+SF+Series";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">Best SF Series</a></font></TD>';
echo "</TR>"."\n\r";
 echo '</table>';
?>
</body>
</HTML>
