<!DOCTYPE html>
<?php
//error_reporting(0);

$pg_tit="sportsonline";
?>
<html>
<head>
<meta charset="utf-8">
<title>sportsonline</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
    <style>
        body {
            background-color: #272B39;
            color: white;
            font-family: "sans-serif", monospace;
            font-size: 18px;
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
$l="https://sportsonline.so/247.txt";
//$l="https://sportsonline.gl/prog.txt";
$l="https://v2.sportsonline.si/247.txt";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://sportsonline.so/',
'Origin: https://sportsonline.so'
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
  $videos=explode("\n",$h);
  
  //unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    if (preg_match("/https/",$video)) {
    $t1=explode("-",$video);
    $r[]=array(trim($t1[0]),trim($t1[1]));
    }
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

echo '<h2>CHANNEL TV 24/7</H2>';
echo '<table border="1px" width="100%">';
for ($z=0;$z<count($r);$z++) {
    $title=trim($r[$z][0]);
    $file = trim($r[$z][1]);

    $mod="direct";
    $from="fara";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash")
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.'</a>';
    else
    echo '<TD class="mp" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a>';
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
 //<div class="channels">
$r=array();
$link="https://sportsonline.gl/prog.txt";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://primasport.one/',
  'Origin: https://primasport.one'
  );
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //$h=preg_replace("/\n/","",$h);
  //echo $h;
$ev=array();
$evv=array();
//$h=str_replace("Sivasspor","Sivasspor Friday",$h);
preg_match_all("/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s*\n/si",$h,$d);
$m=preg_split("/(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\s*\n/i",$h);
//print_r ($d);
//print_r ($m);
for ($z=0;$z<count($d[0]);$z++) {
 $e=array();
  if (preg_match_all("/(\d{1,2}\:\d{2})\s+(.*?)\|?\s*\|?\s*(http.+)/",$m[$z+1],$e)) {
    for ($y=0;$y<count($e[0]);$y++) {
      $ev[$d[0][$z]][]=array($e[1][$y],trim($e[2][$y]),trim($e[3][$y]));
      $evv[$d[0][$z]][trim($e[2][$y])][]=array($e[1][$y], trim($e[3][$y]));
    }
  }
 }
//print_r ($evv);
echo '<h2>Sports Event</H2>';
/////////////////////////////////////////////////////
$n=0;
foreach ($evv as $key=>$links) {
//echo $key." ";
echo '<h3 style="text-align:center;background-color:DodgerBlue;">'.$key.'</h3>';
echo "<table class='event-table'>";
echo "<thead><tr><th>Hours</th><th>Event</th><th>link</th></tr></thead>";
  foreach ($links as $keys=>$value) {
   $ora=$value[0][0];
    preg_match("/(\d+):(\d+)/",$ora,$m);
    $ora=sprintf("%02d:%02d",(($m[1]+2)%24),$m[2]);
   $title=$keys;
   echo "<tr class='cell-color'>";
   echo '<TD>'.$ora.'</TD>';
   echo '<TD>'.$title.'</TD>';
   echo '<TD>';
   //print_r ($value);
   for ($p=0;$p<count($value);$p++) {
   $file=$value[$p][1];
   $title=$keys;
    $mod="direct";
    $from="fara";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;

    if ($flash == "flash")
    echo '<a id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.($p+1).
    '</a> ';
    else
    echo '<a id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".($p+1).
    '</a> ';
    $n++;
    $w++;


  }
  echo '</TD>';
  echo '</TR>';
  }
  echo '</TABLE>';
}
   
/////////////////////////////////////////////////////

?>

<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
