<!doctype html>
<?php
function unfuck($js) {
  $js=str_replace('(+[![]]+[+(+!+[]+(!+[]+[])[!+[]+!+[]+!+[]]+[+!+[]]+[+[]]+[+[]]+[+[]])])[+!+[]+[+[]]]',"'y'",$js);
  $js = str_replace('+[]','0',$js);
  $a1 =array(
   'false' => '![]',
   'true' => '!![]',
   'undefined' => '[][[]]',
   'NaN' => '+[![]]',
   'Infinity' => '+(+!+[]+(!+[]+[])[!+[]+!+[]+!+[]]+[+!+[]]+[+[]]+[+[]]+[+[]])',
  );
  foreach($a1 as $key=>$value) {
    $js=str_replace($value,$key,$js);
  }
  preg_match_all("/\[([\+\!]*0\+?)+\]/",$js,$m);
  for ($k=0;$k<count($m[0]);$k++) {
    $s=str_replace("[","",$m[0][$k]);
    $s=str_replace("]","",$s);
    $code="\$v=".$s.";";
    eval ($code);
    $js=str_replace($m[0][$k],"'".$v."'",$js);
  }

  $js=str_replace('([false]0[[]])','(falseundefined)',$js);
  $js=str_replace("(undefined0)'2'",'d',$js);
  $js=str_replace("(false0)'0'","f",$js);
  $js=str_replace("(false0)'1'","a",$js);
  $js=str_replace("(false0)'3'","s",$js);
  $js=str_replace("(false0)'2'","l",$js);
  $js=str_replace("(!false0)'1'","r",$js);
  $js=str_replace("(undefined0)'0'","u",$js);
  $js=str_replace("(undefined0)'1'","n",$js);
  preg_match_all("/\[(.*?)\]/",$js,$m);
  for ($k=0;$k<count($m[0]);$k++) {
    $s=str_replace("[","",$m[0][$k]);
    $s=str_replace("]","",$s);
    $s=str_replace("'","",$s);
    $code="\$v=".$s.";";
    eval ($code);
    $js=str_replace($m[0][$k],"'".$v."'",$js);
  }
  $js=str_replace("(falseundefined)'1'","i",$js);
  $js=str_replace("++","+",$js);
  $js=str_replace("'","",$js);
  $js=str_replace("+","",$js);
  return $js;
}
include ("../common.php");
error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists($base_pass."debug.txt"))
 $debug=true;
else
 $debug=false;
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
$tit=unfix_t(urldecode($_GET["title"]));
$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
$ep_title=prep_tit($ep_title);
$year=$_GET["year"];
if ($tip=="movie") {
$tit2="";
} else {
if ($ep_title)
   $tit2=" - ".$sez."x".$ep." ".$ep_title;
else
   $tit2=" - ".$sez."x".$ep;
$tip="series";
}
$imdbid="";

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title><?php echo $tit.$tit2; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
function openlink1(link) {
  link1=document.getElementById('file').value;
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
}
function openlink(link) {
  on();
  var request =  new XMLHttpRequest();
  link1=document.getElementById('file').value;
  var the_data = "link=" + link1 + "&title=" + link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      <?php
      if ($debug) echo "document.getElementById('debug').innerHTML = request.responseText.match(/http.+#/g);"."\r\n";
      ?>
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function changeserver(s,t) {
  document.getElementById('server').innerHTML = s;
  document.getElementById('file').value=t;
}
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     //alert (charCode);
     if (charCode == "49") {
      document.getElementById("opensub").click();
     } else if (charCode == "50") {
      document.getElementById("titrari").click();
     } else if (charCode == "51") {
      document.getElementById("subs").click();
     } else if (charCode == "52") {
      document.getElementById("subtitrari").click();
     } else if (charCode == "53") {
      document.getElementById("viz").click();
     } else if (charCode == "55") {
      document.getElementById("opensub1").click();
     } else if (charCode == "56") {
      document.getElementById("titrari1").click();
     } else if (charCode == "57") {
      document.getElementById("subs1").click();
     } else if (charCode == "48") {
      document.getElementById("subtitrari1").click();
     }
   }
