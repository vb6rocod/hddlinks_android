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

$title="The Visitors";
$link='archive_ep.php?title='.urlencode("The Visitors").'&link=https://archive.org/embed/TheVisitors&sezon=1&imdb=tt0085106';
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="Sliders";
$link='archive_ep.php?title='.urlencode("Sliders").'&link=https://archive.org/embed/sliders1x011x02pilot&sezon=1&imdb=tt0112167';
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';


 echo "</TR>"."\n\r";
$n=0;
if ($n == 0) echo "<TR>"."\n\r";
$title="clicksud";
$link="clicksud_main.php";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
$title="Seriale Turcesti";
$link="serialeturcesti.php?page=1&tip=release&title=serialeturcesti&link=";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
$title="Filme";
$link="../tv/filme_main.php";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="CINEMATOGRAFIA ROMANEASCA";
$link="yt_channel.php?token=&id=UC7612aZQ9llm_nR_3wDDqGA&kind=channel&title=(channel)+CINEMATOGRAFIA+ROMANEASCA&image=https://yt3.ggpht.com/a/AATXAJynQBFCnYb0qbwYGXKMFwnDYSiH1-izNKHu-5DW=s88-c-k-c0x00ffffff-no-rj";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

echo "</TR>"."\n\r";
 echo '</table>';
?>
</body>
</HTML>
