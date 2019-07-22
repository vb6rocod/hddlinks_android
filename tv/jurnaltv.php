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
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="../jquery.nicescroll.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
	$("html").niceScroll({styler:"fb",cursorcolor:"#000"});
  });
</script>
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
$n=0;
echo '<h2 style="background-color:deepskyblue;color:black">'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$html=file_get_contents("http://www.jurnaltv.md/rss.xml");
$videos = explode('<item', $html);

unset($videos[0]);
$videos = array_values($videos);
///gallery_video/thumbs3_212949.jpg
foreach($videos as $video) {
  $t1 = explode('src="', $video);
  $t2=explode('"',$t1[1]);
  $img = $t2[0];
  $image = $img;
  $id = str_between($img,"thumbs3_",".jpg");
  $link = "http://video.jurnaltv.md/gallery_video/".$id.".mp4";
  $title = str_between($video,'<title>','</title>');
  $link1="direct_link.php?file=".$link."&title=".urlencode($title);
  if ($link <> "") {
  if ($n==0) echo '<TR>';
  echo '<td align="center" width="25%"><a href="'.$link1.'" target="_blank"><img src="'.$image.'" width="200px" height="106px"><BR><font size="4">'.$title.'</font></a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo "</table>";
?>
<br></body>
</html>
