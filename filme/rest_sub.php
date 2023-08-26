<?php
include ("../common.php");
error_reporting(0);
$sub=$_POST["id"];
$srt_name="sub_extern.srt";

 $key=base64_decode("dHJhaWxlcnMudG8tVUE=");
$head=array('X-Requested-With: XMLHttpRequest',
"X-User-Agent: ".$key);
//$head=array
  $ua="Mozilla/5.0 (Windows NT 10.0; rv:98.0) Gecko/20100101 Firefox/98.0";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  curl_setopt($ch, CURLOPT_URL, $sub);
  $h_sub = curl_exec($ch);
  curl_close ($ch);
  $h = gzdecode($h_sub);
  //$h=mb_convert_encoding($h,'UTF-8', 'ISO-8859-2');
  if ($h) {
   $new_file = $base_sub.$srt_name;
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h, strlen($h));
   fclose($fh);
   echo "Am salvat subtitrarea!";
  } else {
   echo 'Nu am putut salva subtitrarea!';
  }
?>
