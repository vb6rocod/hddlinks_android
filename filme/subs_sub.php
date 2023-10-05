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
if (isset($_GET['cc']))
 $cc=$_GET['cc'];
else
 $cc="1";
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
      history.go(-2);
    }
  }
}
</script>
</head>
<body>
<?php
echo '<h2>'.$page_tit.'</h2>';
$arrsub=array();
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/ms',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/msi',"chr(0x\\1)",$text);  #hex notation
    return $text;
}

$hh="";
$sub_extern="sub_extern.srt";
$ext="";
$file_save=$base_sub."sub.sub";
$file_srt=$base_sub."sub_extern.srt";
$file_zip=$base_sub."sub.zip";
$file_rar=$base_sub."sub.rar";
if (file_exists($file_save)) unlink ($file_save);
function header_callback($ch, $header_line)
{
    global $hh;
    $hh .= $header_line;
    return strlen($header_line);
}
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$l="https://subs.ro/subtitrare/descarca/".$id_sub;
//echo $l;
//$l="https://www.titrari.ro/get.php?id=114012";

   $fp = fopen($file_save, 'w');
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch,CURLOPT_REFERER,"https://subs.ro");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_FILE, $fp);
   //curl_setopt($ch, CURLOPT_WRITEHEADER, $file_header);
   curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'header_callback');
   curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   curl_exec($ch);
   curl_close($ch);
   fclose($fp);

   if (preg_match("/filename\=(.+)/",$hh,$m)) {
    $filename=trim($m[1]);
    $ext = substr(strrchr($filename, '.'), 1);
   } else {
    echo "Server error!";
    if ($cc=="1")
    echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
    else
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
    die();
   }

if (preg_match("/(srt|txt|vtt|sub)/i",$ext)) {
   rename($file_save,$file_srt);
} else if (preg_match("/zip/i",$ext)) {
 rename($file_save,$file_zip);
 $zip = new ZipArchive;
 if ($zip->open($file_zip) === true) {
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
   //echo $sub;
   if (preg_match("/\.(srt|txt|vtt|sub)/i",$sub)) $arrsub[]=$sub;
   }
  $zip->close();
 } else {
  echo "Nu am putut deschide arhiva zip!";
  if ($cc=="1")
  echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
  else
  echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
  die();
 }
 $nn=count($arrsub);
 $k=intval($nn/10) + 1;
 $n=0;
 echo '<table border="1" width="100%">';
 echo '<TR>';
 for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
 }
 echo '<TD></TD></TR>';
 foreach ($arrsub as $key => $val) {
  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".urlencode($val)."&mod=zip"."'".');return false;">'.$val.'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
 }
 echo '</table><BR>';
} else if (preg_match("/rar/i",$ext)) {
 if (function_exists("rar_open")) {
 rename($file_save,$file_rar);
 $rar_arch = rar_open($file_rar);
 if ($rar_arch === FALSE) {
    echo "Could not open RAR archive.";
    if ($cc=="1")
    echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
    else
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
    die();
 }
 $rar_entries = rar_list($rar_arch);
 if ($rar_entries === FALSE) {
    echo "Could retrieve entries.";
    if ($cc=="1")
    echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
    else
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
    die();
 }
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
  if (strpos($base_sub,":") !== false) $sub=str_replace("\\","/",$sub);
  if (preg_match("/\.(srt|txt|vtt|sub)/i",$sub)) $arrsub[]=$sub;
 }
 rar_close($rar_arch);
 } else {
    echo "Missing RAR module!.";
    if ($cc=="1")
    echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
    else
    echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
    die();
 }
 $nn=count($arrsub);
 $k=intval($nn/10) + 1;
 $n=0;
 echo '<table border="1" width="100%">';
 echo '<TR>';
 for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
 }
 echo '<TD></TD></TR>';
 foreach ($arrsub as $key => $val) {
  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".urlencode($val)."&mod=rar"."'".');return false;">'.$val.'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
 }
 echo '</table><BR>';
} else {
  //unsup....
  echo' (nesuportat) '.$filename.'</title>';
  if ($cc=="1")
  echo '<script>setTimeout(function(){ history.go(-2); }, 1500);</script>';
  else
  echo '<script>setTimeout(function(){ history.go(-1); }, 1500);</script>';
}
?>
</body>
</html>
