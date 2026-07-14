<!DOCTYPE html>
<?php
include ("../common.php");
$file=$base_pass."weather.txt";
if (file_exists($file))
 $id=trim(file_get_contents($file));
else
 $id="ROXX0023";
$l="https://weather.codes/romania/";
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $find="/medium gap-1\"\>([^\<]+)\<\/dt\>\<dd class\=font-mono\>".$id."/";
  //echo $find;
  preg_match($find,$h,$m);
  $loc=$m[1];
//$r="%7B%22typeLocation%22%3A%22specificLocation%22%2C%22location%22%3A%22Bac%5Cu0103u%22%2C%22customLocationName%22%3A%22%22%2C%22lang%22%3A%22ro%22%2C%22units%22%3A%22metric%22%2C%22windUnits%22%3A%22kilometres%22%2C%22pressureUnits%22%3A%22imperial%22%2C%22timeFormat%22%3A%2224%22%2C%22layout%22%3A%22detailedForecast%22%2C%22backgroundType%22%3A%22image%22%2C%22backgroundColor%22%3A%22rgb%2877%2C%2052%2C%20243%29%22%2C%22dailyForecastBackgroundColor%22%3A%22rgb%28250%2C%20250%2C%20250%29%22%2C%22currentTemperatureSize%22%3A24%2C%22currentWeatherSize%22%3A24%2C%22iconsAnimation%22%3Atrue%2C%22showUnits%22%3Atrue%2C%22showAllDays%22%3Atrue%2C%22widgetId%22%3A%2216%22%7D";
//echo urldecode($r);
//$u=json_decode(urldecode($r),1);
//print_r  ($u);
//$loc="Bacau";
$w=array(
    "typeLocation" => "specificLocation",
    "location" => "Bacãu",
    "customLocationName" =>"",
    "lang" => "ro",
    "units" => "metric",
    "windUnits" => "kilometres",
    "pressureUnits" => "imperial",
    "timeFormat" => 24,
    "layout" => "detailedForecast",
    "backgroundType" => "image",
    "backgroundColor" => "rgb(77, 52, 243)",
    "dailyForecastBackgroundColor" => "rgb(250, 250, 250)",
    "currentTemperatureSize" => 24,
    "currentWeatherSize" => 24,
    "iconsAnimation" => 1,
    "showUnits" => 1,
    "showAllDays" => 1,
    "widgetId" => 16
);

//    "location" => ".$loc.",
$w['location']=$loc;

//json_encode(
$z=urlencode(json_encode($w));
//echo $z;
echo '
<HTML>

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Vremea....</title>
<script type="text/javascript" src="https://vremea.org/wp-content/plugins/elfsight-weather-cc/assets/elfsight-weather.js?ver=1.3.1" id="elfsight-weather-js"></script>

<style>
.eaw-weather-component {
    max-width: 100%;
}
.eaw-weather-info {
    border-radius: 0px;
}
.eaw-daily-item-component {
    background-color: rgb(255 255 255);
	border-bottom: 1px solid #e5e5e5;
	padding: 7px 24px;
}
#eapps-weather-2 > div > div > div:nth-child(2) > div:nth-child(3) {
	width: 0px !important;
    right: 0px !important;
    bottom: 0px !important;
    top: 0px !important;
    border-radius: 0px !important;
}
@media screen and (min-width: 760px) {
    .eaw-forecast-item-icon {
    width: 45px !important;
    height: 37px !important;
    margin-top: 0px !important;
 }
}
</style>
</head>
<body>
            <div 
                class="elfsight-widget-weather elfsight-widget" 
                data-elfsight-weather-options="'.$z.'"
                data-elfsight-weather-version="1.3.1"
                data-elfsight-widget-id="elfsight-weather-16">
            </div>
</body>
</html>';
?>
