<?php
//$file="https://www.youtube.com/watch?v=y5IdUV6KN_A";
//echo youtube_nou($file);
function youtube_nou($file) {
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {

  $id = $match[2];
  $file = "https://www.youtube.com/watch?v=".$id;
//echo $file."\n";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:93.0) Gecko/20100101 Firefox/93.0";
$l="https://yt1s.com/api/ajaxSearch/index";
$post="q=".$file."&vt=home";
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://yt1s.com',
'Connection: keep-alive',
'Referer: https://yt1s.com/en26');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  //print_r ($x);
$vid=$x['vid'];
$k="";
//die();
$qa=array ("auto","37","22","18");
$l="https://yt1s.com/api/ajaxConvert/convert";
for ($z=0;$z <count($x['links']['mp4']);$z++) {
 for ($y=0;$y<count($qa);$y++) {
 if (isset ($x['links']['mp4'][$qa[$y]])) {
   $k=$x['links']['mp4'][$qa[$y]]['k'];
   break;
 }
 }
}
//echo $post;
//$k=$x['links']['mp4']['auto']['k'];
$v=array(
'vid' => $vid,
'k' => $k);
$post=http_build_query($v);

//sleep(3);
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: https://yt1s.com',
'Connection: keep-alive',
'Referer: https://yt1s.com/en26');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($h,1);
  //print_r ($x);
  if (isset($x['dlink']))
   return $x['dlink'];
  else
   return "";

} else
  return "";
}
?>
