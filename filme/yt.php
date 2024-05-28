<?php
//$file="https://www.youtube.com/watch?v=y5IdUV6KN_A";
//https://www.youtube.com/watch?v=aTKBowDjMQg
//echo youtube_nou($file);
function youtube_nou1($file) {
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {
  $r=array();
  $s=array();
  $out="";
  $id = $match[2];
  $file = "https://www.youtube.com/watch?v=".$id;
  $l="https://y2mate.is/analyze";
  $l="https://en.y2mate.so/analyze";
  $post= "url=".$file;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'Content-Length: '.strlen($post),
  'Origin: https://en.y2mate.is',
  'Connection: keep-alive',
  'Referer: https://en.y2mate.is/');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_HEADER, 1);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1)['formats']['video'];
  //print_r ($r);
  foreach ($r as $rr) {
   if ($rr['needConvert']==false)
    $s[$rr['formatId']]=$rr['url'];
  }
  if (isset($s['37']))
   $out=$s['37'];
  elseif (isset($s['22']))
   $out=$s['22'];
  elseif (isset($s['18']))
   $out=$s['18'];
  else
   $out="";
  return $out;
}
}
//$l="https://www.youtube.com/watch?v=td8B1YlH6Uk";
//$h=youtube_nou2($l);
//echo $h;
function youtube_nou($file) {
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {

  $id = $match[2];
  $file = "https://www.youtube.com/watch?v=".$id;
//echo $file."\n";
//die();
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
function youtube_nou11($file) {
 if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {

  $id = $match[2];
  $file = "https://www.youtube.com/watch?v=".$id;
  $l="https://srvcdn2.2convert.me/api/json?url=".$file;
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Origin: https://en.y2mate.is',
  'X-CSRF-TOKEN: ',
  'X-Requested-With: XMLHttpRequest',
  'Referer: https://en.y2mate.is/');
  $post="";
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $l);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
     curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
     curl_setopt($ch, CURLOPT_POST,1);
     curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
     curl_setopt($ch, CURLOPT_TIMEOUT, 25);
     $h = curl_exec($ch);
     curl_close($ch);
     //echo $h;
     $x=json_decode($h,1);
     //print_r ($x);
     $vid=$x['formats']['video'];
     $v=array();
     $c=array();
     for ($k=0;$k<count($vid);$k++) {
       if (preg_match("/18|22|37/",$vid[$k]['formatId']) && !$vid[$k]['needConvert']) {
        $v[$vid[$k]['formatId']]=array($vid[$k]['url'],$vid[$k]['needConvert']);
       }
       if (preg_match("/18|22|37/",$vid[$k]['formatId']) && $vid[$k]['needConvert']) {
        $c[$vid[$k]['formatId']]=array($vid[$k]['url'],$vid[$k]['needConvert']);
       }
     }
     //print_r ($v);
     //print_r ($c);
     $out="";
     if (isset($v['37']))
       $out=$v['37'][0];
     elseif (isset($v['137']))
       $out=$v['137'][0];
     elseif (isset($v['22']))
       $out=$v['22'][0];
     elseif (isset($v['18']))
       $out=$v['18'][0];
     if ($out)
      return $out;
 } else
  return "";
}
?>
