<!DOCTYPE html>
<?php
error_reporting(0);

$pg_tit="livematch";
?>
<html>
<head>
<meta charset="utf-8">
<title>livematch</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  //var request =  new XMLHttpRequest();

  var the_data = link;

  on();
  $.post("direct_link.php",link,function(s) {
      off();
      ///document.getElementById("text").innerHTML="Wait...";
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
<h2><?php echo $pg_tit; ?></H2>

<table border="1px" width="100%">
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

$n=0;
$w=0;
$r=array();
$l="https://livematch.ge/";
//https://dlhd.sx/24-7-channels.php
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://livematch.ge/',
'Origin: https://livematch.ge'
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
  $videos=explode('<a href="channels',$h);
$videos = array_values($videos);
foreach($videos as $video) {
 $t2=explode('"',$video);
 $l="https://livematch.ge/channels".$t2[0];
 $t1=explode('<p>',$video);
 $t2=explode('</',$t1[1]);
 $title=$t2[0];
 if (!preg_match("/DOCTYPE/",$l)) $r[]=array($l,$title);
}
//print_r ($r);
//print_r ($m3uFile);
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
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.'</a>';
    else
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a>';
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
