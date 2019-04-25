<!DOCTYPE html>
<?php
include ("../common.php");
$page = $_GET["page"];
$search=$_GET["link"];
$page_title=urldecode($_GET["title"]);
if (!$page)
$page_title = "Cautare: ".$search;
if($page) {
	$l=$search."page/".$page."/";
} else {
$search=str_replace(" ","+",$search);
$post= "do=search&subaction=search&story=".$search;
//echo $html;
}
//https://www.filme3d.net/page/2/
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
        self = evt.target;
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=movie&" + val_imdb;
     window.open(msg);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
</script>
</head>
<body><div id="mainnav">
<div class="balloon"></div>
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$w=0;
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';

echo '<table border="1px" width="100%">'."\n\r";

if ($page) {
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="filmehdgratis.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="filmehdgratis.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="filmehdgratis.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
}
if ($page) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
} else {
  $l="http://filme3d.net/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  $html = curl_exec($ch);
  curl_close($ch);
}
//echo $html;
if (strpos($html,'class="short_post') !== false)
$videos = explode('class="short_post', $html);
else if (strpos($html,'class="movie_box') === false)
$videos = explode('id="post"', $html);
else
$videos = explode('class="movie_box', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $t1 = explode('href="', $video);
  $t2 = explode('"', $t1[1]);
  $link = $t2[0];
  /*
  if (strpos($video,'class="tt">') !== false) {
  $t1=explode('class="tt">',$video);
  //$t2=explode('>',$t1[1]);
  $t2_0=explode('<',$t1[1]);
  } else {
  $t1=explode("h1>",$video);
  $t2_0=explode("</",$t1[1]);
  }
  $t3=str_replace("Vizioneaza Film Online","",$t2_0[0]);
  $t4=explode("&#8211;",$t3);
  $title=trim($t4[0]);
  */
  if (!$page) {
    $t1=explode('id="post">',$video);
    $t2=explode('<',$t1[1]);
    $title=$t2[0];
  } else {
  $t1=explode('alt="',$video);
  $t2=explode('"',$t1[1]);
  $title=$t2[0];
  }
  $title=str_replace("&#8211;","-",$title);
  $title=str_replace("&#8217;","'",$title);
  $title=trim(preg_replace("/(gratis|subtitrat|onlin|film|sbtitrat|\shd)(.*)/i","",$title));
  if (preg_match("/\(?((1|2)\d{3})\)?/",$title,$r)) {
     //print_r ($r);
     $year=$r[1];
  } else {
     $year="";
  }
  $t1=explode(" - ",$title);
  $t=$t1[0];
  $t=preg_replace("/\(?((1|2)\d{3})\)?/","",$t);
  $tit3=trim($t);

  if (!$page)  {
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $t3=explode('?',$t2[0]);
    $image=$t3[0];
  } else {
  $t1=explode('data-src="',$video);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
  }
  //$image="r.php?file=".$image;
  if ($n==0) echo '<TR>';
  // <a class="various fancybox.ajax" href="imdb.php?id='.urlencode($tit3).'"><b>?</b></a>
  if ($tast == "NU")
    echo '<td align="center" width="25%"><a href="filme_link.php?file='.urlencode($link).','.urlencode($title).'" target="blank"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.$title.'</font></a></TD>';
  else {
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="filme_link.php?file='.urlencode($link).','.urlencode($title).'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.$title.'</font></a></TD>';
  $w++;
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
if ($page) {
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="filmehdgratis.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="filmehdgratis.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="filmehdgratis.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
}
echo "</table>";
?>
<br></div></body>
</html>
