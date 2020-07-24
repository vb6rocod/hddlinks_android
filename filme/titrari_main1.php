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
<H2><?php echo $page_tit; ?></H2>
<table border="1" width="100%"><tr>

<?php
$year="";
if (!$imdbid) {
  if ($tip == "series") {
    if (!$year)
     $find=$title." serie";
    else
     $find=$title." serie ".$year;
  } else {
    if (!$year)
     $find=$title." movie";
    else
     $find=$title." movie ".$year;
  }
  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/https:\/\/www.imdb.com\/title\/(tt\d+)/ms', $h, $match))
   $imdbid=str_replace("tt","",$match[1]);
}
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);
if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next="titrari_main.php?page=".($page+1)."&".$p;
$prev="titrari_main.php?page=".($page-1)."&".$p;
echo '<TD colspan="2" align="left">';
if ($page>1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD>';
echo '</TR>';
  $page1=$page-1;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
  if ($imdbid)
  $l="https://www.xn--titrri-l0a.ro/index.php?page=cautareavansata&z1=".$page1."&z2=&z3=-1&z4=-1&z5=".$imdbid."&z6=0&z7=&z8=1&z9=All&z10=&z11=0";
  else
  $l="https://www.xn--titrri-l0a.ro/index.php?page=cautareavansata&z1=".$page1."&z2=&z3=-1&z4=-1&z5=&z6=0&z7=".urlencode($title)."&z8=1&z9=All&z10=&z11=0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://www.titrari.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);
$videos=explode('<h1><a style=color:black',$h);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $t1=explode(">",$video);
  $t2=explode("<",$t1[1]);
  $title=$t2[0];
  $t1=explode("<td class=comment",$video);
  //$t2=explode(">",$t1[1]);
  $t3=explode("</tr>",$t1[1]);
  $desc="<td class=comment".$t3[0];
  //$desc = preg_replace("/(<\/?)(\w+)([^>]*>)/e","",$desc);
  $desc = preg_replace("/(<\/?)(\w+)([^>]*>)/","",$desc);
  $t1=explode("get.php?",$video);
  $t2=explode('>',$t1[1]);
  $link=$t2[0];
  echo '<TR>';
  //echo '<TD><font size="4"><a id="myLink" href="#" onclick="changeserver('."'".$link."'".');return false;">'.$title.'</a></font></TD><TD>'.$desc.'</TD></TR>'."\r\n";
  echo '<TD width="33%"><a id="myLink" href="titrari_sub.php?'.$link.'&title='.urlencode(fix_t($title)).'&page_tit='.urlencode(fix_t($page_tit)).'">'.$title.'</a></TD><TD>'.$desc.'</TD></TR>'."\r\n";

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
</div></body>
</HTML>
