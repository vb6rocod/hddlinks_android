<!doctype html>
<?php
include ("../common.php");
error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
//query=8612&tv=0&title=The+Intervention+(2016)&serv=30&hd=NU
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$imdb=urldecode($_GET["imdb"]);
$tip=$_GET["tip"];
if ($tip=="movie") {
$tit2="";
  $sez="";
  $ep="";
} else {
$tip="series";
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
$tit2=$sez."x".$ep." - ".$ep_tit;
}

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
//echo $link;
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
if ($tip=="movie")
  $link="https://tv-v2.api-fetch.website/movie/".$imdb;
else
  $link="https://tv-v2.api-fetch.website/show/".$imdb;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://tv-v2.api-fetch.website");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
//die();
$r=json_decode($html,1);
if ($tip=="movie")
  $t=$r["torrents"];
else {
 //print_r ($r);
 //echo $sez. " ".$ep;
 $p=$r["episodes"];
 //print_r ($p);
 for ($k=0;$k<count($p)-1;$k++) {
   if ($p[$k]["season"] == $sez && $p[$k]["episode"] == $ep) {
    $t=$p[$k]["torrents"];
    break;
   }
 }
}
//print_r ($t);
//die();

?>
<html>



   <head>

      <meta charset="utf-8">
      <title><?php echo $tit." ".$tit2; ?></title>
	  <link rel="stylesheet" type="text/css" href="../custom.css" />
     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

  </head>
   <body><div id="mainnav">
  <a href='' id='mytest1'></a>
<?php

//echo '<h2 style="background-color:deepskyblue;color:black;">'.$tit.' '.$tit2.'</H2>';
echo '<table border="0px" width="100%"><TR>'."\n\r";
echo '<TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px"><font size="6px" color="#64c8ff"><b>'.$tit." ".$tit2.'</b></font></td>';
echo '</TR></TABLE>';
//echo '<font size="5"><b>Server curent: <label id="server22">'.$r[0].'</label></b></font>';

if ($tip=="movie") {
  $tit3=$tit;

  $imdbid=str_replace("tt","",$imdb);
  $from="";
  $link_page="";
  $ep_tit="";
} else {
  $tit3=$tit;

  $imdbid=str_replace("tt","",$imdb);
  $from="";
  $link_page="";
  $ep_tit=$sez."x".$ep." - ".$ep_tit;
}
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".$ep_tit;
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4px" color="#64c8ff"><b>Alegeti o subtitrare</b></font></td></TR>';
echo '<TR>';
echo '<TD align="center"><font size="4"><b><a href="opensubtitles.php?'.$sub_link.'">opensubtitles</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a href="titrari_main.php?page=1&'.$sub_link.'">titrari.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a href="subs_main.php?'.$sub_link.'">subs.ro</b</font></a></td>';
echo '<TD align="center"><font size="4"><b><a href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</b</font></a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie") {
foreach ($t as $lang=>$url) {
  foreach($url as $key=>$value) {
   $seed=$t[$lang][$key]["seed"];
   $peer=$t[$lang][$key]["peer"];
   $q=$key;
   $m=$t[$lang][$key]["url"];
   $tit_q="(".$lang.") ".$q." Seed:".$seed."/Peer:".$peer;
   $link1=$m;
   //$m="sop://broker.sopcast.com:3912/253471";
   //$m="acestream://44c33d2dd50d62b5a123b2291c295fa0f012d183";
   //http://<engine_address>:<engine_port>/ace/getstream
   $link2="intent:".$m."#Intent;package=com.amnis;S.title=".urlencode($tit3).";end";
   $link2="intent:".$m."#Intent;package=org.acestream.media.atv;S.title=".urlencode($tit3).";end";
 if ($flash == "mp" || $flash=="chrome")
   echo '<TD align="center" colspan="4"><font size="4"><b><a href="'.$link2.'"><font size="4">'.$tit_q.'</b></font></a></td>';
 else
   echo '<TD align="center" colspan="4"><font size="4"><b><a href="'.$link1.'"><font size="4">'.$tit_q.'</b></font></a></td>';
 }
}
echo '</tr>';
echo '</table>';
} else {
  foreach($t as $key=>$value) {
   $seed=$t[$key]["seeds"];
   $peer=$t[$key]["peers"];
   $q=$key;
   $m=$t[$key]["url"];
   $tit_q=$q." Seed:".$seed."/Peer:".$peer;
   $link1=$m;
   //$m="sop://broker.sopcast.com:3912/253471";
   //$m="acestream://44c33d2dd50d62b5a123b2291c295fa0f012d183";
   //http://<engine_address>:<engine_port>/ace/getstream
   $link2="intent:".$m."#Intent;package=com.amnis;S.title=".urlencode($tit3).";end";
   $link2="intent:".$m."#Intent;package=org.acestream.media.atv;S.title=".urlencode($tit3).";end";
 if ($flash == "mp" || $flash=="chrome")
   echo '<TD align="center" colspan="4"><font size="4"><b><a href="'.$link2.'"><font size="4">'.$tit_q.'</b></font></a></td>';
 else
   echo '<TD align="center" colspan="4"><font size="4"><b><a href="'.$link1.'"><font size="4">'.$tit_q.'</b></font></a></td>';
 }
echo '</tr>';
echo '</table>';
}
echo '<br></div>
<p>Pentru vizionare instalati Ace Stream Media din Google Play sau de <a href="http://www.mediafire.com/file/8uyifuly87gbuwv/ace-stream-media-3-1-31-0.apk/file">aici</a>.<BR>
Setati la Output format pe "original".<BR>
Daca ati descarcat o subtitrare, dupa ce porneste MX Player alegeti subtitrarea (Menu -> Subtitrare -> Deschide -> sub_extern.srt).</p>
</body>
</html>';
