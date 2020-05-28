<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
$amigo=$base_pass."tvplay1.txt";
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
//$link_fs='cineplex_fs.php?tip=movie&imdb='.$imdb.'&title='.urlencode(fix_t($title1)).'&image='.$image."&year=".$year;

$user="";
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["imdb"];
$tip=$_GET["tip"];
$year=$_GET["year"];
$token=$_GET["token"];
if ($tip=="movie") {
   if (!$year)
     $tit2=$tit;
   else
     $tit2=$tit." (".$year.")";
   $id=str_between($link,"/movies/","-");
} else {
   $id=str_between($link,"series/","-");
   $sez=$_GET["sez"];
   $ep=$_GET["ep"];
   $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
   $tit2=$tit." - ".$sez."x".$ep." - ".$ep_tit;
}
$cookie=$base_cookie."cineplex.dat";
if (file_exists($base_pass."cineplex_host.txt"))
  $host=file_get_contents($base_pass."cineplex_host.txt");
else
  $host="cinogen.net";
if ($tip=="movie")
$l="https://".$host."/movies/getMovieLink?id=".$id."&token=".$token."&oPid=&_=";
else
$l="https://".$host."/series/getTvLink?id=".$id."&token=".$token."&s=".$sez."&e=".$ep."&oPid=&_=";
$rh="https://".$host;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,$rh);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($html,1);
  if (strpos($html,"&end=") !== false) $user="free";
  //print_r ($r);
  //die();
?>
<html>



   <head>

      <meta charset="utf-8">
      <title><?php echo $tit2; ?></title>
	  <link rel="stylesheet" type="text/css" href="../custom.css" />
     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<style>
input[type=radio] + label {
  color: #ccc;
  font-style: normal;
}
input[type=radio]:checked + label {
  color: #f00;
  font-style: normal;
}
input[type=radio]:focus + label {
  outline: 2px solid yellow;
}
</style>
<script type="text/javascript">
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}
function openlink(title) {
  link="";
  link = document.querySelector('input[name="svr"]:checked').value;
  msg="cineplex_fs_link.php?serv=" + link + "&" + title;
  window.open(msg);
}
function openlink1(title) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  document.getElementById("wait").innerHTML = '<font size="4" color="#ebf442"><b>ASTEPTATI...............</b></font>';

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  link="";
  link = document.querySelector('input[name="svr"]:checked').value;
  var the_data = "serv="+ link +"&"+title;
  var php_file="cineplex_fs_link.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       //alert (request.responseText);
       document.getElementById("wait").innerHTML = '';
       document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}

function changeserver(s) {
  document.getElementById('server').innerHTML = s;
  //alert (document.getElementById('server').innerHTML);
  //history.back();
}
</script>
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13" && e.target.type == "radio") {
      document.getElementById(e.target.id).click();
      //alert (e.target.id);
    } else if (charCode == "49") {
      document.getElementById("opensub").click();
     } else if (charCode == "50") {
      document.getElementById("titrari").click();
     } else if (charCode == "51") {
      document.getElementById("subs").click();
     } else if (charCode == "52") {
      document.getElementById("subtitrari").click();
     } else if (charCode == "53") {
      document.getElementById("v0").click();
     } else if (charCode == "54") {
      document.getElementById("v1").click();
     }
   }
document.onkeypress =  zx;
</script>
  </head>
   <body><div id="mainnav">
  <a href='' id='mytest1'></a>
