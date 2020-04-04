<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>dailymotion</title>
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
$users=array(
array('DMG4TheWOLF','Filme Romanesti'),
array('VideoMIXX', 'Las Fierbinti de Romania')
);
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="6"><b>dailymotion</b></font></TD></TR>';

$n=0;
for ($k=0;$k<count($users);$k++) {
  $user=$users[$k][0];
  $title=$users[$k][1];
  $link="dailymotion.php?user=".$user."&title=".urlencode($title);
if ($n == 0) echo "<TR>"."\n\r";

echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
$n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
}
 echo '</table>';
?>
</body>
</HTML>
