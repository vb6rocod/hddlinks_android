<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$page1="";
$page1=$_GET["page1"];
if (!$page1) {
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
   $page_title=urldecode($queryArr[2]);
   $search=str_replace("|","&",$search);
}
} else {
 $search1=$_GET["src"];
file_put_contents($base_cookie."adult.dat",urldecode($search1));
 $search1=str_replace(" ","+",$search1);
 $page_title="Cautare: ".str_replace("+"," ",$search1);
 //$search="http://www.pornjam.com/search/?q=".$search1."&page=".$page1;
 //http://www.redtube.com/?search=&page=2
 $search=$search1;
}
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
<div id="mainnav">

<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
//http://www.hdfilm.ro/index.php?p=filme&gen=Actiune&page=1
echo '<H2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if ($page1) {
if ($page1 > 1)
echo '<a href="tvporn.php?page1='.($page1-1).'&src='.$search1.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="tvporn.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="tvporn.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
//$post = "s=".$search;
//https://tvporn.cc/page/2/?s=mom+son
if ($page1>1)
  $search3="https://tvporn.cc/page/".$page1."/?s=".$search;
else
  $search3="https://tvporn.cc/?s=".$search;
  //$search3="https://www.incestvidz.com/page/2/?s=mom";
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $search3);
     //curl_setopt ($ch, CURLOPT_POST, 1);
     //curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
     curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
     $html = curl_exec($ch);
     curl_close($ch);
     //echo $html;
} else {
if ($page > 1)
echo '<a href="tvporn.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="tvporn.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="tvporn.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
  //$search3=$search."page/".$page."/";
  $search3 = $search."page".$page."/?filter=latest";
  //echo $search3;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $search3);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_REFERER, "https://xhamster.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
}
//http://www.incestvidz.com/page/2/
$n=0;
//$videos = explode('<div class="video">', $html);
//$videos=explode('<span class="video-title">',$html);
//$t1=explode('<div class="post',$html);
//$p=count($t1);
//$html=$t1[count($t1)-1];
//echo $html;
$videos=explode('<article id="post-',$html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];

    //http://img02.redtubefiles.com/_thumbs/0000350/0350855/0350855_009m.jpg
    $t1 = explode('data-src="', $video);
    $t2 = explode('"', $t1[1]);
    $image = $t2[0];
    if (strpos($image,"http") === false) $image="http:".$image;
    //$image="r.php?file=".$image;
    //$t1=explode("h3>",$video);
    //$t2=explode(">",$t1[1]);
    //$t3=explode("<",$t2[1]);
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $title=strip_tags($t2[0]);
    $link = "../filme/filme_link.php?file=".urlencode($link)."&title=".urlencode($title);
  if ($title) {
  if ($n==0) echo '<TR>';
  echo '<td class="mp" align="center" width="25%"><a href="'.$link.'" target="_blank"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page1) {
if ($page1 > 1)
echo '<a href="tvporn.php?page1='.($page1-1).'&src='.$search1.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="tvporn.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="tvporn.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

} else {
if ($page > 1)
echo '<a href="tvporn.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="tvporn.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="tvporn.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

}echo "</table>";
?>
<br></div></body>
</html>
