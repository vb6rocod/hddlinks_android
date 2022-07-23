<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$id_sub=$_GET["id"];
$title=unfix_t(urldecode($_GET["title"]));
if (isset($_GET["page_tit"]))
 $page_tit=unfix_t(urldecode($_GET["page_tit"]));
else
 $page_tit="";
//if (file_exists($base_sub."sub.zip")) unlink($base_sub."sub.zip");
//if (file_exists($base_sub."sub.rar")) unlink($base_sub."sub.rar");
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
// create the XMLHttpRequest object, according browser
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
function changeserver(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = "id="+ link;
  var php_file="sub.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       alert (request.responseText);
       //document.getElementById("mytest1").href=request.responseText;
      //document.getElementById("mytest1").click();
      history.back();
    }
  }
}
</script>
</head>
<body><div id="mainnav">
<H2></H2>
<?php


function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/msi',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/msi',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
function fix_srt($contents) {
$n=1;
$output="";
$bstart=false;
$file_array=explode("\n",$contents);
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line) && !$bstart)
        {
          $output .= $n;
          $output .= PHP_EOL;
          $output .= $line.PHP_EOL;
          $bstart=true;
        } elseif($line != '' && $bstart) {
          $output .= $line.PHP_EOL;
          //$n++;
        } elseif ($line == '' && $bstart) {
          $output .= $line.PHP_EOL;
          $bstart=false;
          $n++;
        }
  }
return $output;
}
    function split_vtt($contents)
    {
        $lines = explode("\n", $contents);
        if (count($lines) === 1) {
            $lines = explode("\r\n", $contents);
            if (count($lines) === 1) {
                $lines = explode("\r", $contents);
            }
        }
        return $lines;
    }
function prepare_sub($h) {
 if (function_exists("mb_convert_encoding")) {
    $enc=mb_detect_encoding($h);
 if (mb_detect_encoding($h, 'UTF-8', true)== false) $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
    /*
    $h = str_replace("ª","Ş",$h);
    $h = str_replace("º","ş",$h);
    $h = str_replace("Þ","Ţ",$h);
    $h = str_replace("þ","ţ",$h);
	$h = str_replace("ã","ă",$h);
	//$h = str_replace("Ã","Ă",$h);

    $h = str_replace("Å£","ţ",$h);
    $h = str_replace("Å¢","Ţ",$h);
    $h = str_replace("Å","ş",$h);
	$h = str_replace("Ă®","î",$h);
	$h = str_replace("Ă¢","â",$h);
	$h = str_replace("Ă","Î",$h);
	//$h = str_replace("Ã","Â",$h);
	$h = str_replace("Ä","ă",$h);
	*/
} else {
    $h = str_replace("�","S",$h);
    $h = str_replace("�","s",$h);
    $h = str_replace("�","T",$h);
    $h = str_replace("�","t",$h);
    $h=str_replace("�","a",$h);
	$h=str_replace("�","a",$h);
	$h=str_replace("�","i",$h);
	$h=str_replace("�","A",$h);
}

if (strpos($h,"WEBVTT") !== false) {
  //convert to srt;

    function convert_vtt($contents)
    {
        $lines = split_vtt($contents);
        array_shift($lines); // removes the WEBVTT header
        $output = '';
        $i = 0;
        foreach ($lines as $line) {
            /*
             * at last version subtitle numbers are not working
             * as you can see that way is trustful than older
             *
             *
             * */
            $pattern1 = '#(\d{2}):(\d{2}):(\d{2})\.(\d{3})#'; // '01:52:52.554'
            $pattern2 = '#(\d{2}):(\d{2})\.(\d{3})#'; // '00:08.301'
            $m1 = preg_match($pattern1, $line);
            if (is_numeric($m1) && $m1 > 0) {
                $i++;
                $output .= $i;
                $output .= PHP_EOL;
                $line = preg_replace($pattern1, '$1:$2:$3,$4' , $line);
            }
            else {
                $m2 = preg_match($pattern2, $line);
                if (is_numeric($m2) && $m2 > 0) {
                    $i++;
                    $output .= $i;
                    $output .= PHP_EOL;
                    $line = preg_replace($pattern2, '00:$1:$2,$3', $line);
                }
            }
            $output .= $line . PHP_EOL;
        }
        return $output;
    }
    $h=convert_vtt($h);
}

//echo $h;
   $h=fix_srt($h);
   //echo $h1;
   return $h;
}
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$l="https://www.titrari.ro/get.php?id=".$id_sub;
$l="https://www.xn--titrri-l0a.ro/get.php?id=".$id_sub;
//echo $l;
//$l="https://www.titrari.ro/get.php?id=114012";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://www.titrari.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $response=curl_exec($ch);
  curl_close($ch);
  //echo $response;
  $t1=explode('filename="',$response);
  $t2=explode('"',$t1[1]);
  $filename=$t2[0];
  $ext = substr(strrchr($filename, '.'), 1);
