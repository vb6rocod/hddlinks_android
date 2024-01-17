<!DOCTYPE html>
<?php
error_reporting(0);

$pg_tit="sportybite";
?>
<html>
<head>
<meta charset="utf-8">
<title>sportybite</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  var the_data = link;
  on();
  $.post("direct_link.php",link,function(s) {
      off();
      document.getElementById("mytest1").href=s;
      document.getElementById("mytest1").click();
  }
  );
}


   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     }
   }

document.onkeypress =  zx;
</script>
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
<a href='' id='mytest1'></a>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>

<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
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
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
$n=0;
$w=0;
$r=array();
$l="https://sportybite.top/";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://sportybite.top/',
'Origin: https://sportybite.top'
);
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
  //echo $h;
  //$h=file_get_contents("C:/EasyPhp/localweb/mobile1/nou/sport.htm");

 //<div class="channels">
$r=array();
  $videos=explode('<div class="channels">',$h);
  unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 $l=$t2[0];
 $t3=explode('>',$t1[1]);
 $t4=explode('<',$t3[1]);
 $title=trim($t4[0]);
 if ($title)
  $r[]=array($l,$title);
}

echo '<h2>CHANNEL TV 24/7</H2>';
$n=0;

echo '<table border="1px" width="100%">';
for ($z=0;$z<count($r);$z++) {
    $title=trim($r[$z][1]);
    $file = trim($r[$z][0]);

    $mod="direct";
    $from="fara";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash")
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.
    '</a>';
    else
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.
    '</a>';
    $n++;
    $w++;

    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }

   //}
  //}
}
 echo '</table>';
//////////////////////
$r=array();
$n=0;
  $t1=explode('<script type="application/ld+json">',$h);
  $t2=explode('</script>',$t1[1]);
  $x=trim($t2[0]);
  //echo $x;
  $videos=explode('@context',$x);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    preg_match("/name\"\:\s*\"([^\"]+)\"/",$video,$m1);
    preg_match("/url event\"\:\s*\"([^\"]+)\"/",$video,$m2);
    preg_match("/startDate\"\:\s*\"([^\"]+)\"/",$video,$m3);
    preg_match("/sport\"\:\s*\"([^\"]+)\"/",$video,$m4);
    preg_match("/league\"\:\s*\"([^\"]+)\"/",$video,$m5);
    print_r ($m);
    $r[]=array($m1[1],$m2[1],$m3[1],$m4[1],$m5[1]);
  }
  /*
  die();
  echo $x."\n";
  $y=json_decode($x,1);
  print_r ($y);
  //die();
  for ($k=0;$k<count($y);$k++) {
    $r[]=array($y[$k]['name'],$y[$k]['location']['url event'],$y[$k]['startDate'],$y[$k]['location']['sport'],$y[$k]['location']['league']);
  }
  */
$n=0;
echo '<h2>Sports Event</H2>';
echo '<table border="1px" width="100%">';
for ($z=0;$z<count($r);$z++) {
    $title=trim($r[$z][0]);
    $file = trim($r[$z][1]);
    $time=$r[$z][2];
    //echo $time;
    //2023-09-17T15:00:00
    preg_match("/(\d+)\-(\d+)\-(\d+)T(\d+)\:(\d+)\:(\d+)/",trim($time),$m);
    //print_r ($m);
    $time=mktime($m[4]+1,$m[5],$m[6],$m[2],$m[3],$m[1]);
    $time=date("d-m-Y H:i",$time);
    $sport=$r[$z][3]." - ".$r[$z][4];
    $mod="direct";
    $from="fara";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash")
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.
    '<BR>'.$sport."<BR>".$time.'</a>';
    else
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.
    '<BR>'.$sport."<BR>".$time.'</a>';
    $n++;
    $w++;

    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }

   //}
  //}
}

 echo '</table>';
?>

<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
