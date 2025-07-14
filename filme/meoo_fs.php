<!doctype html>
<?php
include ("../common.php");

error_reporting(0);
if (file_exists($base_pass."debug.txt"))
 $debug=true;
else
 $debug=false;

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

//echo $link;
$l="https://meoo.ro/?player_movie=".$link."&auto=true";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://meoo.ro/?player_movie=10248&auto=true');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/data\-load\-embed\=\"(\d+)/",$h,$m);
  $tmbd=$m[1];
  preg_match("/data\-load\-embed\=\"(tt\d+)/",$h,$m);
  $imdb=$m[1];
//////////////////////////////////////////////////////////////////
$ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
if ($tip=="movie")
$l="https://api.themoviedb.org/3/movie/".$tmbd."?api_key=".$api_key."&append_to_response=credits,external_ids";
else
$l="https://api.themoviedb.org/3/tv/".$tmbd."?api_key=".$api_key."&append_to_response=credits,external_ids";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($html,1);
  //print_r ($x);
  //die();
  $info="";
  $overview=$x['overview'];
  if (isset($x['first_air_date']))
   $release_date=$x['first_air_date'];
  else
   $release_date=$x['release_date'];
  preg_match("/\d{4}/",$release_date,$d);
  $release_date=$d[0];
  $vote=$x['vote_average'];
  //$vote=$x['popularity'];
  if (isset($x['runtime']))
    $duration=$x['runtime'];
  elseif (isset($x['episode_run_time'][0]))
    $duration=$x['episode_run_time'][0];
  else
    $duration="";
  $y=$x['credits']['cast'];
  $z=$x['credits']['crew'];
  //print_r ($z);
  $actors=array();
  $director=array();
  $producer=array();
  $writer=array();
  for ($k=0;$k<count($y);$k++) {
   $a=$y[$k]['known_for_department'];
   //echo $a;
   if ($a=="Acting") $actors[]=array($y[$k]['name'],$y[$k]['id']);
  }
  //print_r ($actors);
  for ($k=0;$k<count($z);$k++) {
    if (preg_match("/director/i",$z[$k]['job']))
      $director[]=array($z[$k]['name'],$z[$k]['id']);
    elseif (preg_match("/story|writer/i",$z[$k]['job']))
      $writer[]=array($z[$k]['name'],$z[$k]['id']);
    elseif (preg_match("/producer/i",$z[$k]['job']))
      $producer[]=array($z[$k]['name'],$z[$k]['id']);
   }
  $genres="";
  for ($k=0;$k<count($x['genres']);$k++) {
    $genres .=$x['genres'][$k]['name'].",";
  }
  $genres = substr($genres, 0, -1);
  $info .="<b>Release date:</b>".$release_date.".<b>Runtime:</b>".$duration." min.<b>TMDB</b>:".$vote.".".$genres.'.<BR>';
  if (count($director)>0) {
  $info .='<b><font color="yellow">Director:</font></b>';
  for ($k=0;$k<min(10,count($director));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$director[$k][1].'&title='.urlencode($director[$k][0]).'" target="_blank">'.$director[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($producer)>0) {
  $info .='<b><font color="yellow">Producer:</font></b>';
  for ($k=0;$k<min(5,count($producer));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$producer[$k][1].'&title='.urlencode($producer[$k][0]).'" target="_blank">'.$producer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  if (count($writer) > 0) {
  $info .='<b><font color="yellow">Writer:</font></b>';
  for ($k=0;$k<min(10,count($writer));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$writer[$k][1].'&title='.urlencode($writer[$k][0]).'" target="_blank">'.$writer[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  }
  $info .='<b><font color="yellow">Cast:</font></b>';
  for ($k=0;$k<min(15,count($actors));$k++) {
   $info .='<a href="moviewetrust_p.php?page=1&link='.$actors[$k][1].'&title='.urlencode($actors[$k][0]).'" target="_blank">'.$actors[$k][0]."</a>,";
  }
  $info = substr($info, 0, -1).".<BR>";
  $info .= '<b><font color="cyan">Overview:</font></b>'.$overview;
///////////////////////////////////////////////////////////////////
$r=array();
$s=array();
$l="https://multiembed.mov/?video_id=".$imdb;
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: https://multiembed.mov/');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  preg_match("/location:\s*(.*)/i",$h,$m);
  //print_r ($m);
  //echo $h;
  $l=trim($m[1]);
  //echo $l;
  $btn="ZEhKMVpTLVF0LVBTLVF0TmotUDFMUy1Rek5qSXRMLTAtVjNOLTAtVi1RTkRjNU5EWS1RTi0wYy01";
  $btn=base64_encode("dHJ1ZS-Qt-PS-QtNj-P1LS-QzNjItL-0-V3N-0-V-QNDc5NDY-QN-0c-5");
  //$btn=base64_encode("dHJ1ZS-Qt-PS-QtNj-P1LS-QzNjItL-0-V3N-0-V-QNDc5NDY-QN-0c-6");
  $post="button-click=".$btn."&button-referer=";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://streamingnow.mov',
'Connection: keep-alive',
'Referer: '.$l);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/load_sources\(\"([^\"]+)/",$h,$m);
  preg_match_all("/servers\[(\d+)\]\=\s*\"([^\"]+)/",$h,$n);
  //print_r ($m);
  //print_r ($n);
  $token=$m[1];
  $l1="https://streamingnow.mov/response.php";

  //$token="S0dPVFFEaUcxTUlmNXJMUFRGb0tTaXFUVStiSHNRbkRNcXF1OWtBaWljR0dnQ1JIeFdGbWcweTN5akc1RFdZOGZLY1c0ZjZYUElOV09YYzZEcHJWN2JWendOYWh2YXRDUDhRV1duQVRpMW14d0YyNFJSc1RkYzA0cUVYbitzQTZxZENMRDdXRnZ1a2RDVUdNRjBmbjNDeTY2UEdNNUtjMEs4SDI=";
  $post="token=".$token;
  //echo $post."\n";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://streamingnow.mov',
'Connection: keep-alive',
'Referer: '.$l);
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match_all("/data\-id\=\"([^\"]+)\"\s*data\-server\=\"([^\"]+)/",$h,$x);
  //print_r ($x);
  $l_captcha="https://streamingnow.mov/playvideo.php?video_id=".$x[1][0]."&server_id=".$x[2][0]."&token=".$token."&init=1";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Referer: '.$l);
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  for ($i=0;$i<count($x[2]);$i++) {
  $id=$x[1][$i];
  $s1=$x[2][$i];
  $l2="https://streamingnow.mov/playvideo.php?video_id=".$id."&server_id=".$s1."&token=".$token."&init=1";
  //echo $l2;

  curl_setopt($ch, CURLOPT_URL, $l2);
  $h = curl_exec($ch);

  //echo $h;
  if (preg_match("/frameborder\=\"0\" src\=\"/",$h)) {
  $t1=explode('frameborder="0" src="',$h);
  $t2=explode('"',$t1[1]);
  $l3=$t2[0];
  $r[]=$l3;
  if (preg_match("/vipstream\_vfx\.php\?s\=(\d+)/",$l3,$m)) {
  $s[]= "vip".$m[1];
  } else
  $s[]=parse_url($l3)['host'];
  }
  //echo $l3."\n";
  }
curl_close($ch);
//print_r ($r);
if (preg_match("/captcha\_id/",$h)) {
preg_match("/type=\"hidden\"\s*value\=\"([^\"]+)/",$h,$m);
$ch_id=$m[1];
preg_match_all("/captcha_answer\[\\]\"\s*value\=\"([^\"]+)/",$h,$n);
//print_r ($n);
preg_match("/id\=\"captcha-message\"\>([^\<]+)/",$h,$m);
$q=$m[1];
preg_match_all("/captcha\/testing\/[^\"]+/",$h,$o);
//print_r ($o);
echo '
<style>
label:before {
  content: url("https://cdn1.iconfinder.com/data/icons/windows8_icons_iconpharm/26/unchecked_checkbox.png");
  position: absolute;
  z-index: 100;
}
:checked+label:before {
  content: url("check_sign_icon.png");
}
input[type=checkbox] {
  display: none;
}
/*pure cosmetics:*/
img {
  width: 80px;
  height: 80px;
}
label {
  margin: 10px;
}
</style>
';
echo '<span>'.$q."</span><BR>";
echo '<form method="post" action="multiembed1.php?file='.urlencode($l2).'"><BR>';
echo '<input type="hidden" value="'.$ch_id.'" name="captcha_id">';

for ($i=0;$i<count($n[1]);$i++){
 echo '<input type="checkbox" id="ch_'.$i.'" name="captcha_answer[]" value="'.$n[1][$i].'">';
 echo '<label for="ch_'.$i.'">';
 echo '<img src="https://streamingnow.mov/'.$o[0][$i].'">';
 echo '</label>';
}

echo '<button type="submit">Verify</button>';
echo '</form>';
}
///////////////////////////////////////////////////////////////////
echo '<table border="1" width="100%">';
echo '<TR><TD class="mp">Alegeti un server: Server curent:<label id="server">'.$s[0].'</label>
<input type="hidden" id="file" value="'.urlencode($r[0]).'"></td></TR></TABLE>';
echo '<table border="1" width="100%"><TR>';
$k=count($r);
//echo $k;
$x=0;
for ($i=0;$i<$k;$i++) {
  if ($x==0) echo '<TR>';
  $c_link=$r[$i];
  $openload=$s[$i];
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
  //$imdbid="";
  $from="";
  $link_page="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  //$imdbid="";
  $from="";
  $link_page="";
}
  $rest = substr($tit3, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit3=trim(str_replace($m[0],"",$tit3));
  } else {
   $year="";
  }
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
include ("subs.php");
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($tit.$tit2));
 if ($flash == "flash")
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
echo $info;

include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';