if (preg_match("/(srt|txt|vtt)/i",$ext)) {
   $file_srt=$base_sub."sub_extern.srt";
   //$fp = fopen($file_srt, 'w');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch,CURLOPT_REFERER,"https://www.titrari.ro");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   //curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h1=curl_exec($ch);
   curl_close($ch);
   //fclose($fp);
   if ($h1) {
   $h1=prepare_sub($h1);
   $fh = fopen($file_srt, 'w');
   fwrite($fh, $h1);
   fclose($fh);
   echo "Am salvat subtitrarea";
   } else
   echo "Nu am putut salva subtitrarea!";
} else if (preg_match("/(sub)/i",$ext)) {
   $file_srt=$base_sub."sub_extern.srt";
   //$fp = fopen($file_srt, 'w');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch,CURLOPT_REFERER,"https://www.titrari.ro");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   //curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h1=curl_exec($ch);
   curl_close($ch);
   //fclose($fp);
   if ($h1) {
   //$h1=prepare_sub($h1);
   $fh = fopen($file_srt, 'w');
   fwrite($fh, $h1);
   fclose($fh);
   echo "Am salvat subtitrarea";
   } else
   echo "Nu am putut salva subtitrarea!";
} else if (preg_match("/zip/i",$ext)) {
   $file_srt=$base_sub."sub.zip";

   $fp = fopen($file_srt, 'w');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch,CURLOPT_REFERER,"https://www.titrari.ro");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   curl_exec($ch);
   curl_close($ch);
   fclose($fp);
   //echo $file_srt;

$zip = new ZipArchive;
if ($zip->open($file_srt) === true) {
    for($i = 0; $i < $zip->numFiles; $i++) {
$sub= mb_convert_encoding(
    htmlentities(
        $zip->getNameIndex($i),
        ENT_COMPAT,
        "UTF-8"
    ),
    "HTML-ENTITIES",
    "UTF-8"
);
      //$sub=$zip->getNameIndex($i);
      if (preg_match('/(\.srt|\.txt|\.sub)/i', basename($sub))) {
        //$zip->extractTo('path/to/extraction/', array($zip->getNameIndex($i)));
        $arrsub[]=$sub;
        // here you can run a custom function for the particular extracted file

    }
}
$zip->close();
}
$nn=count($arrsub);
$k=intval($nn/10) + 1;
$n=0;
echo '<h2>'.$page_tit.'</h2>';
echo '<table border="1" width="100%">';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '<TD></TD></TR>';
foreach ($arrsub as $key => $val) {

  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".urlencode($val)."&mod=zip"."'".');return false;">'.$val.'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table><BR>';
} else if (preg_match("/rar/i",$ext)) {
   $file_srt=$base_sub."sub.rar";

   $fp = fopen($file_srt, 'w');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HEADER, false);
   curl_setopt($ch,CURLOPT_REFERER,"https://www.titrari.ro");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   curl_exec($ch);
   curl_close($ch);
   fclose($fp);

$rar_arch = rar_open($file_srt);
if ($rar_arch === FALSE)
    die("Could not open RAR archive.");

$rar_entries = rar_list($rar_arch);

if ($rar_entries === FALSE)
    die("Could retrieve entries.");

foreach ($rar_entries as $e) {
$sub= mb_convert_encoding(
    htmlentities(
        $e->getName(),
        ENT_COMPAT,
        "UTF-8"
    ),
    "HTML-ENTITIES",
    "UTF-8"
);
if (preg_match('/(\.srt|\.txt|\.sub)/i', basename($sub))) {
if (strpos($base_sub,":") !== false) $sub=str_replace("\\","/",$sub);
$arrsub[] = $sub;
}
}
rar_close($rar_arch);
//print_r ($rar_entries);
//print_r ($arrsub);
$nn=count($arrsub);
$k=intval($nn/10) + 1;
$n=0;
echo '<H2>'.$page_tit.'</H2>';
echo '<table border="1" width="100%">';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '<TD></TD></TR>';
foreach ($arrsub as $key => $val) {

  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".urlencode($val)."&mod=rar"."'".');return false;">'.$val.'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table><BR>';
} else {
  //unsup....
  echo' (nesuportat) '.$filename.'</title>';
}
?>
</div></body>
</html>
