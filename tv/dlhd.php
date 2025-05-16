<!DOCTYPE html>
<?php
error_reporting(0);

$pg_tit="DaddyLiveHD";
?>
<html>
<head>
<meta charset="utf-8">
<title>DaddyLiveHD</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
    <style>
        body {
            background-color: #272B39;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .event-container {
            width: 100%;
        }


        .event-table {
            width: 100%;
            background-color: #22252a;
            border-collapse: collapse;
            border-spacing: 0;
            font-family: "Courier New", monospace;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .event-table th, .event-table td {
		padding: 5px;
		text-align: center;
		/* border: 1px solid #0000005c; */ /* Supprimer cette ligne pour retirer les traits verticaux */
		font-family: "sans-serif", monospace;
		font-size: 18px;
		color: burlywood;
}

        .event-table th {
            color: #0fe3d1;
        }

        .event-table tbody tr:hover {
            background-color: #666;
        }

		.event-table tbody tr:nth-child(odd) td {
    background-color: #00000042; /* Couleur pour les lignes impaires */
}

.event-table tbody tr:hover td {
    background-color: #666; /* Couleur au survol de la souris */
}

        .btn-link {
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border: 2px solid #ffcc00;
            border-radius: 5px;
            background-color: transparent;
            color: #ffcc00;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
            cursor: pointer;
        }

        .btn-link:hover {
            background-color: #ffcc00;
            color: #22252a;
            border-color: #22252a;
        }

        .btn-stream, .btn-copy {
            background-color: #277ea566;
            color: burlywood;
            border: none;
            padding: 4px 12px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        .btn-stream:hover, .btn-copy:hover {
            background-color: #277ea566;
            color: #ff0000;
        }

        .event-table .cell-color {
            background-color: #18222d;
        }

        .event-table .btn-cell-color {
            background-color: #18222d;
        }

        #video-container {
            width: 100%;
            margin-top: 20px;
        }

        #video-player-container {
            width: 900px;
            height: 800px;
            margin: 0 auto;
            border: 2px solid #ffcc00;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        #video-player-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        #iframe-code {
            width: calc(100% - 10px);
            margin-top: 10px;
        }

        #copy-button {
            display: block;
            margin-top: 10px;
        }

        #event-info {
            border: 2px solid #ffcc00;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            width: fit-content;
            margin: 0 auto;
			font-family: system-ui;
        }
    </style>
<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  //var request =  new XMLHttpRequest();

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
<h2><?php echo $pg_tit; ?> <a href="#all">See all 24/7 Channels</a></H2>

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
echo "<table class='event-table'>"."\r\n";
$l="https://dlhd.sx/";
$l="https://daddylive.mp/schedule/schedule-generated.json";
$l1="https://daddylive.mp/24-7-channels.php";
$l="https://daddylive.mp/schedule/schedule-generated.php";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://daddylive.mp/',
'Origin: https://daddylive.mp'
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
  $r=json_decode($h,1);
  //print_r ($r);
  //print_r ($r[key($r)]);
  $rr=$r[key($r)];
  //die();
  $zz=0;
  //$videos=explode('<h2 style="background-color:cyan">',$h);
  //unset($videos[0]);
//$videos = array_values($videos);
//preg_match_all("/\<h2 style\=\"background-color:cyan\"\>([^\<]+)\</",$h,$a);
//print_r ($a[1]);
echo '<TR><TD colspan="3"><b>Jump to:</b>';
foreach ($rr as $key=>$value) {
 echo '<a href="#'.$key.'">'.$key.'</a>,';
}
echo '</TD></TR>';
//$dd="UK GMT";
echo '<TR><TD colspan="3" style="background-color:cyan;color:red">'.key($r)."(+3)".'</TD></TR>';
foreach($rr as $key=>$value) {
 //$t1=explode('<',$video);
 //$sport=$t1[0];
 //echo $video."\n";

 //if (preg_match_all("/<hr>([^\<]+)\</",$video,$y)) {

 echo '<TR><TH colspan="3" style="background-color:cyan;color:red"><a id="'.$key.'"></a>'.$key.'</TH></TR>';
 //echo $sport."\n";
 echo "<tr><th>Hours</th><th>Event</th><th>link</th></tr>";
 for ($k=0;$k<count($value);$k++) {
 $ora=$value[$k]['time'];
 preg_match("/(\d+):(\d+)/",$ora,$m);
 $ora=sprintf("%02d:%02d",(($m[1]+3)%24),$m[2]);
 $event=trim($value[$k]['event']);
 echo  '<TR>'."\n";
 echo '<TD>'.$ora.'</TD>';
 echo '<TD>'.$event.'</TD>';
 //if (preg_match_all("/href\=\"([^\"]+)\" target\=\"_blank\" rel\=\"noopener\"\>([^\<']+)\</m",$t1[$k+1],$x)) {

 echo '<TD>';
 //print_r ($value);
 for ($z=0;$z<count($value[$k]['channels']);$z++) {

  $title="CH".trim($value[$k]['channels'][$z]['channel_id']);
  //$title=$m[0];
  //$file=fixurl($x[1][$z],$l1);
  //$title=$z+1;
  $file="https://dlhd.sx/stream/stream-".$value[$k]['channels'][$z]['channel_id'].".php";
  $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($event))."&from=".$from."&mod=direct";
  $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($event))."&from=".$from."&mod=".$mod;
    if ($flash == "flash")
    echo '<a href="'.$link.'" target="_blank">'.$title.'</a> ';
    else
    echo '<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a> ';

  //echo $x[2][$z]." ";
 }
 echo '</TD>';
 //print_r ($x);

 //}
 echo '</TR>';
 }
 //}
}
echo '</TABLE>';

///////// all channel
$l1="https://dlhd.sx/24-7-channels.php";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://dlhd.sx/',
'Origin: https://dlhd.sx'
);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $videos=explode('<div class="grid-item"',$h);
  unset($videos[0]);
$videos = array_values($videos);
$n=0;
$r=array();
foreach($videos as $video) {
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 $l2=fixurl($t2[0],$l1);
 $t3=explode('<strong>',$video);
 $t4=explode('<',$t3[1]);
 $title=$t4[0];
 $r[]=array($l2,$title);
}
echo '<h2>24/7 Channels</h2>';
echo '<a id="all"></a>';
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
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
