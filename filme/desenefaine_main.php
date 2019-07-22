<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>desenefaine.ro</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="6"><b>desenefaine.ro</b></font></TD></TR>';

$n=0;
if ($n == 0) echo "<TR>"."\n\r";

$title="Desene animate";
$link="desenefaine.php?tip=release&page=1&link=https://desenefaine.ro/desene-animate-online&title=desene+animate";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="Seriale in romana";
$link="desenefaine.php?tip=release&page=1&link=https://desenefaine.ro/seriale-in-romana&title=seriale+in+romana";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

$title="Filme animate";
$link="desenefaine.php?tip=release&page=1&link=https://desenefaine.ro/filme-animate-online&title=filme+animate";
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';

 echo "</TR>"."\n\r";
 echo '</table>';
?>
</body>
</HTML>
