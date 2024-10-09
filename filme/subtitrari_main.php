<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function get_value($q, $string) {
   $t1=explode($q,$string);
   return str_between($t1[1],"<string>","</string>");
}
   function generateResponse($request)
    {
        $context  = stream_context_create(
            array(
                'http' => array(
                    'method'  => "POST",
                    'header'  => "Content-Type: text/xml",
                    'content' => $request
                )
            )
        );
        $response     = file_get_contents("http://api.opensubtitles.org/xml-rpc", false, $context);
        return $response;
    }
//$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$tit3."&link=".$link_page;
$from=$_GET["from"];
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$imdbid=$_GET["imdb"];
$title=unfix_t(urldecode($_GET["title"]));
$link=$_GET["link"];
if (isset($_GET["ep_tit"]))
 $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
else
 $ep_tit="";
if ($ep_tit)
  $page_tit=$title." ".$ep_tit;
else
  $page_tit=$title;
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body><div id="mainnav">
<H2></H2>
<table border="1" width="100%"><tr>
<H2><?php echo $page_tit; ?></H2>
<?php
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);
if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next="subtitrari_main.php?page=".($page+1)."&".$p;
$prev="subtitrari_main.php?page=".($page-1)."&".$p;
echo '<TD colspan="2" align="left">';
if ($page>1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
echo '</TR>';
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$l="https://subtitrari-noi.ro/paginare_filme.php";
//search_q=1&query_q=G&cautare=G&tip=2&an=Toti anii&gen=Toate
// search_q=1&query_q=9686790&cautare=9686790&tip=2&an=Toti anii&gen=Toate

if ($imdbid)
   $post="search_q=".$page."&query_q=".$imdbid."&cautare=".$imdbid."&tip=2&an=Toti+anii&gen=Toate";
else
   $post="search_q=".$page."&query_q=".urlencode($title)."&cautare=".urlencode($title)."&tip=2&an=Toti+anii&gen=Toate";
//echo $post;
  $head=array('X-Requested-With: XMLHttpRequest',
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
  'Accept-Language: ro-ro,ro;q=0.8,en-us;q=0.6,en-gb;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate','Content-Type: application/x-www-form-urlencoded',
  'Origin: https://subtitrari-noi.ro',
  'Content-Length: '.strlen($post));
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://subtitrari-noi.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
$videos=array();
$cc="0";
$videos=explode('div id="content">',$h);
unset($videos[0]);
$videos = array_values($videos);
$cc=count($videos);
foreach($videos as $video) {
  $t1=explode("href='",$video);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[1]);
  $title=$t3[0];
  $t1=explode('id="bottom">',$video);
  $t2=explode('<div',$t1[1]);
  $t2_1=explode('>',$t2[1]);
  $t3=explode("</",$t2_1[1]);
  $desc=$t3[0];
  $desc = preg_replace("/(<\/?)(\w+)([^>]*>)/","",$desc);
  $t1=explode('class="buton">',$video);
  $t2=explode('href="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $link=$t3[0];
  echo '<TR>';
  //echo '<TD><font size="4"><a id="myLink" href="#" onclick="changeserver('."'".$link."'".');return false;">'.$title.'</a></font></TD><TD>'.$desc.'</TD></TR>'."\r\n";
  echo '<TD width="33%"><a id="myLink" href="subtitrari_sub.php?id='.$link.'&title='.urlencode(fix_t($title)).'&page_tit='.urlencode(fix_t($page_tit)).'&cc='.$cc.'">'.$title.'</a></TD><TD>'.$desc.'</TD></TR>'."\r\n";
}
echo '<TR><TD colspan="2" align="left">';
if ($page>1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
echo '</TR>';
?>

</TABLE>
<BR>
<?php
if ($cc==0) {
    echo "Nu am gasit subtitrari.";
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
}
?>
</div></body>
</HTML>
