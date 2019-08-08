<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title>Vremea...</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />

<BODY>
<table border="0" align="center" width="100%">
<?php
error_reporting(0);
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$s="https://weather.codes/romania/";
$file=$base_pass."weather.txt";
if (file_exists($file))
 $id=trim(file_get_contents($file));
else
 $id="ROXX0023";
//$id="156926c9261b9d02648fb8765d2aa07ce315a418227f750a9ad268f0bdf43b4d";
//$id="86e0c0ca28489cd9bbf9345dccc99ca2b6439a89b4ecd0ed542017661844d60e";
$l="https://weather.com/ro-RO/vreme/astazi/l/".$id;
$ua="Mozilla/5.0 (iPhone; CPU iPhone OS 5_0_1 like Mac OS X)";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //curl_setopt($ch, CURLOPT_REFERER, "http://hqq.tv/");
      $h = curl_exec($ch);
      curl_close($ch);
//echo $h;
$t1=explode('window.env =',$h);
$t1=explode('window.__data=',$h);
$t2=explode(';window.experience',$t1[1]);
$h1=$t2[0];
//echo $h1;
$r=json_decode($h1,1);
//print_r ($r);
//$alert=$r["ads"]["adaptorParams"]["alerts"]["data"]["vt1alerts"][0]["headline"];
$d=$r["dal"]["Location"];
$key = key($d);
$loc=" ".$d[$key]["data"]["location"]["displayName"];
echo '<TR><TD colspan="2"><h2>'.$loc.'</h2></TD></TR>';
//$d=$r["dal"]["DailyForecast"];
//$key = key($d);
$obs = $r["dal"]["Observation"];
$key = key($obs);
$acum = $obs[$key]["data"]["vt1observation"];
$time=$acum["observationTime"];
$d=$r["dal"]["HourlyForecast"];
$key = key($d);
$next=$d[$key]["data"]["vt1hourlyForecast"];
//echo $time;
//2018-06-13T10:54:42+0300
preg_match("/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)/",$time,$m);
$acum_text = 'Actualizat la ora: '.$m[4].":".$m[5]."<BR>";
$acum_text .= "Temperatura: ".$acum["temperature"]."&deg;C ,".$acum["phrase"].", (Temp. resimtita: ".$acum["feelsLike"]."&deg;C)"."<BR>";
$acum_text .= "Presiunea: ".$acum["altimeter"]."mb ,".$acum["barometerTrend"]."<BR>";
$acum_text .= "Vant: ".$acum["windDirCompass"]." ".$acum["windSpeed"]." km/h"."<BR>";
$acum_text .= "Umiditate: ".$acum["humidity"]."%"."<BR>";
$acum_text .= "Indice UV: ".$acum["uvIndex"].", ".$acum["uvDescription"]."<BR>"."<BR>";
$acum_text .= "Evolutia in urmatoarele ore"."<BR>";
for ($k=0;$k<12;$k++) {
 $ora=$next[$k]["processTime"];
 $temp=$next[$k]["temperature"];
 $desc=$next[$k]["phrase"];
 preg_match("/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)/",$ora,$m);
 $acum_text .= 'Ora: '.$m[4].":".$m[5]." Temp. ".$temp."&deg;C ,".$desc."<BR>";
}
//print $acum_text;
echo '<TR><TD class="cat">'.$acum_text.'</TD>';
//////////////////////////////////////////////////////////
$prog_text="";
$d=$r["dal"]["DailyForecast"];
//print_r ($d);
$key = key($d);
$prog=$d[$key]["data"]["vt1dailyForecast"];
$ras=$prog[0]["sunrise"];
preg_match("/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)/",$ras,$m);

$ap=$prog[0]["sunset"];
preg_match("/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)/",$ap,$n);
$soare="Rasarit: ".$m[4].":".$m[5]." ,Apus: ".$n[4].":".$n[5];
$luna="Luna: ".$prog[0]["moonPhrase"];
for ($k=0;$k<4;$k++) {
  $time=$prog[$k]["validDate"];
  preg_match("/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)/",$time,$m);
  $day=$prog[$k]["dayOfWeek"];
  $ziua=$prog[$k]["day"];
  $noaptea=$prog[$k]["night"];
  //$ziua1=$day["dayPartName"];
  $noaptea1=$noaptea["dayPartName"];
  $prog_text .= $day.", ".$m[3]."-".$m[2]."-".$m[1]."<BR>";
  $prog_text .= "Ziua: ".$ziua["narrative"]."<BR>";
  $prog_text .= "Noaptea: ".$noaptea["narrative"]."<BR>"."<BR>";
}
//echo $prog_text;
echo '<TD class="cat">'.$prog_text.'</TD></TR>';
?>
</TABLE>
</BODY>
</HTML>
