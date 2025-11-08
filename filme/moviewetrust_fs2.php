<!doctype html>
<?php
include ("../common.php");
//error_reporting(0);
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (file_exists("vidsrc.txt")) unlink ("vidsrc.txt");
if (file_exists($base_pass."debug.txt"))
 $debug=true;
else
 $debug=false;
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

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title><?php echo $tit.$tit2; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
function openlink1(link) {
  link1=document.getElementById('file').value;
  //alert (link1);
  if (link1.match(/streamembed|imwatchingmovies/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=flash";
  window.open(msg);
  } else {
  msg="link1.php?file=" + link1 + "&title=" + link;
  window.open(msg);
  }
}
function openlink(link) {
  link1=document.getElementById('file').value;
  if (link1.match(/streamembed|imwatchingmovies|streambucket/gi)) {
  msg="streamembed1.php?file=" + link1 + "&title=" + link + "&tip=mp";
  window.open(msg);
  } else {
  on();
  var request =  new XMLHttpRequest();
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
//////////////////////////////
 $DEFAULT_CIPHERS =array(
            "ECDHE+AESGCM",
            "ECDHE+CHACHA20",
            "DHE+AESGCM",
            "DHE+CHACHA20",
            "ECDH+AESGCM",
            "DH+AESGCM",
            "ECDH+AES",
            "DH+AES",
            "RSA+AESGCM",
            "RSA+AES",
            "!aNULL",
            "!eNULL",
            "!MD5",
            "!DSS",
            "!ECDHE+SHA",
            "!AES128-SHA",
            "!DHE"
        );
 if (defined('CURL_SSLVERSION_TLSv1_3'))
  $ssl_version=7;
 else
  $ssl_version=0;
///////////////////////////
$tmdb=$link;
$r=array();
$s=array();
$ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
///////////////////////////////////////////////////////
if ($tip=="movie")
$l="https://api.themoviedb.org/3/movie/".$link."?api_key=".$api_key."&append_to_response=credits,external_ids";
else
$l="https://api.themoviedb.org/3/tv/".$link."?api_key=".$api_key."&append_to_response=credits,external_ids";
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
  $imdb=$x['external_ids']['imdb_id'];
  //echo $imdb;
  //die();
$k=0;
  //echo $imdb;
  //die();
if ($tip=="movie")
  $l="https://vidsrc.net/embed/".$imdb."/";
else
  $l="https://vidsrc.net/embed/".$imdb."/".$sez."-".$ep."/";
  //echo $l;
  //die();
  //echo '<iframe src="'.$l.'" style="display:none;"></iframe>';
  //https://multiembed.mov/directstream.php?video_id=tt6208148
////////////////////////////////////////////////////////////
// vidsrc.me
$ua1 = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0";
  $head=array('User-Agent: '.$ua,
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc.net/',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('player_iframe" src="',$h);
  $t2=explode('"',$t1[1]);
  $l1=fixurl($t2[0],$l);

  $host="https://".parse_url($l1)['host'];
$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Alt-Used: cloudnestra.com',
'Connection: keep-alive',
'Referer: https://vidsrc.net/',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: iframe',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: cross-site');

  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    ),
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
  );
  $context  = stream_context_create($options);
  //$h = @file_get_contents($l1, false, $context);


  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

  //echo $h;
   $t1=explode("src: '",$h);
   $t2=explode("'",$t1[1]);
   $l2=fixurl($t2[0],$l1);


   //$l2="https://cloudnestra.com/prorcp/MzMzZjhlMTJlNjFkYTJmZTYyMDgxN2UzZTVlZWE4NGE6UXpjcmIzZFJjbnBxWWtwaVVFMHhibFUxTjA5dFZFRjZSbHAxY1VJeU9ISkRVU3RtYWs1UlpGRlVjRTFHVUVzM1ZXTkdkMUoxVTFoYVNHaGlhMWxTZWpGMVVVVmtVMGxvT0RGalZ6ZE5ia05RYXpKRVFqaEhZMDA0WkhrMlpTdHVZemw2WWxGQ09HVkxWbVZQUmtGS2NsQllRVkpCYjNWa1NFVXJReTlQUWtvclJ6ZEJVM0JNSzBGcVRYVk1jSGN3YVRSbWIwRlpSbVpVYlVnMVpHMUNSRUZ0V1ZwcFVuRjZlbTE1TlM5VlRrc3pabmxhTlRReVptZGlNRWg1UWxCTmNVWnZhMUJHZDIxUldtSnBUMmxyUTFkWVZVWXJXbkZ4UWtaUGJDOXljblpvSzNjclpHcG5TbFpoZVZnNVRITjNibXMzY2psRlJVZ3pLek5zZEhwWFNHZHJPSGhvWTJwSVZ6VnZTMmRoVkVWc2MxVmlNVk5ZUm1aMVZUbHVWR3hIT1RnME0yTk1XR1ZOU0V0Qk1uUkdOV1kyVW5kUWVsbDJhalV3WVhJeWNtaHVRbUpKUVdaUk4yUjBURzk1UzA5dlluaG1WM2hTY1UxWFVHNXRVSEFyVkhSalduWkZaM1prZDBwTFRuUlliekl3T0hWVFRYUjVUMUJKU1RKc0wySk1ZMU5EYWtaWFEyWXdhREJzVG5oS2JXVmtTMUJPTDFGMmJVZEpPRVpsVTFsV2RVVXZPR1phWWtOd01IRlNObU5QVDJ0UldreDNRamxOYUU5bFpIUkhWV1ZTZVhWNk1uVmFOVEY0UmtJeU1ERkJSMGxzUWpCRWRVWlRORVJCUlZWU1RpdEJkamR0T0habVNrSlNlR0l2TDNCNllpdENPVll4YmtWdWQyTk1RVkpGUldKYUwza3diazFZUkRocE0wcG1RbGx2ZDBzM05UTjZXbVZwYW1wTGMySkpZek0zUVV0SWFubFdTSFZUU25RMlNqVTNORFJyZURKa2RtOXJiRk5PY25Wa2JsZ3hNMnBaTVc5dVNVbDFkV2RoUjFGTWRWUXphVFpCVm05MlltZENZbkUwU3pCV2NubzBUM2xUWm05UFdHbHdVMDlFUjFKeVFuRm5SWFpsVVRGblUxWmFURWxSTUV4UmFEQTFLemgyWlRSQlZXbzJXa0Z1Y0dGYWF6bE1UbVV2YUc0M2RHVklSWFUzUlZGeWNGVnpWSFpzTlVJMFF5c3JNblJ3V25GeWFqVkVhelUwV25CbmEwUmxZbEV4Um1aSFVVY3lTVUpNYkdwM05GY3hVVWMyUTNWbVFrZGtWVmxXVFdadk5UVTBWWGszUkhnMGIxVnZiRXBsWkhWTmJtSnJaVUYxYldkbWVtTnBReXRyTXpNek1rTkpUM2d5ZUc5clQzRllUMHhCZEc5eWNtazJha1pUYW5WRldrTkNWekZYZWtGS1YwWmxNMmxuZDBGUE9IRk9kRWxvWVdsak1XMUVNRFU1Ym5KSE0zQkdTV28xT0dNMUwzUmlWMGh3U3pSV1FqQXhkRzl1YTFkaFpuRnZjR2xxWjBKdE9HcHFPR0ZzV0daTVIwWkZiUzl1YVRCUVN6QlNWRGx6TlRjNGNIUnBZMVk0ZDNaVllVeFVXRmR2TURsamNHZHdNR3BTVWxNclVISjFUVEl6VTJ4QmVuSkRWWHBoU3pNNFZrTnNWa2Q0UVhodWRUQnFMMUZZVkdac1pITlhaR1JNVWpsV2RXSklZa1F2ZGs5UVMzWk5aamhaV1hKT00yeFpRMnBIUm5wcGJXdEdWMU5HUW1WcWJFMUlkVkJzVkd3eVVXVndXR2xGZDJ0emFHMW1ZV1pRYkc1b1dGVXdWak50YXpkbFJrZEhRV1ZvVkVWc1pqaHFjRmxYZUVGT1JUUXZUR3cxYzNKWVVuTlhWbXcxVG5jdmRteGlVVkp4VTAwelMwaEhkamRoV25nek9ETnNORWt6ZGl0RFVXeFZRVVprVUZWaVZYSnRWRFpoVFhVeE9TdHdSalpNV2tSSVYwVm5NMDh5ZDFCclZHeG1TR05GS3pocU0xbHBibGxUY1d4VFFsUm1XbWhTU2xOMVlsQTRjVTlNYnpGcldUbDFaa3hPZG5GaE1FZElWV2swWjBKdGNXZGFSRXM1VDIxaU1GQXZXV0psWTFJNE1HWlRlaXQwT1dsNlpGRnVibVZ6Unl0Q05rVmlaWEkzU0hSUmFuWlVhblJJTDNnelRsQjRTRWREV214eVREa3dhMHBYWlcxb1pFRmlSbXhMVXpndlNHVXJTVTF1UmpORVZVUXlTbVJ2V1dSQk0xUnBiR0ZvYVhGc1VWSnNNemR2UFE9PQ--";
   //$l2="https://cloudnestra.com/prorcp/ODEyMDA0OGRiYTZiMzNjYjJjM2IzYTJlNzMyNTA3OWU6Vkc5V2FFdEZRVEJoV1RkdWRYWmlNakI2YjFGa1NVbzFSa0Z1Y2s0d1NVd3JTVFp5T0hoT1JtdE1RVFpXWVRabFpFd3dlbmN2Y0ZRMGFUTnFNMHB5YUdKMVQxTlVlVVk1YVZwMEswTkdhMkZYTDJ0bE9IWmhZV1JWWVVoU01VUXlaa3d2VTNsSWMydzNPV2hVU1ZwVGIzVXhUbkl3WlRGMGIwbFRVRnBNV1hSNk5UVm9VbHB0ZVU1MFFUWm5jRTVvVGxoWFRtaGthVkk1YmtkUlZIZEpUWFpuVG5GcVZVZE9jbmxaZG1OeUwyaFBOMGQ1TjI1dlIwVldiWEV6YzJGS1QzZHdOblJTTHpOWWJtMWljM2RvVW5aeGJURjJXazR4WnpCNWVqWkxlRTFqY0hGRmRFOUtkM0l5TVRZeVRESjFWVEZWWm5JclRrZEpWVkZuZEdSbVprUXdiSFl5ZW1keVJWaEZOVGQwUjFwbGJFZHhRVTR4UTB4Uk4xVnlURzlRV1d0dlltSkVkbkF4UlhGdGJrUlJkekowZGtaTmQxZE1aMlZhUm5GS1FtNVZjVGxJSzB3ME5rSmlLMkkxT0VjMmFDOUplRTl4Y1V0d1MxbFZVMEozVDJOdEwwa3daVFpuVDJFMldITjVNMUYwVFVSbmFVbHZURU5WTjBRd1VERXpkbTVvZHpWemRWZDBVRGRvTW5rNWIzUk1iRk0yUlROaU9XbE1XWGRxWm5vclpIVndjRFJxZDJwb1kxcHVjMlZWWmpkNWRqSndkM0ZTV1RVM01pOHhjbklyVjNjNWFURlhRa0kzZVZOamJWRnVaemxSWkZadU56TnpaV1pxY1VFdmFUSmtNM1pvWkhGcFltUlBTMFpxVFV4Q1JXTkZSM3AyTkRCS1kyNTRZelJDUTNsRGFDdDNWR2RCWW5RNFVsQjFTRzFFT0VvMFQxWkRRbkEzZDNkSlR6VnliRW95VTBkMVNWbEtNbGhoTnpoR04xUnVVM2xGZGxWMmQzcFROMVpHZEhCeFltUnRlVVJ5TVRFcmRUQTFkV3RPTkd4NFJFNVJNMHd4VVZwTmVtaDNhVk5JTm5oc09VSkpSQ3Q1TmtsSVNuVkNORlZwZG1FeFVrVm9WbVpJYzFaMVpGSTRlVWh3ZDNOcGFGRTJVMVZ2TkRac2VEaDZkVGRyU1hvMEsxZEhZek50VFhScVpVbGhWRFJyVGpVdlVVa3daV2MwT0hSdGJHUndiRE5GU0haWVVGSk5RV3RHVEZOcUswOW5TRXRpY0VwbmNGRmxaMjVVVUZCcGFDc3JkMHMxYjJ4UU0xSlViSGQxTkhCMVZqVk9aV1I2SzA5SVVqTkpSR3haSzNWd05FazJkbmx0VmxCTFIwaG9RemR0VDNGTGVqSmxOVGg2ZUN0a1VXcHNWblpWUjNGUGFFWTNhbHBqYkRVM1dGaHRVbVpuWkdWV1EyTkpVV2R3Y1RGSlNrZzVWbEJSVDFGaVdEQnVWM1pSWW14RVQzZEZaVEl6WjFKbVZGWkJaRlpPUjJadmJTdFBNMVIwUlhGNVYwTlVPRlJPWTNSTGJHTlpTbEpLZFM5TlIwMXJVVFZRYVhOMk1uVlhRamM1VVRSSGRHa3liMmhRYmpFd1R6aERWM3BtVkdwV2EwVndSeTlsWkhaTlZVWm9kVnBhYVdVeGRtNVVXVE5JT1U0clVqTTRTVzR3VjJKTlJWTTRUbEphY1hKSmJ5OTRUMjFPWldnelR6RkpjbGRYTmxaNmVrZE5TbUZ5VkUxVlRUUjFWMHhJY1U1R09VWjJZVXgyVjNOcVdVdzNNRGRIUlV0YVpVeGhaMEl3UVhaMldYSnlUSGgyWkZOYVZHWjRjSHBOVjNkMk1tUnNhRkZSU1RoVWFtdFNZVE5uTTFSaWJGWlRjVzl1Y0Zsb1IwZzRSMWs1TUdkUlJGbEphbVJxUWpaU1dHNU1NakkwZGpCTWVWQlFRMFIwWjBJclNqSkNkVWhxYm5OMFowZHVjelI2WVRSV00xSk1ZVFZ0V1d4blRUVkdjbFJHVlhCcFoxWlNXbk55ZUZvdmJYUlFkSFVyU0RGelEySjZXbFYxYmt3dlVWRldUR0ZsYzFOdVFsTjBRVzA0WldocE1FOUpVMGhMVEdsRk5VOVhhVEZEU201T1kxWlFVR2h0VUhWQ1RFY3lUbFpYZG01TGRsVlFTVll3ZFZGelZubDJNV1ZoTlhjMVdIUlZXRWhHUkdGU2VGWXJZbWxCVkVnNGVuazBjbmhLWjBzemQwbHpVMUpuVkZodWIwZFBUMFZyVDAxYWFHTXhaVVpWTjBWSFdHMTBVMHB5TkhSaVZFVTNhbFZQUmtSMWRtcHdOVVo2YzFocVJsTnZTREJhTkhkQ2RVbEZOM2syTlVaU05tNDNhbFpGVEVKMVltRXZkall4ZDFSeFFWbFlWWGwzYkVacE5VOXlaWFZSUlQwPQ--";
   $head=array('User-Agent: '.$ua,
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Referer: '.$l1,
   'Connection: keep-alive',
   'Upgrade-Insecure-Requests: 1',
   'Sec-Fetch-Dest: iframe',
   'Sec-Fetch-Mode: navigate',
   'Sec-Fetch-Site: same-origin');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();

  //$context  = stream_context_create($options);
  //$h = @file_get_contents($l2, false, $context);
  //echo $h;
  //echo '<a href="https://ttraian22.great-site.net/m.php?link='.$l2.'">xxxxx</a>';
echo '<table border="1" width="100%">';
if (preg_match("/file\:\s*\'([^\']+)\'/",$h,$m))
 echo '<TR><TD class="mp"><label id="server">'.parse_url($m[1])["host"].'</label>';
else
echo '<TR><TD class="mp"><label id="server">vidsrc.net</label>';
echo '<input type="hidden" id="file" value="'.$l.'"></td></TR></TABLE>';

echo '<table border="1" width="100%"><TR>';

if ($tip=="movie") {
  $tit3=$tit;
  $tit2="";
  $sez="";
  $ep="";
  $imdbid="";
  $from="";
  $link_page="";
} else {
  $tit3=$tit;
  $sez=$sez;
  $ep=$ep;
  $imdbid="";
  $from="";
  $link_page="";
}
$imdbid=str_replace("tt","",$imdb);
$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".urlencode(fix_t($tit3))."&link=".$link_page."&ep_tit=".urlencode(fix_t($tit2))."&year=".$year;
include ("subs.php");
echo '<table border="1" width="100%"><TR>';
if ($tip=="movie")
  $openlink=urlencode(fix_t($tit3));
else
  $openlink=urlencode(fix_t($tit.$tit2));

 if ($flash =="flash")
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink1('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
 else
   echo '<TD align="center" colspan="4"><a id="viz" onclick="'."openlink('".$openlink."')".'"'." style='cursor:pointer;'>".'VIZIONEAZA !</a></td>';
echo '</TR><TD align="center" colspan="4"><a href="'.$l1.'" target="_blank">CF</a></TD>';
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
//echo '<a href="https://streamembed.net/play/YTF0TklLYXplRnRhdjNTcHBUQUxnUzd1amt0UkIrZTJTWUZlQk8wYXJsWXhlT2EzTkxaQkU0RU9HQ2ZwemhvPQ==">sasaasas</a>';
include("../debug.html");
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