<?php
if ($tip=="movie") {
$arr=$r["jwplayer"];

//print_r ($arr);

} else {
$arr=$r["jwplayer"];
}
$f=$arr[0]['file'];
if (preg_match("/tt(\d{6,})/",$f,$m)) {
  $imdbid=$m[1];
} else {
  $imdbid="";
}
//print_r ($arr);
//echo '<h2 style="background-color:deepskyblue;color:black;">'.$tit.' '.$tit2.'</H2>';
echo '<table border="0px" width="100%"><TR>'."\n\r";
echo '<TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px"><font size="6px" color="#64c8ff"><b>'.$tit2.'</b></font></td>';
echo '</TR></TABLE><BR>';
echo '<table border="1" width="100%">';
$p=0;
if ($tip=="movie") {
  $tit3=$tit;
  $sez="";
  $ep="";
  //$imdbid="";
  $from="cineplex";
  $link_page="";
  $ep_tit="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  //$imdbid="";
  $from="cineplex";
  $link_page="";
}
if ($tip=="movie")
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit))."&link=".$link_page."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year;
else
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit))."&link=".$link_page."&ep_tit=".urlencode(fix_t($sez."x".$ep." - ".$ep_tit))."&year=".$year;
echo '</table><br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;text-align:center;vertical-align:middle;height:15px" colspan="4"><font size="4"><b>Alegeti o subtitrare</b></font></td></TR>';
echo '<TR>';
echo '<TD align="center"><font size="4"><b><a id="opensub" href="opensubtitles.php?'.$sub_link.'">opensubtitles</a></b></font></td>';
echo '<TD align="center"><font size="4"><b><a id="titrari" href="titrari_main.php?page=1&'.$sub_link.'">titrari.ro</a></b></font></td>';
echo '<TD align="center"><font size="4"><b><a id="subs" href="subs_main.php?'.$sub_link.'">subs.ro</a></b></font></td>';
echo '<TD align="center"><font size="4"><b><a id="subtitrari" href="subtitrari_main.php?'.$sub_link.'">subtitrari_noi.ro</a></b></font></td>';
echo '</TR><TR></TABLE>';
if ($tip=="movie") {
$openlink1="tip=movie&imdb=".$id."&title=".urlencode(fix_t($tit))."&image=".$image."&token=".$token."&year=".$year;
} else {
$openlink1="tip=series&imdb=".$id."&title=".urlencode(fix_t($tit))."&image=".$image."&token=".$token."&sez=".$sez."&ep=".$ep."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year;
}
if ($user=="free" && file_exists($amigo)) {
echo '<table border="1" width="100%">';
echo '<TR><TD align="center" colspan="'.(count($arr)*2).'"><font size="4"><b>Vizionati !</b></font></td></TR>';
echo '<TR>';

foreach ($arr as $key => $value) {
  //print_r ($value);
  //echo $key;
  if ($flash != "mp") {
  $openload=$arr[$key]["label"].'|Part1';
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a href="#" onclick="openlink('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  $openload=$arr[$key]["label"].'|Part2';
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a href="#" onclick="openlink('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  } else {
  $openload=$arr[$key]["label"].'|Part1';
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a href="#" onclick="openlink1('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  $openload=$arr[$key]["label"].'|Part2';
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a href="#" onclick="openlink1('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  }
}

} else {
echo '<table border="1" width="100%">';
echo '<TR><TD align="center" colspan="'.(count($arr)*2).'"><font size="4"><b>Vizionati !</b></font></td></TR>';
echo '<TR>';
$w=0;
foreach ($arr as $key => $value) {
  //print_r ($value);
  if ($flash != "mp") {
  $openload=$arr[$key]["label"];
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a id="v'.$w.'" href="#" onclick="openlink('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  } else {
  $openload=$arr[$key]["label"];
  $openlink=$openlink1."&q=".$openload;
  echo '<TD align="center"><font size="4"><b><a id="v'.$w.'" href="#" onclick="openlink1('."'".$openlink."'".');return false;">'.$openload.'</a></b></font></td>';
  }
  $w++;
}
}

echo '</tr>';
echo '</table>';
$movie=$arr[0]["file"];
if (preg_match("/trial(\d+)/",$movie,$m)) {
 $serv = $m[1];
 echo '<hr><table border="1" width="100%">';
 echo '<TR><TD class="cat">Alegeti alt server:</TD>';
 echo '<TD class="cat">';
 for ($k=1;$k<8;$k++) {
  if ($k==hexdec($serv))
   echo '<input type="radio" name="svr" id="svr'.$k.'" value="'.$k.'" checked><label for="svr'.$k.'">Server: '.$k.'</label>';
  else
   echo '<input type="radio" name="svr" id="svr'.$k.'" value="'.$k.'"><label for="svr'.$k.'">Server: '.$k.'</label>';
 }
 echo '</TD></TR></TABLE>';
} else {
$arr=$r["server"]["list"];
$serv=$r["server"]["selected"];
 echo '<hr><table border="1" width="100%">';
 echo '<TR><TD class="cat">Alegeti alt server:</TD>';
 echo '<TD class="cat">';
foreach ($arr as $key => $value) {
  //print_r ($value);
  //echo $key;
  if ($key==hexdec($serv))
   echo '<br><input type="radio" name="svr" id="svr'.$key.'" value="'.$value.'" checked><label for="svr'.$key.'">Server: '.$value.'</label>';
  else
   echo '<br><input type="radio" name="svr" id="svr'.$key.'" value="'.$value.'"><label for="svr'.$key.'">Server: '.$value.'</label>';
}
}
echo '<BR><table border="0px" width="100%"><TR>'."\n\r";
echo '<TD><font size="4"><b>Scurtaturi: 1=opensubtitles, 2=titrari, 3=subs, 4=subtitrari, 5,6=vizionare</b></font></TD></TR>';
echo '<TR><TD align="center"><label id="wait"></label></TR></TABLE>';
echo '<br></div></body>
</html>';
