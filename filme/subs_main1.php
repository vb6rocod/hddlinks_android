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
<body>
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
  $page1=0;
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
  if ($imdbid)
  $l="https://subs.ro/subtitrari/imdbid/".$imdbid;
  else {
  $l="https://subs.ro/subtitrari/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://subs.ro/subtitrari/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $x = curl_exec($ch);
  curl_close($ch);
  //echo $x;
  $t1=explode('name="antispam',$x);
  $t2=explode('value="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l="https://subs.ro/ajax/search/?search-text=".urlencode($title)."&amp;in=name&amp;antispam=".$t3[0];
  }

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://subs.ro/subtitrari/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);
$videos=array();
$cc="0";
$videos=explode('<div class="w-full grid',$h);
unset($videos[0]);
$videos = array_values($videos);
$cc=count($videos);
foreach($videos as $video) {
  $t1=explode('title="',$video);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[1]);
  $title=trim($t3[0]);
  if (preg_match("/flag\-rom/",$video))
   $title="&#x1F1F7;&#x1F1F4; ".$title;
  //$t1=explode('class="sub-comment">',$video);
  $t1=explode('p class="text-sm font-base overflow-auto h-auto lg:h-16">',$video);
  //$t2=explode("</div",$t1[1]);
  $t2=explode('</p',$t1[1]);
  $desc=trim($t2[0]);
  $desc=str_replace('<span style="color: red;">',"",$desc);
  $desc=str_replace('<span style="color: blue;">',"",$desc);
  $desc=str_replace('<span style="color: green;">',"",$desc);
  $desc = preg_replace("/(<\/?)(\w+)([^>]*>)/","",$desc);
  $t1=explode('descarca/',$video);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  echo '<TR>';
  //echo '<TD><font size="4"><a id="myLink" href="#" onclick="changeserver('."'".$link."'".');return false;">'.$title.'</a></font></TD><TD>'.$desc.'</TD></TR>'."\r\n";
  echo '<TD width="33%"><a id="myLink" href="subs_sub.php?id='.$link.'&title='.urlencode(fix_t($title)).'&page_tit='.urlencode(fix_t($page_tit)).'&cc='.$cc.'">'.$title.'</a></TD><TD>'.$desc.'</TD></TR>'."\r\n";
}

?>
</TABLE>
<BR>
<?php
if ($cc==0) {
    echo "Nu am gasit subtitrari.";
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
}
?>
</body>
</HTML>
