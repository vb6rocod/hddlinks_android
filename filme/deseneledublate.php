<!DOCTYPE html>
<?php
include ("../common.php");
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
   $page_title=urldecode($queryArr[2]);
   $search=str_replace("|","&",$search);
}
if ($page==1)
  $l="http://deseneledublate.blogspot.ro/";
else
  $l="http://deseneledublate.blogspot.ro/search?updated-max=".urlencode($search)."&max-results=32";
$l=str_replace("deseneledublate.blogspot.ro","desenele-dublate.blogspot.ro",$l);
//http://desenele-dublate.blogspot.ro/
  //echo $l;
  //2016-03-23T07:38:00-07:00
  //http://desenele-dublate.blogspot.ro/search?updated-max=2016-03-23T07:38:00-07:00&max-results=32
  //http://deseneledublate.blogspot.ro/search?updated-max=2015-03-26T18%3A47%3A00%2B02%3A00&max-results=20
  //http://deseneledublate.blogspot.ro/search?updated-max=2015-03-26T18:47:00+02:00&max-results=20
  //http://desenele-dublate.blogspot.ro/search?updated-max=2016-03-23T07:38:00-07:00&max-results=32
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
  $t1=explode("a class='blog-pager-newer-link",$html);
if (sizeof ($t1) > 1 ) {
  $t2=explode("href='",$t1[1]);
  $t3=explode("'",$t2[1]);
  $t4=explode("search?updated-max=",$t3[0]);
  $search_prev="";
if (sizeof ($t4) > 1 ) {
  $t5=explode("&",$t4[1]);
  $search_prev=$t5[0];
}
}
  $t1=explode("a class='blog-pager-older-link",$html);
  $t2=explode("href='",$t1[1]);
  $t3=explode("'",$t2[1]);
  //echo $t3[0];
  //die();
  $t4=explode("search?updated-max=",$t3[0]);
  $t5=explode("&",$t4[1]);
  $search=$t5[0];
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>deseneledublate</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body><div id="mainnav">
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$n=0;
echo '<H2>deseneledublate</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="deseneledublate.php?page='.($page-1).','.$search_prev.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="deseneledublate.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="deseneledublate.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

 $videos = explode("h3 class='post-title entry-title", $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {

  $t1 = explode("href='", $video);
  $t2 = explode("'", $t1[1]);
  $link = $t2[0];

  $t3 = explode(">",$t1[1]);
  $t4 = explode("<",$t3[1]);
  $title = $t4[0];
  
  $title=trim(preg_replace("/- filme online subtitrate/i","",$title));
  $t1 = explode('src="', $video);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  if ($n==0) echo '<TR>';
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="filme_link.php?file='.urlencode($link).','.urlencode(fix_t($title)).'" target="_blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="deseneledublate.php?page='.($page-1).','.$search_prev.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="deseneledublate.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="deseneledublate.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo "</table>";
?>
<br></div></body>
</html>
