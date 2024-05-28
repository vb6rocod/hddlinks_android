<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>emisiuni.net</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>emisiuni.net</h2>';
echo '<table border="1px" width="100%">'."\n\r";
$r=array();
$title="Stirile ProTV";
$link="https://emisiuni.net/category/stirile-protv-c9";
$image="https://emisiuni.net/wp-content/uploads/2022/11/1a516dda-7af4-49ef-b43c-e46399c51a11-1024x576.jpeg";
$r[]=array($title,$link,$image);
$n=0;
$l="https://emisiuni.net/toate-emisiunile-c1";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  
//$html=str_between($html,'<div id="main-section">','</ul');
$videos = explode('data-lazy-src="', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('"',$video);
 $image=$t1[0];
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 $link=$t2[0];
 $t3=explode(">",$t1[1]);
 $t4=explode('<',$t3[1]);
 $title=$t4[0];
 if (preg_match("/category/",$link)) $r[]=array($title,$link,$image);
}
for ($k=0;$k<count($r);$k++) {
    $title=$r[$k][0];
    $l2=$r[$k][1];
    $image=$r[$k][2];
    $link="emisiuni.php?link=".$l2."&title=".urlencode($title)."&page=1";
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank"><img src="'.$image.'" width="250px" height="150px"><BR>'.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }

}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
</BODY>
</HTML>
