<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["link"];
$title=$_POST["title"];
$image="";
$file=$base_fav."playlist.dat";
$arr=array();
$h="";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1) -1;$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $tit=trim($a[0]);
      $l=trim($a[1]);
      $img=trim($a[2]);
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$l]["title"]=$tit;
      $arr[$l]["image"]=$img;
      //$arr[]=array($tit,$l,$img);
    }
  }
}
//asort($arr);
//print_r ($arr);
//die();
if ($mod=="add") {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($link == $key) {
      $found=true;
      break;
    }
  }
  if (!$found) {
    $arr[$link]["title"]=$title;
    $arr[$link]["image"]=$image;
    echo "Am adaugat link-ul ".unfix_t(urldecode($title));
  }
  asort($arr);
  } else {
    $arr[$link]["title"]=$title;
    $arr[$link]["image"]=$image;
    echo "Am adaugat link-ul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$arr[$key]["title"]."#separator".$key."#separator".$arr[$key]["image"]."\r\n";
  }
  //echo $out;
  if ($found) echo "link-ul a fost adaugat deja!";
  file_put_contents($file,$out);
} else {
  $found=false;
  //echo $title;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($link == $key) {
      $found=true;
      //echo $title;
      unset ($arr[$key]);
      echo "Am sters link-ul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    asort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$arr[$key]["title"]."#separator".$key."#separator".$arr[$key]["image"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
if ($arr) {
  $out="";
  foreach($arr as $key => $value) {
   $out =$out."#EXTINF:-1,".$arr[$key]["title"]."\n".$key."\n";
  }
  
  file_put_contents("pl/playlist_fav.m3u",$out);
}
?>