document.onkeypress =  zx;
</script>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
</head>
<body>
<a href='' id='mytest1'></a>
<?php
echo '<h2>'.$tit.$tit2.'</H2>';
echo '<BR>';
$rr=array();
$host=parse_url($link)['host'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/7";
  $ref=$link."/watch";
  //$ref="https://moviegaga.to/movie/last-man-standing-season-8-jy913z32/watch";
  $l="https://".$host."/live";
  $post="active=true";
  $head=array('Accept: text/html, */*; q=0.01',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'X-Requested-With: XMLHttpRequest',
   'Content-Length: 11',
   'Origin: https://'.$host.'',
   'Connection: keep-alive',
   'Referer: '.$ref.'');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
//echo $h;
  if ($tip=="movie") $ep="1";
  $pat="/data-epi\=\"(".$ep.")\".*?data-target-i\=\"(\w+)\".*?data-target-e\=\"(\w+)\"/ms";
  preg_match_all($pat,$h,$m);
  $t1=explode('cf.k =',$h);
  $t2=explode(';',$t1[1]);
  $js=trim($t2[0]);
  //file_put_contents("1.txt",$js);
  $jj=unfuck($js);
  $t1=explode('csdiff',$h);
  $t2=explode(';',$t1[1]);
  $code="\$timestamp".str_replace('new Date().getTime()/1000','time()',$t2[0]).";";
  eval ($code);
  $csdiff = $timestamp;
  $timestamp=floor(time() - $csdiff);
  for ($k=0;$k<count($m[0]);$k++) {
   $data_e=$m[3][$k];
   $data_i=$m[2][$k];
   //$timestamp=floor(time() - $csdiff);
   $token=md5($jj.$data_e.$data_i.$timestamp);
   $l="https://moviegaga.to/api/get_episode/".$data_i."/".$data_e;
   $head=array('Accept: application/json, text/javascript, */*; q=0.01',
    'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
    'Accept-Encoding: deflate',
    'X-Requested-With: XMLHttpRequest',
    'Connection: keep-alive',
    'Cookie: timestamp='.$timestamp.'; token='.$token.'',
    'Referer: '.$ref.'');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL,$l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt ($ch, CURLOPT_POST, 0);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close ($ch);
   $r=json_decode($h,1);
   $l = $r['data']['sources']['RAW'];
   if (strpos($l,"http") === false) $l="https:".$l;
   //echo $l."\n";
   if ($l) $rr[]=$l;
}
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.parse_url($rr[0])['host'].'</label>
<input type="hidden" id="file" value="'.urlencode($rr[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($rr);
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link=$rr[$i];
  $openload=parse_url($rr[$i])['host'];
  if (preg_match($indirect,$openload)) {
  echo '<TD class="mp"><a href="filme_link.php?file='.urlencode($c_link).'&title='.urlencode(unfix_t($tit.$tit2)).'" target="_blank">'.$openload.'</a></td>';
  } else
  echo '<TD class="mp"><a id="myLink" href="#" onclick="changeserver('."'".$openload."','".urlencode($c_link)."'".');return false;">'.$openload.'</a></td>';
  $x++;
  if ($x==6) {
    echo '</TR>';
    $x=0;
  }
}
if ($x < 6 && $x > 0 & $k>6) {
 for ($k=0;$k<6-$x;$k++) {
   echo '<TD></TD>'."\r\n";
 }
 echo '</TR>'."\r\n";
}
echo '</TABLE>';
if ($tip=="movie") {
  $tit3=$tit;
  $tit2="";
  $sez="";
  $ep="";
  $imdbid="";
  $from="";
  $link_page="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  $imdbid="";
  $from="";
  $link_page="";
}
  $rest = substr($tit3, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $tit3=trim(str_replace($m[0],"",$tit3));
  }
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
echo '<br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="4">Alegeti o subtitrare</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="4">Alegeti o subtitrare (cauta imdb id)</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub1" href="opensubtitles1.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="titrari1" href="titrari_main1.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs1" href="subs_main1.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari1" href="subtitrari_main1.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($tit.$tit2));
 if ($flash != "mp")
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink1('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
 else
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
echo '</tr>';
echo '</table>';
echo '<br>
<table border="0px" width="100%">
<TR>
<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5=vizioneaza
<BR>Scurtaturi: 7=opensubtitles, 8=titrari, 9=subs, 0=subtitrari (cauta imdb id)
</b></font></TD></TR></TABLE>
';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
