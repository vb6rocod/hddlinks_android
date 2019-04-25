<!DOCTYPE html>
<?php
include ("../common.php");
$cookie=$base_cookie."antena.dat";
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page= $queryArr[0];
   $search = $queryArr[1];
   $tit = urldecode($queryArr[2]);
   $tit=str_replace("\\","",$tit);
   $page_title=$tit;
}
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $tit; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<div id="mainnav">
<H2></H2>
<h2><?php echo $tit; ?> (abonament)</h2>
<table border="1px" width="100%">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

$search1=str_replace("&","|",$search);
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="antenaplay_main.php?page='.($page-1).','.$search1.','.urlencode($page_title).'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="antenaplay_main.php?page='.($page+1).','.$search1.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="antenaplay_main.php?page='.($page+1).','.$search1.','.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';

//echo '<h2>Antena Play</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$pop=$base_pass."antenaplay.txt";
if (file_exists($pop) && !file_exists($cookie)) {
  $handle = fopen($pop, "r");
  $c = fread($handle, filesize($pop));
  fclose($handle);
  $a=explode("|",$c);
  $a1=str_replace("?","@",$a[0]);
  $user=urlencode($a1);
  $user=str_replace("@","%40",$user);
  $pass=trim($a[1]);
$l="https://antenaplay.ro/login";
$post="email=".$user."&password=".$pass."&returnurl=http%3A%2F%2Fantenaplay.ro%2F";
  $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8','Accept-Language: ro-ro,ro;q=0.8,en-us;q=0.6,en-gb;q=0.4,en;q=0.2','Accept-Encoding: gzip, deflate','Content-Type: application/x-www-form-urlencoded','Content-Length: '.strlen($post));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://antenaplay.ro/");
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt ($ch, CURLOPT_POST, 1);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_exec($ch);
  curl_close($ch);
}
$link="https://antenaplay.ro/ajaxs/ajaxshows?page=".$page;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "http://antenaplay.ro/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $link;
//$html=file_get_contents($link);
$videos=explode('<div class="span3 spanX',$html);
unset($videos[0]);
$videos = array_values($videos);
$n=0;
foreach($videos as $video) {
    if ($n == 0) echo "<TR>"."\n\r";
    $cat=str_between($video,'<a href="/','"');
	$title=str_between($video,'alt="','"');
	$data=str_between($video,'<div class="sname">','</div>');
	$data=str_replace("<div>","",$data);
	$data=str_replace("<p>							</p>","",$data);
	$image=str_between($video,'data-src="','"');
    $link="antenaplay.php?file=".urlencode($cat);
  if ($title) {
	if ($n == 0) echo "<TR>"."\n\r";
echo '
<TD><table border="0px">
<TD align="center" width="20%"><a href="'.$link.'&title='.$title.'" target="_blank"><img src="'.$image.'" width="171" height="96"></a><BR><a href="'.$link.'" target="_blank"><b>'.$title.'</b></a></TD>

</TABLE></TD>
';
$n++;
    if ($n > 4) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
}
 if ($n<0) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div>
<br></body>
</html>
