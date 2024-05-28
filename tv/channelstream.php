<!DOCTYPE html>
<?php
error_reporting(0);

$pg_tit="channelstream";
?>
<html>
<head>
<meta charset="utf-8">
<title>channelstream</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
td.link {
  color:black;
  background-color:#0a6996;
  color:#64c8ff;
  text-align:center;
  font-style: bold;
  font-size: 18px;
}
</style>
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

</head>
<body>

<h2><?php echo $pg_tit; ?></H2>


<?php

include ("../common.php");

$r=array();
$l="https://channelstream.es/programme.php";
//$l="https://bundesliga-live.info/";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0',
'Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://channelstream.es/',
'Origin: https://channelstream.es'
);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  
  $r=array();
  $sPattern = "/colspan=\"7\".+?<b>([^<]+)<\/b>.+?location\.href = '([^']+).+?text-align.+?\>(.+?)\<\/td\>.+?<span class=\"flag ([^\"]+).+?text-align.+?\>([^<]+).+?text-align: left.+?\>([^<]+).+?<span class=\"t\">([^<]+)<\/span>/si";
  preg_match_all($sPattern,$h,$m);
  //print_r ($m);
  for ($k=0;$k<count($m[1]);$k++) {
   preg_match("/(\d+)-(\d+)-(\d+)/",$m[3][$k],$n);
   $day=$n[3]."-".$n[2]."-".$n[1];
   $ora=$m[7][$k];
   preg_match("/(\d+):(\d+)/",$ora,$mm);
   $ora=sprintf("%02d:%02d",(($mm[1]+8)%24),$mm[2]);
   $file="https://channelstream.es".$m[2][$k];
   $tit1=trim($m[5][$k]);
   $tit2=trim($m[6][$k]);
   if ($tit2)
     $title=$tit1." - ".$tit2;
   else
     $title=$tit1;
   $sport=$m[1][$k];
   $r[$day][]=array($ora,$file,$title,$sport);
  }
  //print_r ($r);
echo "<table class='event-table'>";
foreach ($r as $key=>$value) {
 echo "<th colspan='3'>".$key.'</th>';
 for ($z=0;$z<count($value);$z++) {
  echo "<tr class='cell-color'>";
  echo "<td class='cell-color'>".$value[$z][0]."</td><td class='cell-color'>".$value[$z][3]."</td>";
  $event=$value[$z][2];
  $l="bundesliga-live_fs.php?page=1&link=".urlencode($value[$z][1])."&title=".urlencode($event);
  echo "<td class='event-title cell-color'>".'<a href="'.$l.'" target="_blank">'.$event."</a></td>";
  echo "<td>";
  echo '</tr>';
 }
}
echo '</table>';
?>

</body>
</html>
