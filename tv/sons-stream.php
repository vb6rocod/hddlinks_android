<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");

$width="200px";
$height=intval(200*(228/380))."px";
$page_title="sons-stream";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>sons-stream</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
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
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
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
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
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

$n=0;
echo '<h2>'.$page_title.'</H2>';
//echo '<table border="1px" width="100%">'."\n\r";
echo "<table class='event-table'><thead><tr><th>Hours</th><th>Logo</th><th>Event</th><th>link</th></tr></thead>";
$l="https://sons-stream.com/";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://sons-stream.com/',
'Origin: https://sons-stream.com'
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
$videos=explode("<th colspan='6'>",$h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('<',$video);
 $day=$t1[0];
 echo "<th colspan='4'>".$day.'</th>';
 $vids=explode('<tr class="cell-color"',$video);
 unset($vids[0]);
 $vids = array_values($vids);
 foreach($vids as $vid) {
  $t1=explode('class="cell-color">',$vid);
  $t2=explode("<",$t1[1]);
  $hour=$t2[0];
   preg_match("/(\d+):(\d+)/",$hour,$mm);
   $hour=sprintf("%02d:%02d",(($mm[1]+1)%24),$mm[2]);
  $t1=explode("src='",$vid);
  $t2=explode("'",$t1[1]);
  $img=$t2[0];
  $t1=explode('class="event-title cell-color">',$vid);
  $t2=explode("<",$t1[1]);
  $event=trim($t2[0]);
  preg_match_all("/data-stream\=\'(\d+)\'/",$vid,$m);
  echo "<tr class='cell-color'>";
  echo "<td class='cell-color'>".$hour."</td><td class='cell-color'><img src='".$img."' width='50' height='45' /></td>";
  echo "<td class='event-title cell-color'>".$event."</td>";
  echo "<td>";
  for ($k=0;$k<count($m[1]);$k++){
   $link="https://sons-stream.com/tvon.php?hd=".$m[1][$k];
   $link1="direct_link.php?link=".$link."&title=".urlencode($event)."&from=fara&mod=direct";
   $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($event))."&from=fara&mod=direct";
   if ($flash == "flash")
   echo '<a href="'.$link1.'" target="_blank">'.$m[1][$k].'</a>';
   else
   echo '<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$m[1][$k].'</a>';
   echo ' ';
   }
   echo '</TD></TR>';
  }
 }
////////////////////////////////////

echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
